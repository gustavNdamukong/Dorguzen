<?php

namespace Dorguzen\Core\Console\Scheduling;

use DateTime;

class CronEvaluator
{
    public static function isDue(CronExpression $expression, DateTime $now): bool
    {
        [$min, $hour, $day, $month, $week] = explode(' ', $expression->value());

        return
            self::matches($min, (int)$now->format('i')) &&
            self::matches($hour, (int)$now->format('G')) &&
            self::matches($day, (int)$now->format('j')) &&
            self::matches($month, (int)$now->format('n')) &&
            self::matches($week, (int)$now->format('w'));
    }

    protected static function matches(string $expr, int $value): bool
    {
        if ($expr === '*') {
            return true;
        }

        if (str_contains($expr, ',')) {
            return in_array($value, array_map('intval', explode(',', $expr)), true);
        }

        if (str_contains($expr, '-')) {
            [$start, $end] = array_map('intval', explode('-', $expr));
            return $value >= $start && $value <= $end;
        }

        if (str_contains($expr, '/')) {
            [$base, $step] = explode('/', $expr);
            return $base === '*' && $value % (int)$step === 0;
        }

        return (int)$expr === $value;
    }
}