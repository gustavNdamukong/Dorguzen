<?php 

namespace Dorguzen\Core\Database\Drivers;

use PDO;
use PDOException;
use Exception;


class DGZ_PDODriver implements DGZ_DBDriverInterface 
{
    private PDO $conn;

    private int $affectedRows = 0;


    public function __construct(array $credentials)
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$credentials['host']};dbname={$credentials['db']};charset=utf8mb4",
                $credentials['username'],
                $credentials['pwd'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            throw new Exception('PDO Connection failed: ' . $e->getMessage());
        }
    }


    public function getTableSchema(string $table): array
    {
        /*$stmt = $this->conn->prepare('DESCRIBE ' . $table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);*/

        $stmt = $this->conn->prepare('DESCRIBE ' . $table);
        $stmt->execute();

        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($columns)) {
            dgzie("Table '{$table}' does not exist.");
        }

        $schema = [];

        foreach ($columns as $column) {
            $type = strtolower($column['Type']);
            $bind = 's';

            if (str_contains($type, 'int')) {
                $bind = 'i';
            } elseif (str_contains($type, 'decimal') || str_contains($type, 'float')) {
                $bind = 'd';
            }

            $schema[$column['Field']] = $bind;
        }

        return $schema;
    }


    public function connect()
    {
        return $this->conn;
    }


    public function prepare($query)
    {
        return $this->conn->prepare($query);
    }


    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute($params);

        $this->affectedRows = $stmt->rowCount();

        return $result;
    }

    public function fetchAll($result): array
    {
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function numRows($result): int
    {
        return $result->rowCount();
    }


    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }


    public function lastInsertId(): int|string
    {
        return $this->conn->lastInsertId();
    }


    public function getPrimaryKeyField(string $table): ?string
    {
        $conn = $this->connect();
        $stmt = $conn->query("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['Column_name'] : null;
    }


    /**
     * Builds the query strings from the data (e.g. arrays) given
     *
     */
    public function prepareInsertOrUpdate(array $data, array $passwordFields, string $type = 'insert'): array
    {
        $fields = '';
        $placeholders = '';
        $values = [];

        foreach ( $data as $field => $value )
        {
            if ($field == 'key')
            {
                $values[] = $value;
                continue;
            }

            $fields .= "{$field},";
            $values[] = $value;

            if ( $type == 'update')
            {
                if (in_array($field, $passwordFields))
                {
                    $placeholders .= $field ." = AES_ENCRYPT(?, ?),";
                }
                else {
                    $placeholders .= $field . '=?,';
                }
            }
            else if (in_array($field, $passwordFields))
            {
                $placeholders .= " AES_ENCRYPT(?, ?),";
            }
            else
            {
                $placeholders .= '?,';
            }
        }

        // Normalize $fields and $placeholders for inserting
        $fields = rtrim($fields, ',');
        $placeholders = rtrim($placeholders, ',');

        return array( $fields, $placeholders, $values );
    }


    public function encryptPasswordCondition(string $field): string
    {
        return "{$field} = AES_ENCRYPT(?, ?)";
    }
}