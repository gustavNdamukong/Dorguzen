<?php

namespace Dorguzen\Core\Console\Scheduling;


use DateTime;
use Dorguzen\Core\Console\Scheduling\CronEvaluator;


/**
 * ScheduledTask defines what all scheduled tasks have in common.
 */
abstract class ScheduledTask
{
    protected string $type;

    protected string $target;


    /**
     * Whether this task should prevent overlapping runs.
     */
    protected bool $withoutOverlapping = false;


    protected ?CronExpression $expression = null;

    public function cron(string $expression): static
    {
        $this->expression = new CronExpression($expression);
        return $this;
    }

    public function everyMinute(): static
    {
        return $this->cron('* * * * *');
    }

    public function hourly(): static
    {
        return $this->cron('0 * * * *');
    }

    public function daily(): static
    {
        return $this->cron('0 0 * * *');
    }

    public function dailyAt(string $time): static
    {
        [$hour, $minute] = explode(':', $time);
        return $this->cron((int)$minute.' '.(int)$hour.' * * *');
    }

    public function weekly(): static
    {
        return $this->cron('0 0 * * 0');
    }

    public function monthly(): static
    {
        return $this->cron('0 0 1 * *');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getExpression(): ?CronExpression
    {
        return $this->expression;
    }

    public function isDue(DateTime $now): bool
    {
        if (! $this->expression) {
            return false;
        }

        return CronEvaluator::isDue($this->expression, $now);
    }

    /**
     * Mark this task as non-overlapping.
     *
     * Meaning:
     * - If the previous run has not finished (lock exists),
     *   this run will be skipped.
     */
    public function withoutOverlapping(): static
    {
        $this->withoutOverlapping = true;
        return $this;
    }

    public function preventsOverlapping(): bool
    {
        return $this->withoutOverlapping;
    }

    /**
     * Unique key used for locking this task.
     * Stable across runs.
     */
    public function lockKey(): string
    {
        return sha1($this->type . ':' . $this->target);
    }
}