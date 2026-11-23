<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female']),
        ];
    }
}
