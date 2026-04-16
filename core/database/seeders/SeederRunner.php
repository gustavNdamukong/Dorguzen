<?php 

namespace Dorguzen\Core\Database\Seeders;

use Dorguzen\Core\DGZ_DBAdapter;
use RuntimeException;

class SeederRunner
{
    protected DGZ_DBAdapter $db;
    protected string $path;

    /**
    * pretend is set by seeding commands when they pass their tasks to this class.
    * They get the value from their user input --pretend option (flag).
    * This value is set using $this->pretend().
    * Seeding methods in here can check for this value & only show SQL queries
    * (instead of running the actual commands) if the user only means to see (pretend)
    * the action that will happen if they ran it.
    *
    * Here are some pretend rules:
    *   -pretend is owned by Runner (this class - SeederRunner)
    *   -propagated → Seeder → Factory
    *   -Hooks do not execute in pretend mode 
    *
    * @var bool
    */
    protected bool $pretend = false;

    protected bool $force = false;
    

    public function __construct(DGZ_DBAdapter $db, string $path)
    {
        $this->db   = $db;
        $this->path = rtrim($path, '/');
    }

    public function run(string $class = 'DatabaseSeeder'): void
    {
        // environment guard (do not allow seeding to be done on production DBs)
        if ($this->isProtectedEnv() && !$this->force)
        {
            throw new RuntimeException(
                'Seeding is disabled in production environments. Use --force to override.'
            );
        }

        $fqcn = "Dorguzen\\Database\\Seeders\\{$class}";
        $file = $this->path . '/' . $class . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("Seeder not found: {$class}");
        }

        // This is technically optional, as Composer autoload has you covered,
        // but we keep it for now for clarity & DX
        require_once $file;

        if (!class_exists($fqcn)) {
            throw new RuntimeException("Seeder class {$fqcn} does not exist in {$file}.");
        }

        $seeder = new $fqcn($this->db);

        if (!$seeder instanceof Seeder) {
            throw new RuntimeException("{$fqcn} must extend Seeder.");
        }

        // whether to truncate or not to truncate table before inserting new data
        if ($seeder->shouldTruncate()) {
            if (!$this->pretend) {
                $this->db->execute("TRUNCATE TABLE `{$seeder->getTable()}`");
            }
        }

        if (!$this->pretend) {
            $seeder->pretend($this->pretend); 
            $seeder->run();
        } else {
            echo "The seeder  {$fqcn} would seed data into table: {$seeder->getTable()}\n";
        }
    }

    public function pretend(bool $state = true): void
    {
        $this->pretend = $state;
    }

    protected function isPretending(): bool
    {
        return $this->pretend;
    }

    /**
     * isProtectedEnv is an environment guard. It helps prevent seeding from 
     * being done on production DBs
     * @return bool
     */
    public function isProtectedEnv(): bool
    {
        $env = env('APP_ENV', 'production');
        return in_array($env, ['production', 'staging'], true);
    }


    public function force(bool $state = true): void
    {
        $this->force = $state;
    }
}