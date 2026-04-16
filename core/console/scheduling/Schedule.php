<?php

namespace Dorguzen\Core\Console\Scheduling;

class Schedule
{
    /**
     * @var ScheduledTask[]
     */
    protected array $tasks = [];

    public function command(string $command): ScheduledCommand
    {
        $task = new ScheduledCommand($command);
        $this->tasks[] = $task;

        return $task;
    }

    public function job(string $jobClass): ScheduledJob
    {
        $task = new ScheduledJob($jobClass);
        $this->tasks[] = $task;

        return $task;
    }

    public function event(string $eventClass): ScheduledEvent
    {
        $task = new ScheduledEvent($eventClass);
        $this->tasks[] = $task;

        return $task;
    }

    /**
     * Used internally by the runner
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}