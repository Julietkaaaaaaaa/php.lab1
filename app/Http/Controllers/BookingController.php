<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\PromoCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function availableHotels(): JsonResponse
    {
        $hotels = Hotel::select(['id', 'name', 'stars', 'city', 'surcharge'])
            ->orderBy('surcharge')
            ->get();

        return response()->json([
            'status' => 'success',
            'hotels' => $hotels,
        ], 200);
    }

    public function calculateTotal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_price' => ['required', 'numeric', 'min:0'],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'tourist_count' => ['required', 'integer', 'min:1', 'max:20'],
            'promo_code' => ['nullable', 'string', 'max:30'],
        ]);

        $hotel = Hotel::findOrFail((int) $validated['hotel_id']);

        // 1. (Базова ціна туру + надбавка за готель) * кількість туристів
        $pricePerTourist = (float) $validated['base_price'] + $hotel->surcharge;
        $subtotal = $pricePerTourist * (int) $validated['tourist_count'];

        // 2. Перевірка активного промокоду в БД та віднімання знижки
        $discountPercent = 0.0;
        if (!empty($validated['promo_code'])) {
            $code = strtoupper(trim((string) $validated['promo_code']));

            $promo = PromoCode::where('code', $code)
                ->where('is_active', true)
                ->whereDate('valid_until', '>=', now())
                ->first();

            if ($promo !== null) {
                $discountPercent = $promo->discount_percent;
            }
        }

        $totalPrice = round($subtotal * (1 - $discountPercent / 100), 2);

        return response()->json([
            'status' => 'success',
            'discount_percent' => $discountPercent,
            'total_price' => $totalPrice,
        ], 200);
    }

    public function index(): JsonResponse
    {
        // Вкладене жадібне завантаження зв'язків 1:1 та 1:M (усуває проблему N+1)
        $bookings = Booking::with([
            'client.user',
            'tour',
            'hotel',
            'promoCode',
            'manager.user',
            'payments',
            'voucher',
        ])->latest('booking_date')->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings,
        ], 200);
    }
}