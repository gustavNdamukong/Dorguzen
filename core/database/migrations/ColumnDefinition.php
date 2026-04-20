<?php

namespace Dorguzen\Core\Database\Migrations;

class ColumnDefinition
{
    protected string $name;
    protected string $type;
    protected array $modifiers = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function unique(): self
    {
        $this->modifiers[] = 'UNIQUE';
        return $this;
    }

    public function nullable(): self
    {
        $this->modifiers[] = 'NULL';
        return $this;
    }


    /**
     * notNullable allows developers to explicitly define a field as not nullable
     * @return ColumnDefinition
     */
    public function notNullable(): self
    {
        $this->modifiers[] = 'NOT NULL';
        return $this;
    }

    public function default(mixed $value): self
    {
        if (is_string($value)) {
            $value = "'" . addslashes($value) . "'";
        }

        $this->modifiers[] = "DEFAULT {$value}";
        return $this;
    }


    public function useCurrent(): self
    {
        $this->modifiers[] = 'DEFAULT CURRENT_TIMESTAMP';
        return $this;
    }

    public function useCurrentOnUpdate(): self
    {
        // MySQL-only modifier; stored so toSql() can emit it only for MySQL
        $this->modifiers[] = '__ON_UPDATE_CURRENT_TIMESTAMP__';
        return $this;
    }

    public function toSql(string $driver = 'mysqli'): string
    {
        $type = $this->type;

        // SQLite has no ENUM type — use TEXT instead
        if ($driver === 'sqlite' && str_starts_with(strtoupper($type), 'ENUM(')) {
            $type = 'TEXT';
        }

        $modifiers = array_filter($this->modifiers, function (string $m) use ($driver): bool {
            if ($m === '__ON_UPDATE_CURRENT_TIMESTAMP__') {
                return $driver !== 'sqlite';
            }
            return true;
        });

        $modifiers = array_map(function (string $m): string {
            return $m === '__ON_UPDATE_CURRENT_TIMESTAMP__' ? 'ON UPDATE CURRENT_TIMESTAMP' : $m;
        }, $modifiers);

        return trim("`{$this->name}` {$type} " . implode(' ', $modifiers));
    }
}