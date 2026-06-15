<?php

namespace Dorguzen\Modules\Blog\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class BlogComment extends DGZ_Model
{
    protected $_columns   = [];
    protected $data       = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'blog_comments';
    protected $primaryKey = 'comment_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    public function getApprovedByPost(int $postId): array
    {
        return $this->query(
            "SELECT * FROM blog_comments
             WHERE post_id = ? AND status = 'approved'
             ORDER BY created_at ASC",
            [$postId]
        ) ?: [];
    }

    public function getAllComments(): array
    {
        return $this->query(
            "SELECT c.*, p.title AS post_title, p.slug AS post_slug
             FROM blog_comments c
             LEFT JOIN blog_posts p ON p.post_id = c.post_id
             ORDER BY c.created_at DESC"
        ) ?: [];
    }

    public function getPendingCount(): int
    {
        $r = $this->query("SELECT COUNT(*) AS cnt FROM blog_comments WHERE status = 'pending'");
        return (int) ($r[0]['cnt'] ?? 0);
    }

    public function getCommentById(int $id): ?array
    {
        $r = $this->query("SELECT * FROM blog_comments WHERE comment_id = ?", [$id]);
        return $r[0] ?? null;
    }

    public function countApprovedByPost(int $postId): int
    {
        $r = $this->query(
            "SELECT COUNT(*) AS cnt FROM blog_comments WHERE post_id = ? AND status = 'approved'",
            [$postId]
        );
        return (int) ($r[0]['cnt'] ?? 0);
    }

    public function deleteByPost(int $postId): void
    {
        $this->deleteWhere(['post_id' => $postId]);
    }
}
