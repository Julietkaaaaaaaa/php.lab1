<?php

declare(strict_types=1);

namespace App\Tasks;

abstract class AbstractWorker
{
    public function __construct(protected float $baseSalary) {}

    abstract public function calculateSalary(): float;
}