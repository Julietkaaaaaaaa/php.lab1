<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromoCode>
 */
class PromoCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('TOUR-####')),
            'discount_percent' => fake()->randomElement([5.00, 10.00, 15.00, 20.00]),
            'valid_until' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}