<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

/**
 * Class Newsletters
 *
 * Represents the `newsletters` table.
 */
class Newsletters extends DGZ_Model
{
    protected $_columns = [];

    protected $data = [];

    protected $_hasParent = [];

    protected $_hasChild = [];

    protected $table = 'newsletters';

    protected $primaryKey = 'newsletter_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Get all newsletters ordered by most recent first.
     */
    public function getAllNewsletters(): array
    {
        $query = "SELECT * FROM newsletters ORDER BY newsletter_created DESC";
        return $this->query($query) ?: [];
    }

    /**
     * Get a single newsletter by ID.
     */
    public function getSingleNewsletter(int $id): ?array
    {
        $query = "SELECT * FROM newsletters WHERE newsletter_id = ?";
        $result = $this->query($query, [$id]);
        return ($result && isset($result[0])) ? $result[0] : null;
    }
}
