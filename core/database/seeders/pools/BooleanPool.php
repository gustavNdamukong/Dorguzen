<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class BooleanPool extends BasePool
{
    public static function int(): int
    {
        return rand(0, 1);
    }

    public static function bool(): bool
    {
        return (bool) rand(0, 1);
    }
}