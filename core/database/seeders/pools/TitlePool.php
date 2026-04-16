<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class TitlePool extends BasePool
{
    protected static array $titles = [
        'Mr','Mrs','Miss','Ms','Dr','Prof','Professor',
        'Sir','Madam','Hon','Lord','Lady'
    ];

    public static function random(): string
    {
        return self::randomFrom(self::$titles);
    }
}