<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class DatePool extends BasePool
{
    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    public static function dob(int $minAge = 18, int $maxAge = 65): string
    {
        $timestamp = strtotime('-' . rand($minAge, $maxAge) . ' years');
        return date('Y-m-d', $timestamp);
    }
}