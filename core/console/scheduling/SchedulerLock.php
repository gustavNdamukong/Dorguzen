<?php

namespace Dorguzen\Core\Console\Scheduling;

use DateTime;

/**
 * Handles DB-based scheduler locks.
 *
 * WHY DB?
 * - No external dependencies
 * - Auditable
 * - Strong consistency via unique constraint
 */
class SchedulerLock
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Attempt to acquire a lock.
     * Returns true if lock acquired, false otherwise.
     */
    public function acquire(string $key, int $ttlSeconds = 60): bool
    {
        $now = new DateTime();
        $expires = (clone $now)->modify("+{$ttlSeconds} seconds");

        try {
            $this->db->insert('dgz_scheduled_task_locks', [
                'task_key'   => $key,
                'locked_at'  => $now->format('Y-m-d H:i:s'),
                'expires_at'=> $expires->format('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            // Duplicate key → already locked
            return false;
        }
    }

    /**
     * Release a lock explicitly.
     */
    public function release(string $key): void
    {
        $this->db->delete('dgz_scheduled_task_locks', [
            'task_key' => $key,
        ]);
    }
}