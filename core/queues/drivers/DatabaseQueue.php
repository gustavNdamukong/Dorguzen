<?php 

namespace Dorguzen\Core\Queues\Drivers;

use Dorguzen\Core\Queues\Drivers\QueueInterface;
use Dorguzen\Core\Queues\QueuedJob;
use Dorguzen\Core\DGZ_DBAdapter as DB;
use RuntimeException;
use Throwable;


/**
 * DatabaseQueue represents the asynchronous part of jobs/events 
 * handling. It is for jobs/events that will be queued in the 
 * DB to be run later e.g. by a CLI queue worker.
 */
class DatabaseQueue implements QueueInterface
{
    public function __construct(
        protected DB $db
    ) {}

    public function push(object $job, ?int $delaySeconds = null): void
    {
        $availableAt = time() + ($delaySeconds ?? 0);

        $this->db->insert('dgz_jobs', [
            'queue'        => 'default',
            'payload'      => serialize($job),
            'attempts'     => 0,
            'max_attempts' => 3,
            'available_at' => date('Y-m-d H:i:s', $availableAt),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }


    public function pop(): ?QueuedJob
    {
        $now = date('Y-m-d H:i:s');

        try {
            // 1. Find next available job
            $rows = $this->db->query(
                "SELECT * FROM dgz_jobs
                WHERE reserved_at IS NULL
                AND available_at <= ?
                ORDER BY id ASC
                LIMIT 1",
                [$now]
            );

            if (empty($rows)) {
                echo "No jobs in the queue \n"; 
                return null;
            }

            $job = $rows[0];
        }
        catch(Throwable $e) {
        echo "Error: {$e->getMessage()}";
        }

        // 2. Attempt to reserve it (optimistic locking)
        $updated = $this->db->execute(
            "UPDATE dgz_jobs
            SET reserved_at = ?, attempts = attempts + 1
            WHERE id = ? AND reserved_at IS NULL",
            [$now, $job['id']]
        );

        if (! $updated || $this->db->getAffectedRows() > 0) { 
            // Another worker claimed it
            return null;
        }

        // 3. Hydrate QueuedJob
        return new QueuedJob(
            queue: (string) $job['queue'],
            payload: unserialize($job['payload']),
            attempts: (int) $job['attempts'] + 1,
            maxAttempts: (int) $job['max_attempts'],
            availableAt: (int) $job['available_at'],
            id: (int) $job['id'],
        );
    }


    public function acknowledge(QueuedJob $job): void
    {
        $this->db->execute(
            "DELETE FROM dgz_jobs WHERE id = ?",
            [$job->id]
        );
    }



    public function release(QueuedJob $job, int $delaySeconds = 5): void
    {
        // Poison-job protection
        if ($job->attempts >= $job->maxAttempts) {
            $this->fail(
                $job,
                new RuntimeException(
                    "Job exceeded max attempts ({$job->maxAttempts})"
                )
            );
            return;
        }

        $availableAt = date(
            'Y-m-d H:i:s',
            time() + $delaySeconds
        );

        $this->db->execute(
            "UPDATE dgz_jobs
            SET
                reserved_at = NULL,
                attempts = ?,
                available_at = ?
            WHERE id = ?",
            [
                $job->attempts,
                $availableAt,
                $job->id,
            ]
        );
    }



    public function fail(QueuedJob $job, Throwable $e): void
    {
        $this->db->insert('dgz_failed_jobs', [
            'queue'            => $job->queue,
            'payload'          => serialize($job->payload),
            'exception'        => $e->getMessage(),
            'exception_trace'  => $e->getTraceAsString(),
            'attempts'         => $job->attempts,
            'failed_at'        => date('Y-m-d H:i:s'),
        ]);

        // Remove job from active queue
        $this->db->execute(
            'DELETE FROM dgz_jobs WHERE id = ?', [$job->id]
        );
    }


    public function stats(): array
    {
        $pending = (int) $this->db->query('SELECT COUNT(*) as c FROM dgz_jobs')[0]['c'];
        $failed = (int) $this->db->query('SELECT COUNT(*) as c FROM dgz_failed_jobs')[0]['c'];
        return ['pending' => $pending, 'failed' => $failed];
    }


    public function clear(): Bool
    {
        return $this->db->execute('DELETE FROM dgz_jobs');
    }
}