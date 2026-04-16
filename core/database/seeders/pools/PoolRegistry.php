<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

class PoolRegistry
{
    protected static array $map = [

        // Text
        'text.sentence'  => [TextPool::class, 'sentence'],
        'text.paragraph' => [TextPool::class, 'paragraph'],
        'text.text'      => [TextPool::class, 'text'],

        // Numbers
        'number.int'     => [NumberPool::class, 'int'],
        'number.float'   => [NumberPool::class, 'float'],
        'number.numeric' => [NumberPool::class, 'numericString'],

        // Names
        'name.first' => [NamePool::class, 'first'],
        'name.last'  => [NamePool::class, 'last'],
        'name.full'  => [NamePool::class, 'full'],

        // Title
        'title.'  => [TitlePool::class, 'random'],

        // Email
        'email' => [EmailPool::class, 'random'],

        // Address
        'address.home'     => [AddressPool::class, 'home'],
        'address.business' => [AddressPool::class, 'business'],

        // Postcodes
        'postcode.uk' => [PostcodePool::class, 'uk'],
        'postcode.us' => [PostcodePool::class, 'us'],
        'postcode.eu' => [PostcodePool::class, 'eu'],

        // Phone
        'phone.mobile' => [PhonePool::class, 'mobile'],
        'phone.uk'     => [PhonePool::class, 'ukLandline'],
        'phone.us'     => [PhonePool::class, 'us'],

        // Company
        'company' => [CompanyPool::class, 'name'],

        // Department
        'department' => [DepartmentPool::class, 'random'],

        // Role
        'role' => [RolePool::class, 'random'],

        // Country
        'country' => [CountryPool::class, 'random'],

        // Currency
        'currency.code'   => [CurrencyPool::class, 'code'],
        'currency.symbol' => [CurrencyPool::class, 'symbol'],

        // Status
        'status' => [StatusPool::class, 'random'],

        // Boolean
        'boolean.int'  => [BooleanPool::class, 'int'],
        'boolean.bool' => [BooleanPool::class, 'bool'],

        // Username
        'username' => [UsernamePool::class, 'random'],

        // Date
        'date.now' => [DatePool::class, 'now'],
        'date.dob' => [DatePool::class, 'dob'],

        // Slug
        'slug'  => [SlugPool::class, 'from'], 
    ];

    public static function get(string $key, ...$arguments)
    {
        if (! isset(self::$map[$key])) {
            throw new \InvalidArgumentException("Pool key '{$key}' is not registered.");
        }

        [$class, $method] = self::$map[$key];

        return $class::$method(...$arguments);
    }

    public static function register(string $key, callable $resolver): void
    {
        self::$map[$key] = $resolver;
    }
}
// SlugPool, TitlePool,  