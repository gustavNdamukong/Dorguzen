<?php

namespace Dorguzen\Core\Console\Scheduling;


use Dorguzen\Core\DGZ_DBAdapter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;


/**
 * Responsible for executing a scheduled due task. 
 * This is the brain behind scheduling in Dorguzen.
 *
 * IMPORTANT:
 * The scheduler does NOT contain business logic.
 * It only decides WHEN to run and delegates the execution to 
 * existing DGZ subsystems (CLI, queues, events).
 * It enforces overlap rules
 * Conceptually:
 *      -Schedule → definition of scheduled tasks by dev (in src/CLI/console/Schedule.php)
 *      -ScheduleLoader → loads user-defined scheduled tasks in Schedule (events, jobs, commands)
 *      -ScheduleRunCommand → orchestration (by running Scheduler)
 *      -Scheduler → actual execution by dispatching tasks on schedule
 * 
 * The job answers the WHAT
 * The sceduler answers WHEN
 * The queue answers the HOW 
 * 
 */
class Scheduler
{
    protected $container;

    protected SchedulerLock $lock;

    protected ArgvInput $input;

    protected ConsoleOutput $output;

    public function __construct($container)
    {
        $this->container = $container;
        $this->lock = new SchedulerLock($container->get(DGZ_DBAdapter::class));
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
    }

    /**
     * Dispatch the task based on its type.
     */
    public function run(ScheduledTask $task): void
    {
        // -----------------------------------------
        // Overlap prevention
        // -----------------------------------------
        if ($task->preventsOverlapping()) {
            if (! $this->lock->acquire($task->lockKey())) {
                return; // Skip silently
            }
        }

        try {
            match ($task->getType()) {
                'command' => $this->runCommand($task),
                'job'     => $this->runJob($task),
                'event'   => $this->runEvent($task),
            };
        } finally {
            if ($task->preventsOverlapping()) {
                $this->lock->release($task->lockKey());
            }
        }
    }


    /**
     * Execute a DGZ command via Symfony Console,
     * without exposing Symfony to the developer.
     * This is basicaslly how you can call a Symfony console command
     * in code on the fly, outside of creating a full command class
     * that a user can run. 
     */
    protected function runCommand(ScheduledTask $task): void
    {
        /** @var \Dorguzen\Core\CLI\Application $app */ 
        $app = $this->container->get(\Dorguzen\Core\CLI\Application::class);

        $input = new ArrayInput([
            'command' => $task->getTarget(),
        ]);

        // Silent output; scheduler should not spam console
        $app->getConsole()->run($input, new NullOutput());
        $this->output->writeln("<info>Comand {$task->getTarget()} successfully ran</info>");
    }


    /**
     * Dispatch a job into the existing queue system.
     */
    protected function runJob(ScheduledTask $task): void
    {
        $this->output->writeln("<info>Dispatching job {$task->getTarget()}</info>"); 
        $job = new ($task->getTarget());

        dispatch($job);
    }

    /**
     * Fire an application event.
     */
    protected function runEvent(ScheduledTask $task): void
    {
        $this->output->writeln("<info>Dispatching Event {$task->getTarget()}</info>"); 
        event(new ($task->getTarget()));
    }
}