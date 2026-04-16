<?php

namespace Dorguzen\Core\Database\Migrations;

use Dorguzen\Core\DGZ_DBAdapter;

/**
 * MigrationRepository manages exactly ONE table; 'dgz_migrations'.
 * MigrationRepository represents data storage.
 */
class MigrationRepository
{
    protected DGZ_DBAdapter $db;

    protected string $table = 'dgz_migrations';

    public function __construct(DGZ_DBAdapter $db)
    {
        $this->db = $db;
    }


    public function ensureTableExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";

        $this->db->execute($sql);
    }


    public function dropAllNonInfrastructureTables(): void
    {
        $tables = $this->db->query("SHOW TABLES");

        $protected = [
            'dgz_migrations',
            'dgz_migration_locks',
        ];

        foreach ($tables as $row) {
            $table = array_values($row)[0];

            if (in_array($table, $protected, true)) {
                continue;
            }

            $this->db->execute("DROP TABLE `$table`");
        }
    }


    // clear migrations table because migrations recorded in there cannot be reran.
    // It was meant to prevent running a migration more than once. 
    public function clear(): void
    {
        // Using TRUNCATE is much faster than DELETE FROM
        $this->db->execute("TRUNCATE TABLE {$this->table}");
    }


    public function getRan(): array
    {
        $rows = $this->db->query(
            "SELECT migration FROM dgz_migrations ORDER BY id ASC"
        );

        return array_column($rows ?? [], 'migration');
    }

    public function getLastBatch(): int
    {
        $rows = $this->db->query(
            "SELECT MAX(batch) AS batch FROM dgz_migrations"
        );

        return (int) ($rows[0]['batch'] ?? 0);
    }


    public function getLastBatchMigrations(): array
    {
        $batch = $this->getLastBatch();

        if ($batch === 0) {
            return [];
        }

        return $this->db->query(
            "SELECT migration FROM dgz_migrations
            WHERE batch = ?
            ORDER BY id DESC",
            [$batch]
        );
    }


    /**
     * Get the last run migration batch, in reverse order (newest first)
     * @param int $int the number of lastly run migrations to select
     */
    public function getLast(int $limit): array
    {
        return $this->db->query(
            "SELECT migration, batch
            FROM dgz_migrations
            ORDER BY batch DESC, migration DESC
            LIMIT {$limit}"
        );
    }


    public function log(string $migration, int $batch): void
    {
        $sql = "
            INSERT INTO dgz_migrations (migration, batch)
            VALUES (?, ?)
        ";

        $this->db->execute($sql, [
            $migration,
            $batch,
        ]);
    }


    public function delete(string $migration): void
    {
        $this->db->execute(
            "DELETE FROM dgz_migrations WHERE migration = ?",
            [$migration]
        );
    }

    public function all(): array
    {
        return $this->db->query(
            "SELECT migration, batch FROM dgz_migrations ORDER BY batch DESC, migration DESC"
        );
    }

    public function dropAllTables(): void
    {
        $tables = $this->db->query("SHOW TABLES");

        foreach ($tables as $row) {
            $table = array_values($row)[0];

            // Never drop the migrations table twice
            $this->db->execute("DROP TABLE IF EXISTS `$table`");
        }
}
}