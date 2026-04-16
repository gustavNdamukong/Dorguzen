<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class CountryPool extends BasePool
{
    protected static array $countries = [
        'United Kingdom',
        'United States',
        'Botswana',
        'France',
        'Germany',
        'Spain',
        'Italy',
        'Netherlands',
        'Canada',
        'Australia',
        'Ireland',
        'Sweden',
        'Norway',
        'Denmark',
        'Brazil',
        'Cameroon',
        'Tanzania',
        'Switzerland'
    ];

    public static function random(): string
    {
        return self::randomFrom(self::$countries);
    }
}