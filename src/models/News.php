<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

/**
 * Class News
 *
 * Represents the `news` table.
 */
class News extends DGZ_Model
{
    protected $_columns = [];

    protected $data = [];

    protected $_hasParent = [];

    protected $_hasChild = [];

    protected $table = 'news';

    protected $primaryKey = 'news_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Get all news items ordered by created date descending.
     */
    public function getAllNews(): array
    {
        $query = "SELECT * FROM news ORDER BY news_created DESC";
        return $this->query($query) ?: [];
    }

    /**
     * Get all published news items ordered by created date descending.
     */
    public function getPublishedNews(): array
    {
        $query  = "SELECT * FROM news WHERE news_status = ? ORDER BY news_created DESC";
        return $this->query($query, ['published']) ?: [];
    }

    /**
     * Get latest published news items (for sidebar / homepage widgets).
     */
    public function getLatestNews(int $limit = 4): array
    {
        $query = "SELECT * FROM news WHERE news_status = ? ORDER BY news_created DESC LIMIT $limit";
        return $this->query($query, ['published']) ?: [];
    }

    /**
     * Search published news by keyword (title or description).
     */
    public function search(string $keyword): array
    {
        $like = '%' . $keyword . '%';
        return $this->query(
            "SELECT news_id, news_title, news_description, news_image
             FROM news
             WHERE news_status = 'published' AND (news_title LIKE ? OR news_description LIKE ?)
             ORDER BY news_created DESC LIMIT 20",
            [$like, $like]
        ) ?: [];
    }

    /**
     * Get a single news item by its ID.
     */
    public function getSingleNewsItem(int $newsId): array
    {
        $query = "SELECT * FROM news WHERE news_id = ?";
        return $this->query($query, [$newsId]) ?: [];
    }
}
