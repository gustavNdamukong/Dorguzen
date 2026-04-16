<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class PhonePool extends BasePool
{
    public static function mobile(): string
    {
        return '+44 7' . rand(100,999) . ' ' . rand(100000,999999);
    }

    public static function ukLandline(): string
    {
        return '+44 1' . rand(100,999) . ' ' . rand(100000,999999);
    }

    public static function us(): string
    {
        return '+1 (' . rand(200,999) . ') ' .
               rand(200,999) . '-' .
               rand(1000,9999);
    }
}