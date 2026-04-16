<?php

namespace Dorguzen\Core\Database\Migrations;

use Dorguzen\Core\DGZ_DBAdapter;

class Schema
{
    protected DGZ_DBAdapter $db;

    public function __construct(DGZ_DBAdapter $db)
    {
        $this->db = $db;
    }

    public function create(string $table, callable $callback): string
    {
        $blueprint = new Blueprint($table);

        try {
            $callback($blueprint);
        } catch (\Throwable $e) {
            dd('Callback exception:', $e->getMessage(), $e->getTraceAsString());
        }

        $sql = $blueprint->toSqlCreate();
        return $sql;
    }

    public function dropIfExists(string $table): string
    {
        return "DROP TABLE IF EXISTS `$table`";
    }
}