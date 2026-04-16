<?php

namespace Dorguzen\Core\Database\Seeders;

use Dorguzen\Core\DGZ_DBAdapter;
use RuntimeException;
use Dorguzen\Core\Database\Seeders\Factory;


/**
 * Seeder is the base seeder class
 */
abstract class Seeder
{
    protected DGZ_DBAdapter $db;

    protected bool $pretend = false;

    protected bool $force = false;

    protected bool $truncate = false;

    protected string $table = '';

    public function __construct(DGZ_DBAdapter $db)
    {
        $this->db = $db;
    }

    abstract public function run(): void;

    abstract public function getTable(): string;



    /**
     * Enable pretend mode (no DB writes)
     */
    public function pretend(bool $state = true): static
    {
        $this->pretend = $state;
        return $this;
    }

    public function isPretending(): bool
    {
        return $this->pretend;
    }

    /**
     * Allow seeding in protected environments
     */
    public function force(bool $state = true): static
    {
        $this->force = $state;
        return $this;
    }



    /**
     * Call one or more seeders from within another seeder.
     *
     * This allows DatabaseSeeder (or any other seeder) to act
     * as an orchestrator without duplicating logic.
     *
     * Usage:
     *   $this->call(UserSeeder::class);
     *   $this->call([UserSeeder::class, PostSeeder::class]);
     *
     * @param string|array $seeders
     */
    protected function call(string|array $seeders): void
    {
        // Normalize to array so we can treat both cases the same
        $seeders = is_array($seeders) ? $seeders : [$seeders];

        foreach ($seeders as $seederClass) {
            // Ensure the class exists before attempting to run it
            if (!class_exists($seederClass)) {
                throw new RuntimeException(
                    "Seeder class not found: {$seederClass}"
                );
            }

            // Instantiate the seeder, passing the same DB adapter
            /** @var Seeder $seeder */
            $seeder = new $seederClass($this->db);

            // Inherit runtime flags from the parent seeder
            $seeder->pretend($this->pretend);
            $seeder->force($this->force);

            // Run the child seeder
            $seeder->run();
        }
    }

    protected function factory(string $factoryClass)
    {
        if (!class_exists($factoryClass)) {
            throw new RuntimeException("Factory {$factoryClass} not found.");
        }

        $factory = new $factoryClass($this->db);

        if (!$factory instanceof Factory) {
            throw new RuntimeException("{$factoryClass} must extend Factory.");
        }
        
        return $factory;
    }


    public function truncate(bool $state = true): static
    {
        $this->truncate = $state;
        return $this;
    }

    public function shouldTruncate(): bool
    {
        return $this->truncate;
    }
}