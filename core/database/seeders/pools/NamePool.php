<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class NamePool extends BasePool
{
    protected static array $firstNames = [
        // Male
        'James','John','Robert','Michael','William','David','Richard','Joseph','Thomas','Charles',
        'Daniel','Matthew','Anthony','Mark','Steven','Paul','Andrew','Joshua','Kenneth','Kevin',
        'Vincent','Ernest','Peter', 'Paul','Alexander', 'Jean','Steve',

        // Female
        'Mary','Patricia', 'Jane','Jennifer','Linda','Arlette','Elizabeth','Barbara','Susan','Dorothy','Jessica','Sarah','Karen',
        'Nancy','Pearl-Anne','Lisa','Margaret','Betty','Sandra','Ashley','Kimberly','Zenita', 'Emily','Donna','Michelle',
        'Wendy','Emma','Shelby','Alexandria','Marie','Nwikery', 'Alvine','Berlinda','Audeyne',
    ];

    protected static array $lastNames = [
        'Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez',
        'Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin',
        'Lee','Perez','Thompson','White','Harris','Sanchez','Clark','Ramirez','Lewis','Robinson'
    ];

    public static function first(): string
    {
        return self::randomFrom(self::$firstNames);
    }

    public static function last(): string
    {
        return self::randomFrom(self::$lastNames);
    }

    public static function middle(): string
    {
        return self::randomFrom(self::$firstNames);
    }

    public static function full(): string
    {
        return self::first() . ' ' . self::last();
    }

    protected static function random(array $items): string
    {
        return $items[array_rand($items)];
    }
}