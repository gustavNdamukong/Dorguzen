<?php

namespace Dorguzen\Core\Console\Scheduling;

class ScheduleLoader
{
    public static function load(): Schedule
    {
        $schedule = new Schedule();

        $path = base_path('src/CLI/console/Schedule.php');

        if (file_exists($path)) {
            $definition = require $path;

            if (is_callable($definition)) {
                $definition($schedule);
            }
        }

        return $schedule;
    }
}