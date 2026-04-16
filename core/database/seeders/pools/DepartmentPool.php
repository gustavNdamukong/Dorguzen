<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class DepartmentPool extends BasePool
{
    protected static array $departments = [
        'Engineering','Development','QA','Operations',
        'Human Resources','Customer Services',
        'Production','Delivery','Marketing','Finance'
    ];

    public static function random(): string
    {
        return self::randomFrom(self::$departments);
    }
}