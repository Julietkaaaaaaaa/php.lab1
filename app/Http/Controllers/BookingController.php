<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\PromoCode;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'promo_code' => ['nullable', 'string', 'max:30'],
            'manager_id' => ['nullable', 'integer', 'exists:managers,id'],
            'tourist_count' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_type' => ['required', 'string', 'max:20'],
        ]);

        $tour = Tour::findOrFail((int) $validated['tour_id']);
        $hotel = Hotel::findOrFail((int) $validated['hotel_id']);

        $discountPercent = 0.0;
        $promoId = null;

        if (! empty($validated['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper(trim((string) $validated['promo_code'])))
                ->where('is_active', true)
                ->whereDate('valid_until', '>=', now())
                ->first();

            if ($promo !== null) {
                $discountPercent = (float) $promo->discount_percent;
                $promoId = $promo->id;
            }
        }

        $totalPrice = $this->computeTotalPrice(
            (float) $tour->base_price,
            (float) $hotel->surcharge,
            (int) $validated['tourist_count'],
            $discountPercent
        );

        $booking = Booking::create([
            'client_id' => (int) $validated['client_id'],
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'promo_code_id' => $promoId,
            'manager_id' => $validated['manager_id'] ?? null,
            'booking_date' => now(),
            'tourist_count' => (int) $validated['tourist_count'],
            'total_price' => $totalPrice,
            'payment_type' => $validated['payment_type'],
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'created',
            'data' => $booking->load(['tour', 'hotel', 'promoCode']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $booking = Booking::with(['client.user', 'tour', 'hotel', 'promoCode', 'manager.user', 'payments', 'voucher'])
            ->find((int) $id);

        if ($booking === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Бронювання не знайдено.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $booking,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $booking = Booking::find((int) $id);

        if ($booking === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Бронювання не знайдено.',
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }

    public function cancel(int $bookingId): JsonResponse
    {
        $booking = Booking::find($bookingId);

        if ($booking === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Бронювання не знайдено.',
            ], 404);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'status' => 'success',
            'cancelled' => true,
            'data' => $booking,
        ], 200);
    }

    public function calculateTotal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_price' => ['required', 'numeric', 'min:0'],
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
            'tourist_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $surcharge = 0.0;
        if (! empty($validated['hotel_id'])) {
            $surcharge = (float) Hotel::findOrFail((int) $validated['hotel_id'])->surcharge;
        }

        $tourists = (int) ($validated['tourist_count'] ?? 1);
        $discount = (float) ($validated['discount'] ?? 0);

        $total = $this->computeTotalPrice(
            (float) $validated['base_price'],
            $surcharge,
            $tourists,
            $discount
        );

        return response()->json([
            'status' => 'success',
            'total_price' => $total,
        ], 200);
    }

    public function applyPromoCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $promo = PromoCode::where('code', strtoupper(trim($validated['code'])))
            ->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->first();

        if ($promo === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Промокод недійсний або прострочений.',
                'discount_percent' => 0,
                'final_amount' => (float) $validated['amount'],
            ], 422);
        }

        $finalAmount = round((float) $validated['amount'] * (1 - (float) $promo->discount_percent / 100), 2);

        return response()->json([
            'status' => 'success',
            'discount_percent' => (float) $promo->discount_percent,
            'final_amount' => $finalAmount,
        ], 200);
    }

    public function confirmByManager(int $bookingId, int $managerId): JsonResponse
    {
        $booking = Booking::find($bookingId);

        if ($booking === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Бронювання не знайдено.',
            ], 404);
        }

        $booking->update([
            'manager_id' => $managerId,
            'status' => 'confirmed',
        ]);

        return response()->json([
            'status' => 'success',
            'confirmed' => true,
            'data' => $booking,
        ], 200);
    }

    private function computeTotalPrice(
        float $basePrice,
        float $surcharge,
        int $tourists,
        float $discountPercent = 0.0
    ): float {
        $subtotal = ($basePrice + $surcharge) * $tourists;

        return round($subtotal * (1 - $discountPercent / 100), 2);
    }
}
