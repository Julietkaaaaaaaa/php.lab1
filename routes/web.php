<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 1. Службовий маршрут перевірки стану системи (Health Check)
Route::get('/health', fn () => response()->json([
    'status' => 'OK',
    'service' => 'Turahentsiya API',
    'timestamp' => now(),
]));

// Ресурсна група маршрутів для турів
Route::prefix('tours')->name('tours.')->group(function () {
    Route::get('/', [TourController::class, 'index'])->name('index');
    Route::post('/', [TourController::class, 'store'])
        ->middleware('manager.access')
        ->name('store');
    Route::get('/{id}', [TourController::class, 'show'])->whereNumber('id')->name('show');
});

// Маршрути контролера бронювань
Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/hotels', [BookingController::class, 'availableHotels'])->name('hotels');
    Route::post('/calculate', [BookingController::class, 'calculateTotal'])->name('calculate');
});
