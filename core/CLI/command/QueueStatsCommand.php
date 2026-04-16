<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\CLI\Command\AbstractCommand;
use Dorguzen\Core\Queues\QueueManager;

class QueueStatsCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:stats';
    protected static $defaultDescription = 'Display statistics about the queues';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this->setName('queue:stats')->setDescription('Show queue statistics');
    }

    protected function handle(): int
    {
        /** @var QueueManager $queue */
        $queue = container(QueueManager::class);
        $stats = $queue->stats(); // We'll add this to DatabaseQueue

        $this->output->writeln("<info>Pending jobs: {$stats['pending']}</info>");
        $this->output->writeln("<info>Failed jobs: {$stats['failed']}</info>");
        return self::SUCCESS;
    }
}