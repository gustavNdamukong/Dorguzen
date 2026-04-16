<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\Database\Seeders\SeederRunner;
use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Input\InputOption;
use RuntimeException;

class DbSeedCommand extends AbstractCommand
{
    protected static $defaultName = 'db:seed';
    protected static $defaultDescription = 'Seed the database with records';

    protected function configure(): void
    {
        $this
            ->setName('db:seed')
            ->setDescription('Seed the database with records')
            ->addOption(
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Show SQL queries without executing them'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force seeding in protected environments'
            )
            ->addOption(
                'class',
                null,
                InputOption::VALUE_OPTIONAL,
                'Run a specific seeder class'
            )
            ->addOption(
            'all',
            null,
            InputOption::VALUE_NONE,
            'Run the DatabaseSeeder (seed everything)'
        );
    }

    protected function handle(): int
    {
        $db = $this->container->get(DGZ_DBAdapter::class);

        $runner = new SeederRunner(
            $db,
            DGZ_BASE_PATH . '/database/seeders'
        );
        $pretend = $this->input->getOption('pretend');

        // pass some options to the seeder runner
        $runner->pretend($pretend);
        $runner->force($this->input->getOption('force'));

        $class = $this->input->getOption('class') ?? 'DatabaseSeeder';

        // ---------------------------------------------------------
        // Determine which seeder should run
        //
        // Rules:
        // 1. --all        → run DatabaseSeeder
        // 2. --class=Foo  → run FooSeeder only
        // 3. no option    → default to DatabaseSeeder
        // ---------------------------------------------------------

        $all   = $this->input->getOption('all');

        // Guard against ambiguous intent
        if ($all && $class) {
            throw new RuntimeException(
                'You cannot use --all and --class together. ' .
                '--all already runs DatabaseSeeder.'
            );
        }

        // If --all was explicitly requested, we force DatabaseSeeder
        if ($all) {
            $class = 'DatabaseSeeder';
        }

        // If neither option was provided, default behavior
        $class = $class ?? 'DatabaseSeeder';

        $this->output->writeln("<info>Seeding database:</info> {$class}");

        $runner->run($class);

        if ($pretend) {
            $this->output->writeln('<info>Because of your --pretend flag, no queries were ran.</info>');
        } else {
            $this->output->writeln('<info>Database seeding completed.</info>');
        }

        return self::SUCCESS;
    }
}