<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestCommand extends AbstractCommand
{
    protected static $defaultName = 'test';

    protected static $defaultDescription = 'Run the application test suite';


    public function __construct($container)
    {
        parent::__construct($container);
    }

    /*protected function configure(): void
    {
        $this
            ->setName('test')
            ->setDescription('Run the application test suite')
            ->addArgument(
                'args',
                InputArgument::IS_ARRAY,
                'Arguments to pass to PHPUnit'
            );
    }*/

    protected function configure(): void
    {
        $this
            ->setName('test')
            ->setDescription('Run the application test suite')
            ->setHelp('Pass any PHPUnit options directly, e.g. dgz test --filter FooTest')

            // Accept any number of paths
            ->addArgument(
                'paths',
                InputArgument::IS_ARRAY,
                'Test paths'
            )

            // Common PHPUnit passthroughs (minimal set)
            ->addOption('filter', null, InputOption::VALUE_REQUIRED)
            ->addOption('coverage-text', null, InputOption::VALUE_NONE)
            ->addOption('stop-on-failure', null, InputOption::VALUE_NONE);
    }


    protected function handle(): int
    {
        $input = $this->input;
        $output = $this->output;

        $paths = $input->getArgument('paths');

        $phpunit = base_path('vendor/bin/phpunit');
        $config  = base_path('phpunit.xml');

        $command = ['php', $phpunit, '-c', $config];

        if (!file_exists($phpunit)) {
            $output->writeln('<error>PHPUnit is not installed.</error>');
            return SELF::FAILURE;
        }

        if ($input->getOption('filter')) {
            $command[] = '--filter=' . $input->getOption('filter');
        }

        if ($input->getOption('coverage-text')) {
            $command[] = '--coverage-text';
        }

        if ($input->getOption('stop-on-failure')) {
            $command[] = '--stop-on-failure';
        }

        $command = array_merge($command, $paths);

        // Raw argv passthrough (everything after "test")
        $output->writeln('<info>Running tests…</info>');

        // Raw argv passthrough (everything you type in after the "test" in the 'dgz test ...' command)
        passthru(implode(' ', array_map('escapeshellarg', $command)), $exitCode);

        return $exitCode === 0
            ? SELF::SUCCESS
            : SELF::FAILURE;
    }
}