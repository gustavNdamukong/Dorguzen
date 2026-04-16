<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class PostcodePool extends BasePool
{
    public static function uk(): string
    {
        return chr(rand(65,90)) .
               chr(rand(65,90)) .
               rand(1,9) .
               rand(0,9) .
               ' ' .
               rand(1,9) .
               chr(rand(65,90)) .
               chr(rand(65,90));
    }

    public static function us(): string
    {
        return str_pad((string) rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    }

    public static function eu(): string
    {
        return rand(1000, 9999);
    }
}