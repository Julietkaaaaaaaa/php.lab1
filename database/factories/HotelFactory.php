<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hotel>
 */
class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Resort & Spa',
            'description' => fake()->sentence(12),
            'stars' => fake()->numberBetween(3, 5),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'surcharge' => fake()->randomFloat(2, 500, 6000),
        ];
    }
}
