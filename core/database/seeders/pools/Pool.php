<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

/**
 * Pool class is meant to the public Facade (bridge) between the 
 * Pool classes and the PoolRegistry. This separates concerns 
 * in the perspective of the Pool service. Client code will 
 * go through this here Pool, and then the PoolRegistry, to 
 * call methods on the Pool classes. 
 * It offers an ergonomic API for client calls.
 * 
 * Now your public API becomes:
 *  
 *  Pool::get('name.full');
 *  Pool::get('country');
 *  Pool::get('status');
 *  Pool::get('postcode.uk');
 * 
 * And the base pool utilities remain untouched
 * Usage example:
 * 
 * Inside a Factory, you would seed a DB table like this:
 * 
 *      use Dorguzen\Core\Database\Seeders\Pools\Pool;
 *      
 *  then:
 * 
 *      return [
 *          'name' => Pool::get('name.full'),
 *          'email' => Pool::get('email', Pool::get('name.full')),
 *          'status' => Pool::get('status'),
 *          'country' => Pool::get('country'),
 *      ];
 * 
 *  or:
 * 
 *      'name' => Pool::get('name.full')
 * 
 */
class Pool
{
    public static function get(string $key, ...$arguments)
    {
        return PoolRegistry::get($key, ...$arguments);
    }
}