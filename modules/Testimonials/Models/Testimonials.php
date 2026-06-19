<?php

namespace Dorguzen\Modules\Testimonials\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class Testimonials extends DGZ_Model
{
    protected $_columns   = [];
    protected $data       = [];
    protected $_hasParent = [];
    protected $_hasChild  = [];

    protected $table      = 'testimonials';
    protected $primaryKey = 'testimonial_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Return approved testimonials ordered newest-first.
     * Pass $limit > 0 to cap the result set.
     */
    public function getApproved(int $limit = 0): array
    {
        $sql    = "SELECT * FROM testimonials WHERE testimonial_status = 'approved' ORDER BY created_at DESC";
        $params = [];

        if ($limit > 0) {
            $sql .= ' LIMIT ?';
            $params[] = $limit;
        }

        return $this->query($sql, $params) ?: [];
    }

    /**
     * Return every testimonial for the admin panel, newest first.
     */
    public function getAllForAdmin(): array
    {
        return $this->query(
            "SELECT * FROM testimonials ORDER BY created_at DESC"
        ) ?: [];
    }
}
