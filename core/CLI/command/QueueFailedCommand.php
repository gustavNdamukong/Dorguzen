<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Helper\Table;

class QueueFailedCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:failed';
    protected static $defaultDescription = 'List all failed jobs';

    protected DGZ_DBAdapter $db;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->db = $container->get(DGZ_DBAdapter::class);
    }

    protected function configure(): void
    {
        $this
            ->setName('queue:failed')
            ->setDescription('List all failed jobs');
    }

    public function handle(): int
    {
        $rows = $this->db->query(
            'SELECT id, queue, attempts, failed_at, exception FROM dgz_failed_jobs ORDER BY failed_at DESC'
        );

        if (empty($rows)) {
            $this->output->writeln('<info>No failed jobs found.</info>');
            return 0;
        }

        $table = new Table($this->output);
        $table->setHeaders([
            'ID',
            'Queue',
            'Attempts',
            'Failed At',
            'Exception'
        ]);

        foreach ($rows as $row) {
            $table->addRow([
                $row['id'],
                $row['queue'],
                $row['attempts'],
                $row['failed_at'],
                $row['exception'],
            ]);
        }

        $table->render();

        return 0;
    }
}