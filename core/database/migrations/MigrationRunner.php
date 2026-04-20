<?php

namespace Dorguzen\Core\Database\Migrations;

use Dorguzen\Core\DGZ_DBAdapter;
use Dorguzen\Core\Database\Migrations\MigrationRepository;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;


/**
 * MigrationRunner represents execution logic, while
 * MigrationRepository represents data storage on one table; 
 *  'dgz_migrations'.
 */
class MigrationRunner
{
    protected DGZ_DBAdapter $db;

    protected MigrationRepository $repository;

    protected MigrationLockRepository $lock;

    protected string $path;

     /**
      * pretend is set by migration commands when they pass their tasks to this class.
      * They get the value from their user input --pretend option (flag).
      * This value is set with $this->pretend().
      * Migration methods in here can check for this value & only show SQL queries
      * (instead of running the actual commands) if the user only means to see (pretend)
      * the action that will happen if they ran it.
      *
      * Here are some pretend rules:
      *   -pretend is owned by Runner (this class - MigrationRunner)
      *   -its value updated by any Command class calling it → It applies that when it calls → MigrationRepository
      *   -Hooks (if any) do not execute in pretend
      * @var bool
      */
     protected bool $pretend = false;

    public function __construct(
        DGZ_DBAdapter $db,
        MigrationRepository $repo,
        MigrationLockRepository $lock,
        string $path
    ) {
        $this->db   = $db;
        $this->repository = $repo;
        $this->lock = $lock;
        $this->path = rtrim($path, '/');

        // 🔐 Ensure infrastructure exists BEFORE anything else
        $this->repository->ensureTableExists();
        $this->lock->ensureTableExists();
    }

    /**
     * Return all migration files on disk
     */
    public function getMigrationFiles(): array
    {
        $files = glob($this->path . '/*.php') ?: [];
        sort($files);
        return $files;
    }

    public function runUp(): void
    { 
        $this->lock->acquire();

        try {
            $ran = $this->repository->getRan();

            foreach ($this->getMigrationFiles() as $file) {
                if (in_array(basename($file), $ran)) {
                    continue;
                }

                $migration = require $file;

                // build the SQL
                $migration->up();  
                $statements = $migration->getStatements();

                if (empty($statements)) {
                    return;
                }

                // Execute or pretend
                foreach ($statements as $sql) {
                    // A single "statement" may contain multiple SQL queries separated
                    // by ";\n" (e.g. CREATE TABLE + CREATE INDEX for SQLite).
                    $parts = array_filter(array_map('trim', explode(";\n", $sql)));
                    foreach ($parts as $part) {
                        if ($this->pretend) {
                            echo $part . PHP_EOL;
                        } else {
                            $this->db->execute($part);
                        }
                    }
                }

                // Log ONLY if not pretending (into dgz_migrations)
                if (! $this->pretend) {
                    $this->repository->log(
                        basename($file),
                        $this->repository->getLastBatch() + 1
                    );
                }
                // Migration done
            }
        } finally {
            // using finally ensures that lock is released even if migration crashes
            $this->lock->release();
        }
    }


    public function runDown(?int $steps = null): void
    {
        $this->lock->acquire();

        try {
            if ($steps !== null) {
                $migrations = $this->repository->getLast($steps);
            } else {
                $migrations = $this->repository->getLastBatchMigrations();
            }

            if (empty($migrations)) {
                echo "Nothing to rollback.\n";
                return;
            }

            foreach ($migrations as $row) {
                $file = $this->path . '/' . $row['migration'];

                if (!file_exists($file)) {
                    echo "Missing migration file: {$row['migration']}\n";
                    continue;
                }

                $migration = require $file;

                echo "Rolling back: {$row['migration']}\n";

                $migration->down();
                $statements = $migration->getStatements();

                if (empty($statements)) {
                    return;
                }

                // Execute or pretend
                foreach ($statements as $sql) {
                    if ($this->pretend) {
                        echo $sql . PHP_EOL;
                    } else {
                        $this->db->execute($sql);
                    }
                }

                // remove the logged migration (from dgz_migrations)
                if (! $this->pretend) {
                    $this->repository->delete($row['migration']);
                    echo "Rolled back: {$row['migration']}\n";
                }
            }
        } finally {
            // using finally ensures that lock is released even if migration crashes
            $this->lock->release();
        }
    }


