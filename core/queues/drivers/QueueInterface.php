<?php

namespace Dorguzen\Core\Queues\Drivers;

use Dorguzen\Core\Queues\QueuedJob;

/**
 * QueueInterface is the bridge between sync and async.
 * The synchronous part consists of jobs/events that are run immediately.
 * The asynchronous part consists of jobs/events that are stored in the DB to be run later.
 */
interface QueueInterface
{
    /**
     * push() pushes a job onto the queue.
     * @param object $job can be: events, commands, anything
     * @param mixed $delaySeconds is what allows for future scheduling
     * @return void
     */
    public function push(object $job, ?int $delaySeconds = null): void;

    /**
     * Pop the next available job.
     * QueuedJob is a (metadata) wrapper around a job → lives outside the job
     */
    public function pop(): ?QueuedJob;

    /**
     * Mark job as successfully processed.
     */
    public function acknowledge(QueuedJob $job): void;

    /**
     * Mark job as failed.
     */
    public function fail(QueuedJob $job, \Throwable $e): void;

    public function release(QueuedJob $job, int $delaySeconds = 0): void;

     public function stats(): array;

    public function clear(): bool;
}