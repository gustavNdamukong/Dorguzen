<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;

class QueueForgetCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:forget';
    protected static $defaultDescription = 'Permanently delete a failed job';

    protected function configure(): void
    {
        $this
            ->setName('queue:forget')
            ->setDescription('Delete a failed job permanently')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The failed job ID to delete'
            );
    }

    public function handle(): int
    {
        $id = (int) $this->input->getArgument('id');

        $deleted = $this->container
            ->get(\Dorguzen\Core\DGZ_DBAdapter::class)
            ->execute(
                'DELETE FROM dgz_failed_jobs WHERE id = ?',
                [$id]
            );

        if (! $deleted) {
            $this->output->writeln("<error>Failed job {$id} not found.</error>");
            return self::FAILURE;
        }

        $this->output->writeln("<info>✔ Failed job {$id} permanently deleted.</info>");
        return self::SUCCESS;
    }
}