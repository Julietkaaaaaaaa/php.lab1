<?php

declare(strict_types=1);

namespace App\Tasks;

class Worker extends AbstractWorker
{
    public function calculateSalary(): float
    {
        return $this->baseSalary;
    }
}