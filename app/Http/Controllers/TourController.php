<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(): JsonResponse
    {
        // Жадібне завантаження зв'язку M:M з готелями (усуває проблему N+1)
        $tours = Tour::with('hotels')->get();

        return response()->json([
            'status' => 'success',
            'data' => $tours,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'destination_country' => ['required', 'string', 'min:2', 'max:100'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:60'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'hotel_ids' => ['nullable', 'array'],
            'hotel_ids.*' => ['integer', 'exists:hotels,id'],
        ]);

        $tour = Tour::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'destination_country' => $validated['destination_country'],
            'start_date' => $validated['start_date'],
            'duration_days' => (int) $validated['duration_days'],
            'base_price' => (float) $validated['base_price'],
        ]);

        if (!empty($validated['hotel_ids'])) {
            $tour->hotels()->attach($validated['hotel_ids']);
        }

        return response()->json([
            'status' => 'created',
            'data' => $tour->load('hotels'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $tour = Tour::with('hotels')->find((int) $id);

        if ($tour === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Тур із зазначеним ідентифікатором не знайдено.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $tour,
        ], 200);
    }
}