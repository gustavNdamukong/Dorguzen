<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class RolePool extends BasePool
{
    protected static array $roles = [
        'Employee','Manager','Supervisor','Director',
        'Professor','Doctor','Student','Assistant',
        'Secretary','Engineer','Developer',
        'Footballer','Runner','Singer','Athlete'
    ];

    public static function random(): string
    {
        return self::randomFrom(self::$roles);
    }
}