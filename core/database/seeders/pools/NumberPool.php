<?php

namespace Dorguzen\Core\Database\Seeders\Pools;


class NumberPool extends BasePool
{
    /**
     * Random integer within range.
     */
    public static function int(int $min = 0, int $max = 1000): int
    {
        return rand($min, $max);
    }

    /**
     * Random float within range.
     */
    public static function float(
        float $min = 0,
        float $max = 1000,
        int $decimals = 2
    ): float {
        $number = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return round($number, $decimals);
    }

    /**
     * Random numeric string with fixed length.
     */
    public static function numericString(int $length = 6): string
    {
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            $output .= rand(0, 9);
        }

        return $output;
    }
}