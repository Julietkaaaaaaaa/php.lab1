<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Hotel;
use App\Models\Manager;
use App\Models\PromoCode;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'tour_id' => Tour::factory(),
            'hotel_id' => Hotel::factory(),
            'promo_code_id' => PromoCode::factory(),
            'manager_id' => Manager::factory(),
            'booking_date' => now(),
            'tourist_count' => fake()->numberBetween(1, 4),
            'total_price' => fake()->randomFloat(2, 15000, 90000),
            'payment_type' => fake()->randomElement(['card', 'iban', 'cash']),
            'status' => fake()->randomElement(['pending', 'confirmed', 'paid']),
        ];
    }
}
