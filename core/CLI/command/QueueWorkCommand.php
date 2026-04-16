<?php 

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\Queues\QueueManager;
use Symfony\Component\Console\Input\InputOption;

class QueueWorkCommand extends AbstractCommand
{
    protected static $defaultName = 'queue:work';

    protected QueueManager $queue;

    protected static $defaultDescription = 'Start a queue worker';


    protected bool $shouldQuit = false;


    public function __construct($container)
    {
        parent::__construct($container);

        $this->queue = $container->get(QueueManager::class);
    }

    protected function configure(): void
    {
        $this
            ->setName('queue:work')
            ->setAliases(['q:work', 'qw', 'queue:consume', 'q:consume'])
            ->setDescription('Start a queue worker')
            ->setHelp(
                "This command processes queued jobs.\n\n".
                "Examples:\n".
                "  dgz queue:work\n".
                "  dgz queue:work --once\n".
                "  dgz queue:work --sleep=5\n".
                "  dgz queue:work --max-jobs=10\n".
                "  dgz queue:work --timeout=300\n". 
                "                               \n".
                "Exact same command, then you can add the options the same way as above:\n".
                "  dgz q:work\n".
                "  dgz qw\n".
                "  dgz queue:consume\n".
                "  dgz q:consume\n"
            )

            ->addOption(
                'once', 
                null, 
                InputOption::VALUE_NONE, 
                'Process only one job and exit - e.g --once')

            ->addOption(
                'sleep', 
                null, 
                InputOption::VALUE_REQUIRED, 
                'Seconds to sleep when no job is available - default is 1')

            ->addOption(
                'max-jobs', 
                null, 
                InputOption::VALUE_REQUIRED, 
                'Maximum number of jobs to process e.g --max-jobs=2')

            ->addOption(
                'timeout',
                null,
                InputOption::VALUE_OPTIONAL,
                'Max execution time per job (seconds)',
                60
            );
    }


    public function handle(): int
    {
        // when OS requests a stop eg via human Ctrl+C, we will not allow 
        // this worker to run, as it may start a job and leave it unfininished
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);

            pcntl_signal(SIGTERM, fn () => $this->shouldQuit = true);
            pcntl_signal(SIGINT, fn () => $this->shouldQuit = true);
        }
        $output = $this->output;

        $sleep    = (int) $this->input->getOption('sleep');
        $once     = (bool) $this->input->getOption('once');
        $maxJobs  = (int) $this->input->getOption('max-jobs');

        $processed = 0;

        $output->writeln("Queue worker started...");

        while (! $this->shouldQuit) {
            if ($maxJobs > 0 && $processed >= $maxJobs) {
                $output->writeln("Max jobs ({$maxJobs}), processed, exiting...");
                return 0;
            }
            $job = $this->queue->pop();

            if (! $job) {
                $output->writeln("No job in queue, sleeping for {$sleep} seconds...");
                sleep($sleep);
                continue;
            }

            try {
                // enforce max execution time limit per job
                $timeout = (int) ($this->input->getOption('timeout') ?? 60);
                set_time_limit($timeout);

                $job->payload->handle();
                $this->queue->acknowledge($job);
                $output->writeln("✔ Job {$job->id} processed");
            } catch (\Throwable $e) {
                if ($job->attempts < $job->maxAttempts) {
                    // Retry: release back to queue
                    $this->queue->release($job);

                    $output->writeln(
                        "↺ Job {$job->id} released (attempt {$job->attempts}/{$job->maxAttempts})"
                    );
                } else {
                    // Permanent failure
                    $this->queue->fail($job, $e);

                    $output->writeln(
                        "✖ Job {$job->id} permanently failed after {$job->attempts} attempts"
                    );
                }
            }

            $processed++;

            if ($once) {
                $output->writeln("✔ Job ran once as specified. Exiting...");
                return 0;
            }
        }

        $this->output->writeln('Queue worker stopped gracefully.');
        return 0;
    }
}