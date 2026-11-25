<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// 1. Службовий маршрут перевірки стану системи (Health Check)
Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'service' => 'Turahentsiya API',
    'timestamp' => now(),
]));

// 2. Ресурсні маршрути для основних сутностей
Route::resource('tours', TourController::class)
    ->except(['create', 'edit'])
    ->middlewareFor(['store', 'update', 'destroy'], 'role:admin');

Route::resource('hotels', HotelController::class)
    ->except(['create', 'edit'])
    ->middlewareFor(['store', 'update', 'destroy'], 'role:admin');

Route::resource('bookings', BookingController::class)
    ->only(['index', 'store', 'show', 'destroy']);

// 3. Додаткові маршрути бронювань
Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])
        ->whereNumber('booking')
        ->name('cancel');
    Route::post('/calculate', [BookingController::class, 'calculateTotal'])
        ->name('calculate');
    Route::post('/promo', [BookingController::class, 'applyPromoCode'])
        ->name('promo');
    Route::post('/{booking}/confirm/{managerId}', [BookingController::class, 'confirmByManager'])
        ->whereNumber(['booking', 'managerId'])
        ->middleware('role:manager')
        ->name('confirm');
});

// 4. Оплати та ваучери
Route::post('/payments/{bookingId}/process', [PaymentController::class, 'process'])
    ->whereNumber('bookingId')
    ->name('payments.process');
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])
    ->name('payments.webhook');

Route::post('/vouchers/{bookingId}/generate', [VoucherController::class, 'generate'])
    ->whereNumber('bookingId')
    ->name('vouchers.generate');
Route::get('/vouchers/{voucherId}/download', [VoucherController::class, 'download'])
    ->whereNumber('voucherId')
    ->name('vouchers.download');
