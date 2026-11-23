<?php

namespace Database\Factories;

use App\Models\Manager;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Manager>
 */
class ManagerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role' => 'manager']),
            'position' => fake()->randomElement(['Менеджер з туризму', 'Старший консультант', 'Керівник напрямку']),
        ];
    }
}
