<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    public function definition(): array
    {
        $countries = ['Туреччина', 'Іспанія', 'Греція', 'Італія', 'Єгипет', 'Чехія', 'Хорватія'];
        $country = fake()->randomElement($countries);

        return [
            'title' => 'Відпочинок: ' . $country . ' (' . fake()->words(2, true) . ')',
            'description' => fake()->paragraph(),
            'destination_country' => $country,
            'start_date' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'duration_days' => fake()->numberBetween(4, 14),
            'base_price' => fake()->randomFloat(2, 12000, 65000),
        ];
    }
}