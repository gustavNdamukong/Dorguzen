<?php 

namespace Dorguzen\Core\Database\Drivers;

use PDO;
use Exception;
use PDOException; 

/**
 * DGZ_SQLiteDriver is the SQLite DB driver for Dorguzen.
 * Dorguzen implements SQLite as a PDO-based driver,
 * not as a brand-new connection technology. This is because:
 * 
 *  -SQLite is natively supported by PDO (pdo_sqlite)
 *  -The DGZ abstraction already supports PDO 
 */
class DGZ_SQLiteDriver implements DGZ_DBDriverInterface 
{
    private PDO $pdo;

    // used to track affected rows during queries, since SQLite does not support it.
    private int $affectedRows = 0;

    public function __construct(array $credentials)
    {
        if (empty($credentials['sqlite_path'])) {
            dgzie('SQLite database path is required in env set up.');
        }

        $path = $credentials['sqlite_path'];

        if (!str_starts_with($path, '/')) {
            // resolve project root
            $path = base_path($path); 
        }

        $dsn = 'sqlite:' . $path;

        try {
            $this->pdo = new PDO($dsn);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            dgzie('SQLite connection failed: ', $e->getMessage());
        }
    }


    public function getTableSchema(string $table): array
    {
        $stmt = $this->pdo->query("PRAGMA table_info($table)");
        $columns = $stmt->fetchAll();

        if (empty($columns)) {
            dgzie("Table '{$table}' does not exist.");
        }

        $schema = [];

        foreach ($columns as $column) {
            $type = strtolower($column['type']);
            $bind = 's';

            if (str_contains($type, 'int')) $bind = 'i';
            elseif (str_contains($type, 'real') || str_contains($type, 'float')) $bind = 'd';

            $schema[$column['name']] = $bind;
        }

        return $schema;
    }


    public function connect()
    {
        return $this->pdo;
    }

    public function prepare($query)
    {
        return $this->pdo->prepare($query);
    }

    /**
     * query() mirrors that of the PDO driver almost exactly
     * @param string $sql
     * @param array $params
     * @throws \Exception
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * execute() mirrors tthe execute() of the PDO driver almost exactly.
     * @param string $sql
     * @param array $params
     * @throws Exception
     * @return bool
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute($params);

        $this->affectedRows = $stmt->rowCount();

        return $success;
    }

    public function fetchAll($result): array
    {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function numRows($result): int
    {
        return $result->num_rows ?? 0;
    }


    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }


    /**
     * While MySQLi has affected_rows on the connection object;
     * and PDO exposes it on the statement, 
     * SQLite doesn’t reliably support affected.
     * The cleanest way to achieve this in SQLite is to track affected 
     * rows manually in the driver. We use the $affectedRows property for that.
     * Basically, we achieve this by storing affected rows data from the PDO 
     * statement's rowCount() (PDOStatement::rowCount()) into the $affectedRows 
     * property. See an example in action in execute().
     * @return int
     */
    /*public function affected_rows(): int
    {
        return $this->affectedRows;
    }*/

    public function lastInsertId(): int|string
    {
        return $this->pdo->lastInsertId();
    }


    public function getPrimaryKeyField(string $table): ?string
    {
        $stmt = $this->pdo->query("PRAGMA table_info($table)");
        $columns = $stmt->fetchAll();

        foreach ($columns as $column) {
            if ((int)$column['pk'] === 1) {
                return $column['name'];
            }
        }

        return null;
    }


    /**
     * Builds the query strings from the data (e.g. arrays) given
     *
     */
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
                // For UPDATE: field=?
                $placeholders[] = "{$field}=?";
            } else {
                // For INSERT: ?
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
}