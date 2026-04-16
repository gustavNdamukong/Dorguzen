<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\CLI\Command\AbstractCommand;
use Dorguzen\Core\Queues\QueueManager;

class QueueClearCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:clear';
    protected static $defaultDescription = 'Clear all pending jobs from the database queue';

    protected function configure(): void
    {
        $this->setName('queue:clear')->setDescription('Clear all pending jobs');
    }

    protected function handle(): int
    {
        /** @var QueueManager $queue */
        $queue = container(QueueManager::class);
        $count = $queue->clear(); 
        $this->output->writeln("<info>Cleared all jobs from the queue</info>");
        return self::SUCCESS;
    }
}