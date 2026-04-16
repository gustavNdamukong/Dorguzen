<?php

namespace Dorguzen\Tests\Manual;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model as Model;
use Dorguzen\Tests\Manual\DgzTestAuthor;

/**
 * Test model for verifying DGZ lazy loading (hasParent direction).
 * Paired with DgzTestAuthor.
 *
 * Demonstrates:
 *   $post->dgzTestAuthor()   — resolves the parent Author for this post
 */
class DgzTestPost extends Model
{
    protected string $table = 'dgz_test_posts';

    protected $id = 'post_id';

    protected $_columns = [];

    protected $data = [];

    protected $_hasChild = [];

    protected $_hasParent = [
        DgzTestAuthor::class => 'post_author_id',  // FK on this (posts) table pointing to authors
    ];

    public function __construct(?Config $config)
    {
        parent::__construct($config);
    }
}
