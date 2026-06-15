<?php

namespace Dorguzen\Modules\Blog\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class BlogPost extends DGZ_Model
{
    protected $_columns   = [];
    protected $data       = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'blog_posts';
    protected $primaryKey = 'post_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getAllPosts(): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.category_id = p.category_id
             ORDER BY p.created_at DESC"
        ) ?: [];
    }

    public function getPublishedPosts(): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.category_id = p.category_id
             WHERE p.status = 'published'
             ORDER BY p.published_at DESC"
        ) ?: [];
    }

    public function getPublishedByCategory(int $categoryId): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.category_id = p.category_id
             WHERE p.status = 'published' AND p.category_id = ?
             ORDER BY p.published_at DESC",
            [$categoryId]
        ) ?: [];
    }

    public function searchPublished(string $term): array
    {
        $like = '%' . $term . '%';
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.category_id = p.category_id
             WHERE p.status = 'published'
               AND (p.title LIKE ? OR p.excerpt LIKE ? OR p.body LIKE ? OR c.name LIKE ?)
             ORDER BY p.published_at DESC",
            [$like, $like, $like, $like]
        ) ?: [];
    }

    public function getPostById(int $id): ?array
    {
        $r = $this->query(
            "SELECT p.*, c.name AS category_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.category_id = p.category_id
             WHERE p.post_id = ?",
            [$id]
        );
        return $r[0] ?? null;
    }

    public function getPostBySlug(string $slug): ?array
    {
        $r = $this->query(
            "SELECT p.*, c.name AS category_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.category_id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'",
            [$slug]
        );
        return $r[0] ?? null;
    }

    public function getRecentPublished(int $limit = 5): array
    {
        return $this->query(
            "SELECT post_id, title, slug, cover_image, excerpt, published_at
             FROM blog_posts
             WHERE status = 'published'
             ORDER BY published_at DESC
             LIMIT ?",
            [$limit]
        ) ?: [];
    }

    public function slugExists(string $slug, int $excludeId = 0): bool
    {
        $sql    = $excludeId
            ? "SELECT post_id FROM blog_posts WHERE slug = ? AND post_id != ?"
            : "SELECT post_id FROM blog_posts WHERE slug = ?";
        $params = $excludeId ? [$slug, $excludeId] : [$slug];
        return !empty($this->query($sql, $params));
    }

    public function deleteByPostId(int $postId): void
    {
        $this->deleteWhere(['post_id' => $postId]);
    }
}
