<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

/**
 * Dorguzen keeps it simple. No currency formatting engine
 */
class CurrencyPool extends BasePool
{
    protected static array $currencies = [
        'GBP',
        'USD',
        'EUR',
        'CAD',
        'AUD',
        'CHF',
        'SEK',
        'NOK',
        'DKK'
    ];

    public static function code(): string
    {
        return self::randomFrom(self::$currencies);
    }

    public static function symbol(): string
    {
        return match (self::code()) {
            'GBP' => '£',
            'USD' => '$',
            'EUR' => '€',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
            default => '$'
        };
    }
}