<?php 

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\Database\Migrations\MigrationRunner;
use Dorguzen\Core\Database\Migrations\MigrationRepository;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;
use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Input\InputOption;


/**
 * Rollback all migrations and re-run them
 */
class MigrateRefreshCommand extends AbstractCommand
{
    protected static $defaultName = 'migrate:refresh';
    protected static $defaultDescription = 'Rollback all migrations and re-run them';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate:refresh')
            ->setDescription('Rollback all migrations and re-run them')
            ->addOption(
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run without executing them'
            );
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
            DGZ_BASE_PATH . '/database/migrations'
        );
        $pretend = $this->input->getOption('pretend');
        $runner->pretend($pretend);

        $this->output->writeln('<info>Refreshing migrations.</info>');

        $runner->runDownAll();
        $runner->runUp();

        if ($pretend) {
            $this->output->writeln('<info>Because of your --pretend flag, no queries were ran.</info>');
        } else {
            $this->output->writeln('<info>Migrations refreshed successfully.</info>');
        }

        return self::SUCCESS;
    }
}