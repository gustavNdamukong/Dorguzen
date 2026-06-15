<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

/**
 * Class PendingEmails
 *
 * Represents the `pending_emails` table.
 * Stores outbound newsletter emails waiting to be processed by the scheduler.
 */
class PendingEmails extends DGZ_Model
{
    protected $_columns = [];

    protected $data = [];

    protected $_hasParent = [];

    protected $_hasChild = [];

    protected $table = 'pending_emails';

    protected $primaryKey = 'id';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Fetch pending emails up to the given limit, oldest first.
     */
    public function getPendingEmails(int $limit = 50): array
    {
        $sql = "SELECT * FROM pending_emails WHERE status = 'pending' ORDER BY created_at ASC LIMIT ?";
        return $this->query($sql, [$limit]) ?: [];
    }

    /**
     * Mark a single row as successfully sent.
     */
    public function markSent(int $id): bool
    {
        return (bool) $this->updateObject(
            ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s')],
            ['id' => $id]
        );
    }

    /**
     * Mark a single row as failed and increment the try counter.
     */
    public function markFailed(int $id): bool
    {
        $sql = "UPDATE pending_emails SET status = 'failed', tries = tries + 1, last_attempt_at = NOW() WHERE id = ?";
        return (bool) $this->query($sql, [$id]);
    }

    /**
     * Increment the try counter without changing status.
     * Useful for transient failures where you want to retry on the next run.
     */
    public function incrementTries(int $id): bool
    {
        $sql = "UPDATE pending_emails SET tries = tries + 1, last_attempt_at = NOW() WHERE id = ?";
        return (bool) $this->query($sql, [$id]);
    }

    /**
     * Count of rows still in pending status.
     */
    public function pendingCount(): int
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM pending_emails WHERE status = 'pending'";
        $result = $this->query($sql);
        return isset($result[0]['cnt']) ? (int) $result[0]['cnt'] : 0;
    }
}
