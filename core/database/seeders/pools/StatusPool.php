<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

/**
 * Statuses are perfect for:
 *  
 *  -users
 *  -posts
 *  -invoices
 *  -subscriptions
 */
class StatusPool extends BasePool
{
    protected static array $statuses = [
        'active',
        'inactive',
        'pending',
        'archived',
        'suspended',
        'draft'
    ];

    public static function random(): string
    {
        return self::randomFrom(self::$statuses);
    }
}