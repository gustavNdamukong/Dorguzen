<?php

namespace Dorguzen\Core\Database\Migrations;


use Dorguzen\Core\Database\Migrations\ColumnDefinition;


class Blueprint
{
    protected array $indexes = [];
    protected string $table;
    protected array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $idField = 'id'): ColumnDefinition
    {
        $column = new ColumnDefinition(
            $idField,
            'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY'
        );

        $this->columns[] = $column;
        return $column;
    }

    /**
     * primaryKey allows developers to add another primary key field to a table.
     * This can be handly for example, when creating primary key fields that are 
     * not auto-incremented, or multiple primary key fields on a cross-reference tables.
     * @param string $pkField
     * @return ColumnDefinition
     */
    public function primaryKey(string $pkField): ColumnDefinition
    {
        $column = new ColumnDefinition(
            $pkField,
            'VARCHAR(255) PRIMARY KEY'
        );

        $this->columns[] = $column;
        return $column;
    }

    /**
     * foreignId TODO: We give it not fk constraints yet-to do so later
     * @param string $name
     * @return ColumnDefinition
     */
    public function foreignId(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'INT UNSIGNED');
        $this->columns[] = $column;
        return $column;
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $column = new ColumnDefinition(
            $name,
            "VARCHAR({$length})"
        );

        $this->columns[] = $column;
        return $column;
    }

    /**
     * integer supports both positive and negative numbers. 
     * In MySQL supports a range between: -2,147,483,648 and 2,147,483,647
     * Note: INT length only affects display width (MySQL),
     * not storage or numeric range.
     * @param string $name
     * @param int $length optional
     * @return ColumnDefinition
     */
    public function integer(string $name, ?int $length = null): ColumnDefinition
    {
        $type = $length === null 
            ? 'INT'
            : "INT({$length})";

        $column = new ColumnDefinition($name, $type);
        $this->columns[] = $column;
        return $column;
    }


    /**
     * unsignedInteger only stores positive numbers
     * @param string $name
     * @return ColumnDefinition
     */
    public function unsignedInteger(string $name, ?int $length = null): ColumnDefinition
    {
        $type = $length === null
        ? 'INT UNSIGNED'
        : "INT({$length}) UNSIGNED";

        $column = new ColumnDefinition($name, $type);
        $this->columns[] = $column;
        return $column;
    }


    public function decimal(string $name, int $precision, int $scale): ColumnDefinition
    {
        $column = new ColumnDefinition(
            $name,
            "DECIMAL({$precision},{$scale})"
        );

        $this->columns[] = $column;
        return $column;
    }


    public function enum(string $name, array $values): ColumnDefinition
    {
        $escaped = array_map(
            fn ($v) => "'" . addslashes($v) . "'",
            $values
        );

        $column = new ColumnDefinition(
            $name,
            'ENUM(' . implode(',', $escaped) . ') NOT NULL'
        );

        $this->columns[] = $column;
        return $column;
    }


    /**
     * TEXT supports a max size of 64 KB
     * @param string $name
     * @return ColumnDefinition
     */
    public function text(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'TEXT');
        $this->columns[] = $column;
        return $column;
    }

    /**
     * LONGTEXT supports a max size of 5 GB, so it is suitable for storing:
     *      -Serialized objects
     *      -Closures 
     *      -Large job metadata 
     *      -Potentially email templates, reports, etc.
     * @param string $name
     * @return ColumnDefinition
     */
    public function longText(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'LONGTEXT');
        $this->columns[] = $column;
        return $column;
    }

    public function unique(string $column): void
    {
        $this->indexes[] = "UNIQUE (`$column`)";
    }


    public function date(string $date): ColumnDefinition
    {
        $column = new ColumnDefinition($date, 'DATE');
        $this->columns[] = $column;
        return $column;
    }


    /**
     * timestamp() is a single field to hold an explicit timestamps from PHP
     * not SQL's default NOW().  
     * Such single datetime fields are often used to represents system events. 
     * It may be nullable
     * @param string $name
     * @return ColumnDefinition
     */
    public function timestamp(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'DATETIME');
        $this->columns[] = $column;
        return $column;
    }

    public function timestamps(): self
    {
        $this->columns[] = new ColumnDefinition("created_at", "DATETIME");
        $this->columns[] = new ColumnDefinition("updated_at", "DATETIME");
        return $this;
    }

    public function toSqlCreate(): string
    {
        try {
            $cols = array_map(fn (ColumnDefinition $c) => $c->toSql(),$this->columns);
            $cols = array_merge($cols, $this->indexes);
        } catch (\Throwable $e) {
            dd('In toSqlCreate():', $e->getMessage(), $e->getTraceAsString()); 
        }

        return <<<SQL
CREATE TABLE `{$this->table}` (
    {$this->implodeColumns($cols)}
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;
    }


    protected function implodeColumns(array $cols): string
    {
        return implode(",\n    ", $cols);
    }
}