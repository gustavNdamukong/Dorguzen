<?php

namespace Dorguzen\Tests\Manual;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model as Model;
use Dorguzen\Tests\Manual\DgzTestPost;

/**
 * Test model for verifying DGZ lazy loading (hasChild direction).
 * Paired with DgzTestPost.
 *
 * Demonstrates:
 *   $author->dgzTestPost()   — explicit FK
 *   $author->dgzTestComment() — omitted FK (defaults to 'dgztestauthor_id')
 */
class DgzTestAuthor extends Model
{
    protected string $table = 'dgz_test_authors';

    protected $id = 'author_id';

    protected $_columns = [];

    protected $data = [];

    protected $_hasChild = [
        DgzTestPost::class => 'post_author_id',  // explicit FK
        // 'DgzTestComment' => '',               // example of omitted FK (defaults to 'dgztestauthor_id')
    ];

    protected $_hasParent = [];

    public function __construct(?Config $config)
    {
        parent::__construct($config);
    }
}
