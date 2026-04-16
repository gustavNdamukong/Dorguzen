<?php 

namespace Dorguzen\Core\CLI\Command;


use Dorguzen\Core\Database\Migrations\MigrationRunner;
use Dorguzen\Core\Database\Migrations\MigrationRepository;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends AbstractCommand
{
    protected static $defaultName = 'migrate';

    protected static $defaultDescription = 'Migrate your DB tables';


    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate')
            ->setDescription('Migrate your DB tables')
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Run a specific migration file'
            )
            ->addOption(
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Dump the SQL queries that would be run without executing them'
            );
    }

    protected function handle(): int
    { 
        $db = $this->container->get(\Dorguzen\Core\DGZ_DBAdapter::class);
        $migrationLock = $this->container->get(MigrationLockRepository::class);
        $repository = new MigrationRepository($db);
        
        $runner = new MigrationRunner(
            $db,
            $repository,
            $migrationLock,
            DGZ_BASE_PATH.'/database/migrations'
        );

        $file = $this->input->getOption('file');
        $pretend = $this->input->getOption('pretend');

        $runner->pretend($pretend);

        if ($file) {
            $runner->runSingle($file);
            $outputMessage = "<info>Migration {$file} processed.</info>";
        } else {
            $runner->runUp();
            $outputMessage = "<info>Migrations completed successfully.</info>";
        }
        
        if ($pretend) {
            $this->output->writeln('<info>Because of your --pretend flag, no queries were ran.</info>');
        } else {
            $this->output->writeln($outputMessage);
        }
        
        return self::SUCCESS;
    }
}