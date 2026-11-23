<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private array $hotels = [
        1 => ['id' => 1, 'name' => 'Sunny Beach Resort (3★)', 'surcharge' => 0.0],
        2 => ['id' => 2, 'name' => 'Grand Blue Hotel (4★)', 'surcharge' => 4500.0],
        3 => ['id' => 3, 'name' => 'Royal Palace & Spa (5★)', 'surcharge' => 9500.0],
    ];

    private array $promoCodes = [
        'SUMMER10' => 10.0,
        'VIP20' => 20.0,
    ];

    public function availableHotels(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'hotels' => array_values($this->hotels),
        ], 200);
    }

    public function calculateTotal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_price' => ['required', 'numeric', 'min:0'],
            'hotel_id' => ['required', 'integer', 'in:1,2,3'],
            'tourist_count' => ['required', 'integer', 'min:1', 'max:20'],
            'promo_code' => ['nullable', 'string', 'max:30'],
        ]);

        // 1. (Базова ціна туру + надбавка за готель) * кількість туристів
        $pricePerTourist = (float) $validated['base_price'] + $this->hotels[(int) $validated['hotel_id']]['surcharge'];
        $subtotal = $pricePerTourist * (int) $validated['tourist_count'];

        // 2. Перевірка промокоду та віднімання знижки
        $code = isset($validated['promo_code']) ? strtoupper(trim($validated['promo_code'])) : null;
        $discountPercent = ($code !== null && isset($this->promoCodes[$code])) ? $this->promoCodes[$code] : 0.0;

        $totalPrice = round($subtotal * (1 - $discountPercent / 100), 2);

        return response()->json([
            'status' => 'success',
            'discount_percent' => $discountPercent,
            'total_price' => $totalPrice,
        ], 200);
    }
}
