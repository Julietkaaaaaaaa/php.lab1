<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Hotel;
use App\Models\Manager;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Models\Tour;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //  10 готелів та 5 промокодів
        $hotels = Hotel::factory()->count(10)->create();
        $promoCodes = PromoCode::factory()->count(5)->create();

        // 6 турів + 3 випадкові готелі (зв'язок M:M у hotel_tour)
        $tours = Tour::factory()
            ->count(6)
            ->create()
            ->each(function (Tour $tour) use ($hotels): void {
                $tour->hotels()->attach($hotels->random(3)->pluck('id')->toArray());
            });

        // 5 клієнтів та 2 менеджерів (зв'язки 1:1 з users)
        $clients = Client::factory()->count(5)->create();
        $managers = Manager::factory()->count(2)->create();

        // 8 бронювань (зв'язки 1:M), а до підтверджених  платежі та ваучери
        foreach (range(1, 8) as $index) {
            $tour = $tours->random();
            $hotel = $tour->hotels->first() ?? $hotels->random();
            $touristCount = rand(1, 3);
            $totalPrice = ($tour->base_price + $hotel->surcharge) * $touristCount;

            $booking = Booking::create([
                'client_id' => $clients->random()->id,
                'tour_id' => $tour->id,
                'hotel_id' => $hotel->id,
                'promo_code_id' => $index % 2 === 0 ? $promoCodes->random()->id : null,
                'manager_id' => $managers->random()->id,
                'booking_date' => now()->subDays(rand(1, 15)),
                'tourist_count' => $touristCount,
                'total_price' => $totalPrice,
                'payment_type' => 'card',
                'status' => $index <= 5 ? 'paid' : 'pending',
            ]);

            if ($booking->status === 'paid') {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'payment_method' => 'card',
                    'transaction_id' => 'TXN-'.strtoupper(Str::random(10)),
                    'status' => 'completed',
                ]);

                Voucher::create([
                    'booking_id' => $booking->id,
                    'qr_code_hash' => hash('sha256', 'voucher-'.$booking->id),
                    'pdf_file_path' => 'vouchers/voucher_'.$booking->id.'.pdf',
                    'issued_at' => now(),
                ]);
            }
        }
    }
}
