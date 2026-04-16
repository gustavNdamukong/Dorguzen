<?php

namespace Dorguzen\Core\Console\Scheduling;

class ScheduledCommand extends ScheduledTask
{
    public function __construct(string $command)
    {
        $this->type   = 'command';
        $this->target = $command;
    }
}