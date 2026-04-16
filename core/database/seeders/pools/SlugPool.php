<?php 

namespace Dorguzen\Core\Database\Seeders\Pools;

/**
 * Slugs are useful for:
 *  
 *      -blog posts
 *      -products
 *      -categories
 *      -URLs
 */
class SlugPool extends BasePool
{
    public static function from(string $value): string
    {
        $slug = strtolower($value);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}