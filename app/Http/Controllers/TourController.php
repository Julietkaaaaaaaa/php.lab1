<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tour::with('hotels');

        if ($request->filled('country')) {
            $query->where('destination_country', 'like', '%'.$request->string('country').'%');
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->input('max_price'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->latest('start_date')->get(),
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

        $tour = Tour::create($validated);

        if (! empty($validated['hotel_ids'])) {
            $tour->hotels()->sync($validated['hotel_ids']);
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
                'message' => 'Тур не знайдено.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $tour,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $tour = Tour::find((int) $id);

        if ($tour === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Тур не знайдено.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'destination_country' => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'start_date' => ['sometimes', 'required', 'date'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1', 'max:60'],
            'base_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'hotel_ids' => ['nullable', 'array'],
            'hotel_ids.*' => ['integer', 'exists:hotels,id'],
        ]);

        $tour->update($validated);

        if (array_key_exists('hotel_ids', $validated)) {
            $tour->hotels()->sync($validated['hotel_ids'] ?? []);
        }

        return response()->json([
            'status' => 'updated',
            'data' => $tour->load('hotels'),
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $tour = Tour::find((int) $id);

        if ($tour === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Тур не знайдено.',
            ], 404);
        }

        $tour->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}
