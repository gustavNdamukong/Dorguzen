<?php 

namespace Dorguzen\Core\Database\Drivers;

use mysqli;
use Exception; 

class DGZ_MySQLiDriver implements DGZ_DBDriverInterface 
{
    private mysqli $conn;

    private int $affectedRows = 0;

    public function __construct(array $credentials)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->conn = new mysqli(
                $credentials['host'],
                $credentials['username'],
                $credentials['pwd'],
                $credentials['db']
            );
        } catch (\mysqli_sql_exception $e) {
            throw new Exception('MySQLi connection failed: ' . $e->getMessage());
        }

        if ($this->conn->connect_error) {
            throw new Exception('MySQLi Connection failed: ' . $this->conn->connect_error);
        }
    }


   public function getTableSchema(string $table): array
    {
        /*$sql = 'DESCRIBE ' . $table;
        return $this->query($sql);*/

        $columns = $this->query("DESCRIBE {$table}");

        $schema = [];

        foreach ($columns as $column) {
            $type = strtolower($column['Type']);
            $bind = 's';

            if (str_contains($type, 'int')) $bind = 'i';
            elseif (str_contains($type, 'decimal') || str_contains($type, 'float')) $bind = 'd';

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
        if ($stmt === false) throw new Exception($this->conn->error);

        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }


    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) throw new Exception($this->conn->error);

        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();

        $this->affectedRows = $stmt->affected_rows;

        $stmt->close();
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


    public function affected_rows(): int
    {
        return $this->conn->affected_rows;
    }

    public function lastInsertId(): int|string
    {
        return $this->conn->insert_id;
    }


    public function getPrimaryKeyField(string $table): ?string
    {
        $conn = $this->connect();
        $result = $conn->query("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
        if ($result && $row = $result->fetch_assoc()) {
            return $row['Column_name'];
        }
        return null;
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