<?php 

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\Database\Migrations\MigrationRunner;
use Dorguzen\Core\Database\Migrations\MigrationRepository;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;
use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * This command does the folowing:
 *    -Drops all application database tables and re-run all migrations
 *    -Resets migration history (by truncating 'dgz_migrations').
 *        This is because any migration recorded there as ran cannot be re-run.
 *    -Re-runs all migrations
 */
class MigrateFreshCommand extends AbstractCommand
{
    protected static $defaultName = 'migrate:fresh';
    protected static $defaultDescription = 'Drop all tables and re-run all migrations';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate:fresh')
            ->setDescription('Drop all tables and re-run all migrations')
            ->addOption(
                'pretend',
                null,
                InputOption::VALUE_NONE,
                'Show SQL queries without executing them'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the operation to run without confirmation'
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
        $force   = $this->input->getOption('force');
        $runner->pretend($pretend);


        if (!$pretend && !$force) {
            $this->output->writeln('');
            $this->output->writeln('<error>⚠️  WARNING</error>');
            $this->output->writeln(
                '<comment>This will DROP ALL TABLES in your database.</comment>'
            );
            $this->output->writeln('');

            $helper = $this->getHelper('question');

            $question = new ConfirmationQuestion(
                'Are you sure you want to continue? (yes/no) ',
                false
            );

            if (!$helper->ask($this->input, $this->output, $question)) {
                $this->output->writeln('<info>Migration fresh aborted.</info>');
                return self::SUCCESS;
            }
        }

        if ($pretend) {
            $this->output->writeln('<comment>Pretending migration fresh.</comment>');
            $runner->fresh();
        } else {
            $this->output->writeln('<info>Dropping all tables.</info>');
            $runner->fresh();
        }

        if (!$pretend)
        {
            $this->output->writeln('<info>Database refreshed successfully.</info>');
        }

        return self::SUCCESS;
    }
}