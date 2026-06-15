<?php

namespace Dorguzen\Modules\Blog\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class BlogCategory extends DGZ_Model
{
    protected $_columns   = [];
    protected $data       = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'blog_categories';
    protected $primaryKey = 'category_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getAllCategories(): array
    {
        return $this->query(
            "SELECT c.*, COUNT(p.post_id) AS post_count
             FROM blog_categories c
             LEFT JOIN blog_posts p ON p.category_id = c.category_id AND p.status = 'published'
             GROUP BY c.category_id
             ORDER BY c.name ASC"
        ) ?: [];
    }

    public function getCategoryById(int $id): ?array
    {
        $r = $this->query("SELECT * FROM blog_categories WHERE category_id = ?", [$id]);
        return $r[0] ?? null;
    }

    public function slugExists(string $slug, int $excludeId = 0): bool
    {
        $sql    = $excludeId
            ? "SELECT category_id FROM blog_categories WHERE slug = ? AND category_id != ?"
            : "SELECT category_id FROM blog_categories WHERE slug = ?";
        $params = $excludeId ? [$slug, $excludeId] : [$slug];
        return !empty($this->query($sql, $params));
    }
}
