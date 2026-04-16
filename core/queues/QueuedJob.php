<?php

namespace Dorguzen\Core\Queues;

/**
 * This is not the event/job, but the wrapper (envelop) around it.
 * It sort of holds meta data about the job to be passed around.
 * It reflects the exact same field in the DB jobs table, because
 * that's what it represents.
 */
class QueuedJob
{
    public function __construct(
        public string $queue,
        public object $payload,
        public int $attempts = 0,
        public int $maxAttempts = 3,
        public ?int $availableAt = null,
        public ?int $id = null
    ) {}
}