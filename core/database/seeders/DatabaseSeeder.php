<?php 

namespace Dorguzen\Core\Database\Seeders;

use Dorguzen\Core\Database\Seeders\Seeder;

class DatabaseSeeder extends Seeder
{
    protected string $table = '';
    
    public function run(): void
    {
        // seeders will go here
    }

    public function getTable(): string
    {
        return $this->table; 
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }
}