<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Hotel;
use App\Models\PromoCode;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Booking::with([
            'client.user',
            'tour',
            'hotel',
            'promoCode',
            'manager.user',
            'payments',
            'voucher',
        ])->latest('booking_date');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $query->get(),
            ], 200);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request): View
    {
        $tourId = $request->query('tour_id');
        $tour = $tourId !== null
            ? Tour::with('hotels')->findOrFail((int) $tourId)
            : Tour::with('hotels')->first();

        return view('bookings.create', compact('tour'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'promo_code' => ['nullable', 'string', 'max:30'],
            'tourist_count' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_type' => ['required', 'string', 'in:card,cash'],
        ]);

        $clientId = isset($validated['client_id'])
            ? (int) $validated['client_id']
            : (int) Client::query()->value('id');

        if ($clientId === 0) {
            return back()
                ->withInput()
                ->with('error', 'У базі немає жодного клієнта для оформлення бронювання.');
        }

        $tour = Tour::findOrFail((int) $validated['tour_id']);
        $hotel = Hotel::findOrFail((int) $validated['hotel_id']);

        [$discountPercent, $promoId] = $this->resolvePromoCode($validated['promo_code'] ?? null);

        $totalPrice = $this->computeTotalPrice(
            (float) $tour->base_price,
            (float) $hotel->surcharge,
            (int) $validated['tourist_count'],
            $discountPercent
        );

        $booking = Booking::create([
            'client_id' => $clientId,
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'promo_code_id' => $promoId,
            'manager_id' => null,
            'booking_date' => now(),
            'tourist_count' => (int) $validated['tourist_count'],
            'total_price' => $totalPrice,
            'payment_type' => $validated['payment_type'],
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'created',
                'data' => $booking->load(['tour', 'hotel', 'promoCode']),
            ], 201);
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Бронювання успішно створено!');
    }

    public function show(Request $request, string $id): View|JsonResponse
    {
        $booking = Booking::with(['client.user', 'tour', 'hotel', 'promoCode', 'manager.user', 'payments', 'voucher'])
            ->find((int) $id);

        if ($booking === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Бронювання не знайдено.',
                ], 404);
            }

            abort(404);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $booking,
            ], 200);
        }

        return view('bookings.show', compact('booking'));
    }

    public function edit(string $id): View
    {
        $booking = Booking::with('tour.hotels')->findOrFail((int) $id);

        return view('bookings.edit', [
            'booking' => $booking,
            'tour' => $booking->tour,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $booking = Booking::find((int) $id);

        if ($booking === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Бронювання не знайдено.',
                ], 404);
            }

            abort(404);
        }

        $validated = $request->validate([
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'promo_code' => ['nullable', 'string', 'max:30'],
            'tourist_count' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_type' => ['required', 'string', 'in:card,cash'],
        ]);

        $tour = Tour::findOrFail((int) $validated['tour_id']);
        $hotel = Hotel::findOrFail((int) $validated['hotel_id']);

        [$discountPercent, $promoId] = $this->resolvePromoCode($validated['promo_code'] ?? null);

        $totalPrice = $this->computeTotalPrice(
            (float) $tour->base_price,
            (float) $hotel->surcharge,
            (int) $validated['tourist_count'],
            $discountPercent
        );

        $booking->update([
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'promo_code_id' => $promoId,
            'tourist_count' => (int) $validated['tourist_count'],
            'total_price' => $totalPrice,
            'payment_type' => $validated['payment_type'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'updated',
                'data' => $booking->load(['tour', 'hotel', 'promoCode']),
            ], 200);
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Бронювання успішно оновлено!');
    }

    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $booking = Booking::find((int) $id);

        if ($booking === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Бронювання не знайдено.',
                ], 404);
            }

            abort(404);
        }

        $booking->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'deleted',
            ], 200);
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Бронювання успішно видалено!');
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

    /**
     * @return array{0: float, 1: int|null}
     */
    private function resolvePromoCode(?string $code): array
    {
        if (empty($code)) {
            return [0.0, null];
        }

        $promo = PromoCode::where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->first();

        if ($promo === null) {
            return [0.0, null];
        }

        return [(float) $promo->discount_percent, (int) $promo->id];
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
