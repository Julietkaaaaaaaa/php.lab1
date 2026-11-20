<?php

declare(strict_types=1);

namespace App\Tasks;

class DepartmentManager extends ShopManager
{
    public function __construct(float $baseSalary, float $shopBonus, protected float $deptBonus)
    {
        parent::__construct($baseSalary, $shopBonus);
    }

    public function calculateSalary(): float
    {
        return parent::calculateSalary() + $this->deptBonus;
    }
}
