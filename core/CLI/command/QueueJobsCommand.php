<?php 

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Helper\Table;

class QueueJobsCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:jobs';
    protected static $defaultDescription = 'List all jobs';

    protected DGZ_DBAdapter $db;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->db = $container->get(DGZ_DBAdapter::class);
    }

    protected function configure(): void
    {
        $this
            ->setName('queue:jobs')
            ->setDescription('List all jobs');
    }

    public function handle(): int
    {
        $rows = $this->db->query(
            'SELECT id, queue, payload, attempts, max_attempts, reserved_at, available_at, failed_at, created_at 
                FROM dgz_jobs ORDER BY created_at ASC'
        );

        if (empty($rows)) {
            $this->output->writeln('<info>No jobs in queue.</info>');
            return 0;
        }

        $table = new Table($this->output);
        $table->setHeaders([
            'ID',
            'Queue',
            'Payload',
            'Attempts',
            'Max Attempts',
            'Reserved At',
            'Available At',
            'Failed At',
            'Created At'
        ]);

        foreach ($rows as $row) {
            $table->addRow([
                $row['id'],
                $row['queue'],
                substr($row['payload'], 0, 50),
                $row['attempts'],
                $row['max_attempts'],
                $row['reserved_at'],
                $row['available_at'],
                $row['failed_at'],
                $row['created_at'],
            ]);
        }

        $table->render();

        return 0;
    }
}