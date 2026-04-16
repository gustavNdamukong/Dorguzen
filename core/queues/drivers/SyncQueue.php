<?php

namespace Dorguzen\Core\Queues\Drivers;

use Dorguzen\Core\Queues\Drivers\QueueInterface;
use Dorguzen\Core\Queues\QueuedJob;

/**
 * SyncQueue is for jobs/events that will be run straight away
 */
class SyncQueue implements QueueInterface
{
    public function push(object $job, ?int $delaySeconds = null): void
    {
        // Execute immediately
        $this->execute($job);
    }

    public function pop(): ?QueuedJob
    {
        return null; // No-op
    }

    public function acknowledge(QueuedJob $job): void
    {
        // No-op
    }

    public function fail(QueuedJob $job, \Throwable $e): void
    {
        throw $e;
    }

    protected function execute(object $job): void
    {
        // This will be delegated later
        if (method_exists($job, 'handle')) {
            $job->handle();
        }
    }

    public function release(QueuedJob $job, int $delaySeconds = 0): void
    {
        echo "About to release a job in SyncQueue \n";
    }


    public function stats(): array
    {
        // not used
        return [];
    }


    public function clear(): bool
    {
        // not used
        return 1;
    }
}