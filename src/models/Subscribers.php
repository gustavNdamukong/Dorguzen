<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

/**
 * Class Subscribers
 *
 * Represents the `subscribers` table.
 */
class Subscribers extends DGZ_Model
{
    protected $_columns = [];

    protected $data = [];

    protected $_hasParent = [];

    protected $_hasChild = [];

    protected $table = 'subscribers';

    protected $primaryKey = 'subscriber_id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Get all subscribers ordered by most recent first.
     */
    public function getAllSubscribers(): array
    {
        $query = "SELECT * FROM subscribers ORDER BY subscriber_created DESC";
        return $this->query($query) ?: [];
    }

    /**
     * Get new subscribers (welcomed=0, active=1) — these are pending a welcome email.
     */
    public function getNewSubscribers(): array
    {
        $query = "SELECT * FROM subscribers WHERE subscriber_welcomed = 0 AND subscriber_active = 1 ORDER BY subscriber_created DESC";
        return $this->query($query) ?: [];
    }

    /**
     * Get all active subscribers.
     */
    public function getActiveSubscribers(): array
    {
        $query = "SELECT * FROM subscribers WHERE subscriber_active = 1 ORDER BY subscriber_created DESC";
        return $this->query($query) ?: [];
    }

    /**
     * Find a subscriber by email. Returns the row array or null.
     */
    public function findByEmail(string $email): ?array
    {
        $query = "SELECT * FROM subscribers WHERE subscriber_email = ?";
        $result = $this->query($query, [$email]);
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    /**
     * Find a subscriber by primary key. Returns the row array or null.
     */
    public function findById(int $id): ?array
    {
        $query  = "SELECT * FROM subscribers WHERE subscriber_id = ?";
        $result = $this->query($query, [$id]);
        return ($result && isset($result[0])) ? $result[0] : null;
    }

    /**
     * Mark a subscriber as having been sent a welcome email.
     */
    public function markAsWelcomed(int $id): bool
    {
        return (bool) $this->updateObject(
            ['subscriber_welcomed' => 1],
            ['subscriber_id' => $id]
        );
    }

    /**
     * Mark a subscriber as inactive (unsubscribed) by email address.
     */
    public function deactivateByEmail(string $email): bool
    {
        return (bool) $this->updateObject(
            ['subscriber_active' => 0],
            ['subscriber_email' => $email]
        );
    }
}
