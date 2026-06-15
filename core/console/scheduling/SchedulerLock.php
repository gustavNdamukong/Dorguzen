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
        $now     = new DateTime();
        $expires = (clone $now)->modify("+{$ttlSeconds} seconds");

        // Clear any expired lock for this key before attempting to acquire
        try {
            $this->db->query(
                "DELETE FROM dgz_scheduled_task_locks WHERE task_key = ? AND expires_at < ?",
                [$key, $now->format('Y-m-d H:i:s')]
            );
        } catch (\Throwable) {
            // Ignore — table may not support this yet
        }

        try {
            $this->db->insert('dgz_scheduled_task_locks', [
                'task_key'   => $key,
                'locked_at'  => $now->format('Y-m-d H:i:s'),
                'expires_at' => $expires->format('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            // MySQL 1062 = duplicate key → lock already held by another run, skip silently
            if ((int) $e->getCode() === 1062) {
                return false;
            }

            // Any other error (e.g. table missing, connection lost) is a real infrastructure
            // problem that should not be silently swallowed as a false "already locked" signal
            throw new \RuntimeException(
                'SchedulerLock: failed to acquire lock for "' . $key . '": ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
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