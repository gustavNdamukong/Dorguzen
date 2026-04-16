<?php

namespace Dorguzen\Core\Console\Scheduling;

class ScheduledEvent extends ScheduledTask
{
    public function __construct(string $eventClass)
    {
        $this->type   = 'event';
        $this->target = $eventClass;
    }
}