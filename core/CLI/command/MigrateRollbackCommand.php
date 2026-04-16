<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\Database\Migrations\MigrationRunner;
use Dorguzen\Core\Database\Migrations\MigrationRepository;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;
use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Input\InputOption;

class MigrateRollbackCommand extends AbstractCommand
{
    protected static $defaultName = 'migrate:rollback';

    protected static $defaultDescription = 'Rollback the last database migration batch';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate:rollback')
            ->setDescription('Rollback the last database migrations')
            ->addOption(
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run without executing them'
            )
            ->addOption(
            'step',
            null,
            InputOption::VALUE_REQUIRED,
            'Rollback the last N migrations'
        );
    }

    protected function handle(): int
    {
        $steps = $this->input->getOption('step');
        $db   = $this->container->get(DGZ_DBAdapter::class);
        $repo = $this->container->get(MigrationRepository::class);
        $migrationLock = $this->container->get(MigrationLockRepository::class);

        $runner = new MigrationRunner(
            $db,
            $repo,
            $migrationLock,
            DGZ_BASE_PATH.'/database/migrations'
        );

        $pretend = $this->input->getOption('pretend');
        $runner->pretend($pretend);

        $runner->runDown($steps ? (int)$steps : null);

        if ($pretend) {
            $this->output->writeln('<info>Because of your --pretend flag, no queries were ran.</info>');
        } else {
            $this->output->writeln('<info>Rollback successfully done.</info>');
        }

        return self::SUCCESS;
    }
} 