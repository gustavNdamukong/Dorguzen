<?php 

namespace Dorguzen\Core\Database\Migrations;

use Dorguzen\Core\DGZ_DB_Singleton;

abstract class Migration
{
    protected Schema $schema;

    protected array $statements = [];

    public function __construct()
    {
        $db = DGZ_DB_Singleton::getInstance();
        $this->schema = new Schema($db);
    }

    abstract public function up(): void;
    abstract public function down(): void;

    public function addStatement(string $sql): void
    {
        $this->statements[] = $sql;
    }

    public function getStatements(): array
    {
        return $this->statements;
    }
}