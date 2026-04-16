<?php

namespace Dorguzen\Core\CLI\Command;

use DateTime;
use Dorguzen\Core\Console\Scheduling\ScheduleLoader; 
use Dorguzen\Core\Console\Scheduling\Scheduler;

class ScheduleRunCommand extends AbstractCommand
{
    protected static $defaultName = 'schedule:run';
    protected static $defaultDescription = 'Run scheduled tasks';


    public function __construct($container)
    {
        parent::__construct($container);
        // dependencies here
    }

    protected function configure(): void
    {
        $this
            ->setName('schedule:run')
            ->setDescription('Run scheduled tasks');
    }

    public function handle(): int
    {
        $output = $this->output;
        $now = new DateTime();

        $scheduler = new Scheduler($this->container);

        $output->writeln("<info>Running scheduler at ".$now->format('Y-m-d H:i:s').'</info>');

        $schedule = ScheduleLoader::load();

        foreach ($schedule->getTasks() as $task) {
            $output->writeln("<info>Checking task {$task->getTarget()}.</info>");
            if (! $task->isDue($now)) {
                $output->writeln("<comment>{$task->getTarget()} is not due. Skipping ...</comment>");
                continue;
            }

            $output->writeln(
                "▶ Executing {$task->getType()} → {$task->getTarget()}"
            );

            $scheduler->run($task);
        }

        $output->writeln("<info>Schedule run process complete.</info>");

        return 0;
    }
}