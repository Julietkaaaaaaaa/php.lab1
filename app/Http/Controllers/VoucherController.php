<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoucherController extends Controller
{
    public function generate(int $bookingId): JsonResponse
    {
        $booking = Booking::find($bookingId);

        if ($booking === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Бронювання не знайдено.',
            ], 404);
        }

        $path = 'vouchers/voucher_'.$booking->id.'.txt';
        Storage::disk('local')->put($path, "Voucher for booking #{$booking->id}, total: {$booking->total_price}");

        $voucher = Voucher::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'qr_code_hash' => hash('sha256', 'voucher-'.$booking->id),
                'pdf_file_path' => $path,
                'issued_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'generated',
            'voucher' => $voucher,
        ], 201);
    }

    public function download(int $voucherId): StreamedResponse|JsonResponse
    {
        $voucher = Voucher::find($voucherId);

        if ($voucher === null || ! Storage::disk('local')->exists($voucher->pdf_file_path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Файл ваучера не знайдено.',
            ], 404);
        }

        return Storage::disk('local')->download($voucher->pdf_file_path);
    }
}
