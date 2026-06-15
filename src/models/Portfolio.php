<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

/**
 * Class Portfolio
 *
 * Represents the `portfolio` table.
 */
class Portfolio extends DGZ_Model
{
    protected $_columns = [];
    protected $data     = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'portfolio';
    protected $primaryKey = 'portfolio_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Get all portfolio items, newest first.
     */
    public function getAllPortfolio(): array
    {
        return $this->query(
            "SELECT * FROM portfolio ORDER BY portfolio_created DESC"
        ) ?: [];
    }

    /**
     * Search portfolio items by keyword (title or description).
     */
    public function search(string $keyword): array
    {
        $like = '%' . $keyword . '%';
        return $this->query(
            "SELECT portfolio_id, portfolio_title, portfolio_description, portfolio_image
             FROM portfolio
             WHERE portfolio_title LIKE ? OR portfolio_description LIKE ?
             ORDER BY portfolio_created DESC LIMIT 20",
            [$like, $like]
        ) ?: [];
    }

    /**
     * Get a single portfolio item by ID.
     */
    public function getSinglePortfolioItem(int $id): ?array
    {
        $result = $this->query(
            "SELECT * FROM portfolio WHERE portfolio_id = ?", [$id]
        );
        return ($result && isset($result[0])) ? $result[0] : null;
    }
}
