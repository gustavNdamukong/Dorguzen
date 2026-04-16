<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

/**
 * Pool is the base for all data pool objects
 * Dorguzen has its own data pooling service, rather than using Faker. 
 * This is not because Faker is wrong, but rather to:
 * 
 *     -Keep everything static
 *     -Keep everything deterministic-friendly
 *     -Avoid external dependencies
 *     -Keep classes small and composable
 *     -and avoid over-engineering
 *     -It reuses Pool::randomFrom()
 */
abstract class BasePool
{
    protected static function randomFrom(array $items)
    {
        return $items[array_rand($items)];
    }
}
