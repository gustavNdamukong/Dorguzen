<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_DBAdapter;

class QueueRetryCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:retry';
    protected static $defaultDescription = 'Retry a failed job or all failed jobs. Pass the id e.g. queue:retry <id>';

    protected DGZ_DBAdapter $db;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->db = $container->get(DGZ_DBAdapter::class);
    }

    protected function configure(): void
    {
        $this
            ->setName('queue:retry')
            ->setDescription('Retry a failed job or all failed jobs. Pass the id e.g. queue:retry <id>')
            ->addArgument(
                'id',
                \Symfony\Component\Console\Input\InputArgument::REQUIRED,
                'Failed job ID or "all"'
            );
    }

    public function handle(): int
    {
        $id = $this->input->getArgument('id');

        if ($id === 'all') {
            $failedJobs = $this->db->query(
                'SELECT * FROM dgz_failed_jobs'
            );
        } else {
            $failedJobs = $this->db->query(
                'SELECT * FROM dgz_failed_jobs WHERE id = ?',
                [$id]
            );
        }

        if (empty($failedJobs)) {
            $this->output->writeln('<error>No failed jobs found.</error>');
            return 1;
        }

        foreach ($failedJobs as $job) {
            $this->db->insert('dgz_jobs', [
                'queue'        => $job['queue'],
                'payload'      => serialize($job['payload']),
                'attempts'     => 0,
                'max_attempts' => 3,
                'reserved_at'  => null, 
                'available_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            $this->db->execute(
                'DELETE FROM dgz_failed_jobs WHERE id = ?',
                [$job['id']]
            );

            $this->output->writeln("↻ Retried job {$job['id']}");
        }

        return 0;
    }
}