<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class UsernamePool extends BasePool
{
    public static function generate(string $name): string
    {
        $base = strtolower(str_replace(' ', '.', $name));

        return $base . rand(10, 99);
    }

    public static function random(): string
    {
        $name = NamePool::full();
        return self::generate($name);
    }
}