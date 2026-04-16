<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class EmailPool extends BasePool
{
    protected static array $domains = [
        'example.com',
        'test.com',
        'test.dev',
        'mail.com'
    ];

    public static function random(string $name): string
    {
        $slug = strtolower(str_replace(' ', '.', $name));

        return $slug
            . rand(1000, 9999)
            . '@'
            . static::$domains[array_rand(static::$domains)];
    }
}