    public function runDownAll(): void
    {
        $this->lock->acquire();

        try {
            $migrations = $this->repository->all();

            foreach ($migrations as $row) {
                $file = $this->path . '/' . $row['migration'];

                if (!file_exists($file)) {
                    echo "Missing migration file: {$row['migration']}\n";
                    continue;
                }

                $migration = require $file;

                echo "Rolling back: {$row['migration']}\n";

                $migration->down();
                $statements = $migration->getStatements();

                if (empty($statements)) {
                    return;
                }

                // Execute or pretend
                foreach ($statements as $sql) {
                    if ($this->pretend) {
                        echo $sql . PHP_EOL;
                    } else {
                        $this->db->execute($sql);
                    }
                }

                // remove the logged migration (from dgz_migrations)
                if (! $this->pretend) {
                    $this->repository->delete($row['migration']);
                    echo "Rolled back: {$row['migration']}\n";
                }
            }
        } finally {
            // using finally ensures that lock is released even if migration crashes
            $this->lock->release();
        }
    }


    public function runSingle(string $file): void
    {
        $this->lock->acquire();

        try {  
            $ran = $this->repository->getRan();

            if (in_array(basename($file), $ran)) {
                // migration already ran
                echo "Migration: {$file} was already ran ...aborting\n";
                return; 
            }

            $path = $this->path . '/' . $file;

            if (!file_exists($path)) {
                throw new \RuntimeException("Migration file not found: {$file}");
            }

            $migration = require $path;

            $migration->up();

            foreach ($migration->getStatements() as $sql) {
                $this->db->execute($sql);
            }

            // register migration as ran, in dgz_migrations table
            $this->repository->log(
                basename($file), 
                $this->repository->getLastBatch() + 1
            );
        } finally {  
            // using finally ensures that lock is released even if migration crashes
            $this->lock->release();
        } 
    }



    public function status(): array
    {
        $ran = $this->repository->all();

        // Convert to lookup table
        $ranMap = [];
        foreach ($ran as $row) {
            $ranMap[$row['migration']] = $row['batch'];
        }

        $files = glob($this->path . '/*.php');
        sort($files);

        $status = [];

        foreach ($files as $file) {
            $name = basename($file);

            if (isset($ranMap[$name])) {
                $status[] = [
                    'migration' => $name,
                    'batch'     => $ranMap[$name],
                    'status'    => 'Ran',
                ];
            } else {
                $status[] = [
                    'migration' => $name,
                    'batch'     => null,
                    'status'    => 'Pending',
                ];
            }
        }

        return $status;
    }


    public function fresh(): void
    {
        if ($this->pretend) {
            echo "[Pretend] Dropping non-infrastructure tables\n";
            foreach ($this->db->listTables() as $table) {
                if (in_array($table, ['dgz_migrations', 'dgz_migration_locks'], true)) {
                    continue;
                }
                echo "DROP TABLE $table;\n";
            }

            echo "\n[Pretend] Running migrations:\n";
            foreach ($this->getMigrationFiles() as $file) {
                echo "Would run: " . basename($file) . "\n";
            }

            return;
        }

        // Drop all app tables and clear migration history before re-running.
        // runUp() acquires and releases its own lock, so no outer lock is needed.
        $this->repository->dropAllNonInfrastructureTables();
        $this->repository->clear();
        $this->runUp();
    }



    public function pretend(bool $state = true): void
    {
        $this->pretend = $state;
    }

    protected function isPretending(): bool
    {
        return $this->pretend;
    }
}