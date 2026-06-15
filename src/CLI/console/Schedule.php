<?php

/**
 * Dorguzen Scheduler — dorguzen
 *
 * Define all scheduled tasks here. This file is loaded automatically by
 * ScheduleLoader when you run:  php dgz schedule:run
 *
 * Available frequencies:
 *   ->everyMinute()
 *   ->hourly()
 *   ->daily()
 *   ->dailyAt('08:00')
 *   ->weekly()
 *   ->monthly()
 *   ->cron('* * * * *')    — raw cron expression
 *
 * Task types:
 *   $schedule->job('Fully\Qualified\JobClass')
 *   $schedule->command('artisan:command-name')
 *   $schedule->event('Fully\Qualified\EventClass')
 *
 * To prevent a task from running if a previous run is still in progress:
 *   ->withoutOverlapping()
 */

use Dorguzen\Core\Console\Scheduling\Schedule;

return function (Schedule $schedule): void {

    /**
     * Process the outbound newsletter email queue.
     *
     * Picks up to 50 rows from `pending_emails` where status = 'pending',
     * sends each via DGZ_Messenger (PHPMailer / SMTP), then marks them
     * as 'sent' or 'failed'.
     *
     * Running every minute ensures subscribers receive their emails within
     * ~60 seconds of being queued. Adjust to ->hourly() or ->dailyAt('07:00')
     * if you prefer a less frequent send cadence.
     */
    $schedule->job(\Dorguzen\Jobs\SendPendingEmailsJob::class)
             ->everyMinute()
             ->withoutOverlapping();
};
