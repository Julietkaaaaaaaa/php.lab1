<?php

declare(strict_types=1);

namespace App\Tasks;

class EnterpriseManager extends DepartmentManager
{
    public function __construct(float $baseSalary, float $shopBonus, float $deptBonus, protected float $enterpriseBonus)
    {
        parent::__construct($baseSalary, $shopBonus, $deptBonus);
    }

    public function calculateSalary(): float
    {
        return parent::calculateSalary() + $this->enterpriseBonus;
    }
}