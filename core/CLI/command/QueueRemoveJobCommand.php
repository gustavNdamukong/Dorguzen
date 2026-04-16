<?php 

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;

class QueueRemoveJobCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:removejob';
    protected static $defaultDescription = 'Remove a job from the queue';

    protected function configure(): void
    {
        $this
            ->setName('queue:removejob')
            ->setDescription('Remove a job from the queue')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                "The job ID to remove - you can check with the 'queue:jobs' command"
            );
    }

    public function handle(): int
    {
        $id = (int) $this->input->getArgument('id');

        $deleted = $this->container
            ->get(\Dorguzen\Core\DGZ_DBAdapter::class)
            ->execute(
                'DELETE FROM dgz_jobs WHERE id = ?',
                [$id]
            );

        if (! $deleted) {
            $this->output->writeln("<error>No job found with that id.</error>");
            return self::FAILURE;
        }

        $this->output->writeln("<info>✔ Job {$id} successfully deleted from queue.</info>");
        return self::SUCCESS;
    }
}