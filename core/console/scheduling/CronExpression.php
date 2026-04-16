<?php

namespace Dorguzen\Core\Console\Scheduling;

class CronExpression
{
    protected string $expression;

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public function __toString(): string
    {
        return $this->expression;
    }

    public function value(): string
    {
        return $this->expression;
    }
}