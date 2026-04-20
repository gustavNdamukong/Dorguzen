<?php 

namespace Dorguzen\Core;

use Exception;
use Dorguzen\Core\Database\Drivers\DGZ_DBDriverInterface;


class DGZ_DBAdapter 
{
    private DGZ_DBDriverInterface $driver;

    public function __construct(DGZ_DBDriverInterface $driver)
    {
        $this->driver = $driver;
    }


    public function getTableSchema($table)
    {
        return $this->driver->getTableSchema($table);
    }

    public function listTables(): array
    {
        return $this->driver->listTables();
    }

    public function autoIncrementPrimaryKey(): string
    {
        return $this->driver->autoIncrementPrimaryKey();
    }

    public function getDriverName(): string
    {
        return $this->driver->getDriverName();
    }


    public function insert(string $table, array $data): bool
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Insert data cannot be empty.');
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(fn ($c) => "`$c`", $columns)),
            implode(', ', $placeholders)
        );

        return $this->execute($sql, array_values($data));
    }




    /**
     * Delete records from a table using simple equality conditions.
     *
     * Example:
     *  $db->delete('scheduled_task_locks', [
     *      'task_key' => 'emails:send'
     *  ]);
     *
     * Design goals:
     *  - Simple API (table + where array)
     *  - Safe (prepared statements)
     *  - Consistent with insert()
     *  - No business logic here
     */
    public function delete(string $table, array $where): bool
    {
        // A DELETE without WHERE is almost always a bug.
        // We explicitly forbid it to protect data.
        if (empty($where)) {
            throw new \InvalidArgumentException("Specify the fields on {$table} you wish to delete based on.");
        }

        /*
        * Build WHERE clause like:
        *   task_key = ? AND expires_at = ?
        */
        $conditions = [];

        foreach (array_keys($where) as $column) {
            $conditions[] = sprintf('`%s` = ?', $column);
        }

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s',
            $table,
            implode(' AND ', $conditions)
        );

        return $this->execute($sql, array_values($where));
    }



    /**
     * Runs a SELECT-like query and returns rows (MySQLi style).
     * Recommended use: ONLY for SELECT queries. It returns an array.
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        return $this->driver->query($sql, $params);
    }


    /**
     * Runs an INSERT/UPDATE/DELETE-like query and returns success boolean.
     * Recommended use: for INSERT / UPDATE / & DELETE queries ONLY. It returns a boolean.
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function execute(string $sql, array $params = []): bool
    {
        return $this->driver->execute($sql, $params);
    }
    

    /**
     * Returns the last inserted ID.
     */
    public function insert_id(): int|string
    {
        return $this->driver->lastInsertId();
    }

    // for ergonomics
    public function lastInsertId(): int|string 
    {
        return $this->driver->lastInsertId();
    }


    public function getAffectedRows(): int
    {
        return $this->driver->getAffectedRows();
    }


    /**
     * Compatibility for $conn->real_escape_string() in MySQLi.
     * However there's no need to escape string if prepared statements are being used.
     * The methods in this class use prepared statements, so no need for this method, unless 
     * its to cover for backwards compatibility.
     */
    public function escape(string $value): string
    {
        $conn = $this->driver->connect();
        if ($conn instanceof \mysqli) {
            return $conn->real_escape_string($value);
        }
        // For PDO
        return substr($conn->quote($value), 1, -1);
    }


    /**
     * Fetches the primary key field name of a given table, if available.
     */
    public function getPrimaryKeyField(string $table): ?string
    {
        return $this->driver->getPrimaryKeyField($table);
    }


    public function prepareInsertOrUpdate(array $data, array $passwordFields, string $type = 'insert'): array
    {
        return $this->driver->prepareInsertOrUpdate($data, $passwordFields, $type);
    }

    public function encryptPasswordCondition(string $passwordField): string
    {
        return $this->driver->encryptPasswordCondition($passwordField);
    }
}