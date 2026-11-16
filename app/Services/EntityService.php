<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class EntityService
{
    public function calculateTotalPrice(float $price, int $persons): float
    {
        return $price * $persons;
    }

    /**
     * Застосування знижки до ціни туру
     *
     * @throws Exception
     */
    public function applyDiscount(float $price, float $discount): float
    {
        if ($discount < 0 || $discount > 100) {
            throw new Exception('Неприпустиме значення знижки!');
        }

        return $price - ($price * ($discount / 100));
    }
}
