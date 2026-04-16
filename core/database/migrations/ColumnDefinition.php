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
    

    public function toSql(): string
    {
        return trim(
            "`{$this->name}` {$this->type} " . implode(' ', $this->modifiers)
        );
    }
}