<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoucherController extends Controller
{
    public function generate(Request $request, int $bookingId): RedirectResponse|JsonResponse
    {
        $booking = Booking::with(['client.user', 'tour', 'hotel', 'promoCode', 'manager.user'])->find($bookingId);

        if ($booking === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Бронювання не знайдено.',
                ], 404);
            }

            return back()->with('error', 'Бронювання не знайдено.');
        }

        $clientName = trim(
            ($booking->client?->user?->first_name ?? '').' '.($booking->client?->user?->last_name ?? '')
        );
        if ($clientName === '') {
            $clientName = 'Клієнт #'.($booking->client_id ?? '—');
        }

        $managerName = trim(
            ($booking->manager?->user?->first_name ?? '').' '.($booking->manager?->user?->last_name ?? '')
        );
        if ($managerName === '') {
            $managerName = $booking->manager_id ? 'Менеджер #'.$booking->manager_id : 'Не призначено';
        }

        $lines = [
            '=== ВАУЧЕР БРОНЮВАННЯ ===',
            'ID бронювання: #'.$booking->id,
            'Клієнт: '.$clientName.' (ID: '.($booking->client_id ?? '—').')',
            'Менеджер: '.$managerName,
            'Тур: '.($booking->tour?->title ?? '—').' (ID: '.($booking->tour_id ?? '—').')',
            'Готель: '.($booking->hotel?->name ?? '—').' (ID: '.($booking->hotel_id ?? '—').')',
            'Промокод: '.($booking->promoCode?->code ?? 'Не застосовано'),
            'Дата бронювання: '.($booking->booking_date?->format('Y-m-d H:i:s') ?? '—'),
            'Кількість туристів: '.$booking->tourist_count,
            'Спосіб оплати: '.$booking->payment_type,
            'Статус: '.$booking->status,
            '------------------------',
            'TOTAL (Загальна сума): '.number_format((float) $booking->total_price, 2, '.', ' ').' грн',
        ];

        $content = implode(PHP_EOL, $lines).PHP_EOL;
        $path = 'vouchers/voucher_'.$booking->id.'.txt';
        Storage::disk('local')->put($path, $content);

        $voucher = Voucher::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'qr_code_hash' => hash('sha256', 'voucher-'.$booking->id.'-'.$booking->total_price),
                'pdf_file_path' => $path,
                'issued_at' => now(),
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'generated',
                'voucher' => $voucher,
            ], 201);
        }

        return back()->with('success', 'Ваучер сформовано!');
    }

    public function download(int $voucherId): StreamedResponse|RedirectResponse|JsonResponse
    {
        $voucher = Voucher::find($voucherId);

        if ($voucher === null || ! Storage::disk('local')->exists($voucher->pdf_file_path)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Файл ваучера не знайдено.',
                ], 404);
            }

            return back()->with('error', 'Файл ваучера не знайдено.');
        }

        return Storage::disk('local')->download($voucher->pdf_file_path);
    }
}
