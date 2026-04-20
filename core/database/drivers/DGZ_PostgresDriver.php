<?php 

namespace Dorguzen\Core\Database\Drivers;

use PDO;
use PDOException;

class DGZ_PostgresDriver implements DGZ_DBDriverInterface
{
    private PDO $pdo;

    private int|string|null $lastInsertId = null;


    private int $affectedRows = 0;


    public function __construct(array $credentials)
    {
        foreach (['host', 'db', 'username', 'pwd'] as $key) {
            if (!array_key_exists($key, $credentials)) {
                throw new \InvalidArgumentException("Postgres credential '{$key}' is missing.");
            }
        }

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $credentials['host'],
            $credentials['port'] ?? 5432,
            $credentials['db']
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $credentials['username'],
                $credentials['pwd'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Postgres connection failed: ' . $e->getMessage());
        }
    }


    public function connect(): PDO
    {
        return $this->pdo;
    }


    public function prepare($query)
    {
        return $this->pdo->prepare($query);
    }


    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


    public function execute(string $sql, array $params = []): bool
    {
        $isInsert = preg_match('/^\s*insert\s+/i', $sql);

        if ($isInsert && !str_contains(strtolower($sql), 'returning')) {
            $table = $this->extractTableFromInsert($sql);

            if ($table) {
                $pk = $this->getPrimaryKeyField($table);
                if ($pk) {
                    $sql .= " RETURNING {$pk}";
                }
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $this->affectedRows = $stmt->rowCount();

        // Capture returned ID if present
        // Postgres requires sequence name OR RETURNING id
        // so we capture it, then store the id on the $this->lastInsertId property
        if ($isInsert && str_contains(strtolower($sql), 'returning')) {
            $row = $stmt->fetch();
            $this->lastInsertId = array_values($row)[0] ?? null;
            return true;
        }

        return true;
    }


    public function fetchAll($result): array
    {
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * In PDO, numRows is only reliable for for the following queries:
     *  -DELETE
     *  -UPDATE 
     *  -INSERT
     * 
     * But not for:
     * 
     *  -SELECT 
     * 
     * Hence, numRows is ONLY intended for mutation queries or 
     * driver-supported SELECTs.
     * 
     * @param mixed $result
     * @return int
     */
    public function numRows($result): int
    {
        return $result->rowCount();
    }


    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }


    public function lastInsertId():int|string|null
    {
        return $this->lastInsertId;
    }


    private function extractTableFromInsert(string $sql): ?string
    {
        if (preg_match('/insert\s+into\s+["`]?(\w+)["`]?\s*/i', $sql, $matches)) {
            return $matches[1];
        }
        return null;
    }


    public function getTableSchema(string $table): array
    {
        $sql = "
            SELECT column_name AS \"Field\",
                   data_type   AS \"Type\"
            FROM information_schema.columns
            WHERE table_name = :table
            AND table_schema = 'public'
            ORDER BY ordinal_position
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['table' => $table]);

        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($columns)) {
            dgzie("Table '{$table}' does not exist in public schema.");
        }

        $schema = [];

        foreach ($columns as $column) {
            $type = strtolower($column['Type']);
            $bind = 's';

            if (str_contains($type, 'int')) {
                $bind = 'i';
            } elseif (
                str_contains($type, 'numeric') ||
                str_contains($type, 'double') ||
                str_contains($type, 'real')
            ) {
                $bind = 'd';
            }

            $schema[$column['Field']] = $bind;
        }

        return $schema;
    }

    public function getPrimaryKeyField(string $table): ?string
    {
        $sql = "
            SELECT a.attname
            FROM pg_index i
            JOIN pg_attribute a
              ON a.attrelid = i.indrelid
             AND a.attnum = ANY(i.indkey)
            WHERE i.indrelid = :table::regclass
              AND i.indisprimary
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['table' => $table]);

        $row = $stmt->fetch();
        return $row['attname'] ?? null;
    }

    public function prepareInsertOrUpdate(array $data, array $passwordFields, string $type = 'insert'): array
    {
        $fields = [];
        $placeholders = [];
        $values = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $passwordFields, true)) {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }

            $fields[] = $field;
            $values[] = $value;

            if ($type === 'update') {
                // UPDATE mode → field=?
                $placeholders[] = "{$field}=?";
            } else {
                // INSERT mode → ?
                $placeholders[] = '?';
            }
        }

        return [
            implode(',', $fields),
            implode(',', $placeholders),
            $values
        ];
    }


    public function encryptPasswordCondition(string $passwordField): string
    {
        return $passwordField;
    }

    public function listTables(): array
    {
        $stmt = $this->pdo->query(
            "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
        );
        return array_column($stmt->fetchAll(), 'tablename');
    }

    public function autoIncrementPrimaryKey(): string
    {
        return 'SERIAL PRIMARY KEY';
    }

    public function getDriverName(): string
    {
        return 'postgres';
    }
}