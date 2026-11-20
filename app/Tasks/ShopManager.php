<?php

declare(strict_types=1);

namespace App\Tasks;

class ShopManager extends Worker
{
    public function __construct(float $baseSalary, protected float $shopBonus)
    {
        parent::__construct($baseSalary);
    }

    public function calculateSalary(): float
    {
        return parent::calculateSalary() + $this->shopBonus;
    }
}