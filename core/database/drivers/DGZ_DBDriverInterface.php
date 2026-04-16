<?php 

namespace Dorguzen\Core\Database\Drivers;

interface DGZ_DBDriverInterface
{
    /**
     * getTableSchema() is used by the DGZ ORM system to map a 
     * model to its table and all its fields. 
     * @param string $table
     * @return void
     */
    public function getTableSchema(string $table): array;

    public function connect();

    public function prepare($query);

    /**
     * SELECT queries ONLY, , by design. It returns results as an array. 
     */
    public function query(string $sql, array $params = []): array;

    /**
     * INSERT/UPDATE/DELETE queries, by design.
     * Recommended use: for INSERT / UPDATE / & DELETE queries ONLY. 
     * It returns a boolean.
     */
    public function execute(string $sql, array $params = []): bool;

    public function fetchAll($result): array;

    public function numRows($result): int;

    /**
     * returns last inserted ID (useful for save() in your models).
     */
    public function lastInsertId(): int|string|null;

    public function getAffectedRows(): int;

    public function getPrimaryKeyField(string $table): string|null;

    public function prepareInsertOrUpdate(array $data, array $passwordFields, string $type = 'insert'): array;

    public function encryptPasswordCondition(string $field): string;
}