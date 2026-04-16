<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class AddressPool extends BasePool
{
    protected static array $streets = [
        'High Street','Main Street','Park Avenue','Church Road',
        'London Road','Victoria Street','Green Lane','Station Road',
        'Kingsway','Queens Road','Mill Lane','Bridge Street'
    ];

    protected static array $cities = [
        'London','Manchester','Birmingham','Liverpool','Leeds',
        'Bristol','Glasgow','Edinburgh','Cardiff','Newcastle'
    ];

    protected static array $businessPrefixes = [
        'Suite','Floor','Building','Unit','Office'
    ];

    public static function home(): string
    {
        return rand(1, 250) . ' ' .
               self::randomFrom(self::$streets) . ', ' .
               self::randomFrom(self::$cities);
    }

    public static function business(): string
    {
        return self::randomFrom(self::$businessPrefixes) . ' ' .
               rand(1, 20) . ', ' .
               rand(1, 250) . ' ' .
               self::randomFrom(self::$streets) . ', ' .
               self::randomFrom(self::$cities);
    }
}