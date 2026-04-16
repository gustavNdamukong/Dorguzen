<?php

namespace Dorguzen\Core\CLI\Command;


use Dorguzen\Core\Database\Migrations\MigrationRunner;
use Dorguzen\Core\Database\Migrations\MigrationRepository;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;
use Dorguzen\Core\DGZ_DBAdapter;

class MigrateStatusCommand extends AbstractCommand
{
    protected static $defaultName = 'migrate:status';
    protected static $defaultDescription = 'View the status of your migrations';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate:status')
            ->setDescription('View the status of your migrations');
    }



    protected function handle(): int
    {
        $db   = $this->container->get(DGZ_DBAdapter::class);
        $repo = $this->container->get(MigrationRepository::class);
        $migrationLock = $this->container->get(MigrationLockRepository::class);


        $runner = new MigrationRunner(
            $db,
            $repo,
            $migrationLock,
            DGZ_BASE_PATH.'/database/migrations'
        );

        $rows = $runner->status();

        $this->output->writeln('');
        $this->output->writeln(
            sprintf(
                "%-50s %-8s %-10s",
                'Migration',
                'Batch',
                'Status'
            )
        );
        $this->output->writeln(str_repeat('-', 72));

        if ($rows)
        {
            foreach ($rows as $row) {
                $this->output->writeln(
                    sprintf(
                        "%-50s %-8s %-10s",
                        $row['migration'],
                        $row['batch'] ?? '-',
                        $row['status']
                    )
                );
            }
        } else {
            $this->output->writeln('<comment>No Migrations have been run yet.</comment>');
            return self::SUCCESS;
        }

        $this->output->writeln('');

        return self::SUCCESS;
    }
} 