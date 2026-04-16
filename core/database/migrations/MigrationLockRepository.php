<?php

namespace Dorguzen\Core\Database\Migrations;

use Dorguzen\Core\DGZ_DBAdapter;
use RuntimeException;

class MigrationLockRepository
{
    protected DGZ_DBAdapter $db;

    protected string $table = 'dgz_migration_locks';

    public function __construct(DGZ_DBAdapter $db)
    {
        $this->db = $db;
    }


    public function ensureTableExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT PRIMARY KEY,
                locked_at TIMESTAMP NULL
            )
        ";

        $this->db->execute($sql);

        // Ensure a single lock row exists
        $this->db->execute(
            "INSERT IGNORE INTO {$this->table} (id, locked_at) VALUES (1, NULL)"
        );
    }


    public function acquire(): void
    {
        // Check if another process already holds the lock
        $rows = $this->db->query("SELECT locked_at FROM {$this->table} WHERE id = 1");
        if (!empty($rows[0]['locked_at'])) {
            throw new RuntimeException(
                "Another migration process is already running (locked at {$rows[0]['locked_at']})."
            );
        }

        try {
            $this->db->execute(
                "UPDATE {$this->table} SET locked_at = ? WHERE id = 1",
                [date('Y-m-d H:i:s')]
            );
        } catch (\Throwable $e) {
            throw new RuntimeException(
                "Could not acquire migration lock. Cause: " . $e->getMessage()
            );
        }
    }

    public function release(): void
    {
        $this->db->execute(
            "UPDATE {$this->table} SET locked_at = NULL WHERE id = 1"
        );
    }
} 