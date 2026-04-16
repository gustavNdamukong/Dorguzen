<?php

namespace Dorguzen\Core\Console\Scheduling;

class ScheduledJob extends ScheduledTask
{
    public function __construct(string $jobClass)
    {
        $this->type   = 'job';
        $this->target = $jobClass;
    }
}