<?php

namespace Dorguzen\Database\Seeders;

use Dorguzen\Core\Database\Seeders\Seeder;
use Dorguzen\Database\Seeders\SuperAdminSeeder;
use Dorguzen\Database\Seeders\BaseSettingsSeeder;

/**
 * The root seeder — orchestrates all application seeders.
 * Run via: php dgz db:seed
 */
class DatabaseSeeder extends Seeder
{
    protected string $table = '';

    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
        $this->call(BaseSettingsSeeder::class);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
