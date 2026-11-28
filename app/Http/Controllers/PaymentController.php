<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function process(Request $request, int $bookingId): RedirectResponse|JsonResponse
    {
        $booking = Booking::find($bookingId);

        if ($booking === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Бронювання не знайдено.',
                ], 404);
            }

            return back()->with('error', 'Бронювання не знайдено.');
        }

        if ($booking->status === 'paid') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Бронювання вже оплачено.',
                ], 409);
            }

            return back()->with('error', 'Бронювання вже оплачено.');
        }

        $validated = $request->validate([
            'payment_method' => ['nullable', 'string', 'max:30'],
            'simulate_status' => ['nullable', 'in:completed,failed'],
        ]);

        $status = $validated['simulate_status'] ?? 'completed';

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => $validated['payment_method'] ?? $booking->payment_type,
            'transaction_id' => 'TXN-'.strtoupper(Str::random(10)),
            'status' => $status,
        ]);

        if ($status === 'completed') {
            $booking->update(['status' => 'paid']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'simulated' => true,
                'payment' => $payment,
                'booking_status' => $booking->status,
            ], 201);
        }

        return back()->with('success', 'Оплату проведено ('.$payment->transaction_id.').');
    }

    public function webhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => ['required', 'string'],
            'status' => ['required', 'in:completed,failed,pending'],
        ]);

        $payment = Payment::where('transaction_id', $validated['transaction_id'])->first();

        if ($payment === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Транзакцію не знайдено.',
            ], 404);
        }

        $payment->update(['status' => $validated['status']]);

        if ($validated['status'] === 'completed' && $payment->booking) {
            $payment->booking->update(['status' => 'paid']);
        }

        return response()->json([
            'status' => 'received',
            'payment' => $payment,
        ], 200);
    }
}
