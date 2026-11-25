<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tourId = $request->query('tour_id');

        if ($tourId !== null) {
            $tour = Tour::with('hotels')->find((int) $tourId);

            if ($tour === null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Тур не знайдено.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $tour->hotels,
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => Hotel::orderBy('stars', 'desc')->get(),
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'city' => ['required', 'string', 'min:2', 'max:100'],
            'address' => ['required', 'string', 'min:3', 'max:255'],
            'surcharge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $hotel = Hotel::create([
            ...$validated,
            'surcharge' => $validated['surcharge'] ?? 0,
        ]);

        return response()->json([
            'status' => 'created',
            'data' => $hotel,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $hotel = Hotel::with('tours')->find((int) $id);

        if ($hotel === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Готель не знайдено.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $hotel,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $hotel = Hotel::find((int) $id);

        if ($hotel === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Готель не знайдено.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'stars' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'city' => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'address' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'surcharge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $hotel->update($validated);

        return response()->json([
            'status' => 'updated',
            'data' => $hotel,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $hotel = Hotel::find((int) $id);

        if ($hotel === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Готель не знайдено.',
            ], 404);
        }

        $hotel->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}
