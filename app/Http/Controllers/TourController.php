<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Тимчасове сховище турів
     */
    private array $tours = [
        [
            'id' => 1,
            'title' => 'Сонячна Туреччина: All Inclusive',
            'destination_country' => 'Туреччина',
            'start_date' => '2026-06-15',
            'duration_days' => 7,
            'base_price' => 28500.0,
        ],
        [
            'id' => 2,
            'title' => 'Екскурсійний вікенд у Празі',
            'destination_country' => 'Чехія',
            'start_date' => '2026-07-02',
            'duration_days' => 4,
            'base_price' => 14200.0,
        ],
        [
            'id' => 3,
            'title' => 'Відпочинок на узбережжі Іспанії',
            'destination_country' => 'Іспанія',
            'start_date' => '2026-08-10',
            'duration_days' => 10,
            'base_price' => 46000.0,
        ],
    ];

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->tours,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'destination_country' => ['required', 'string', 'min:2', 'max:100'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:60'],
            'base_price' => ['required', 'numeric', 'min:0'],
        ]);

        $newTour = [
            'id' => count($this->tours) + 1,
            'title' => $validated['title'],
            'destination_country' => $validated['destination_country'],
            'start_date' => $validated['start_date'],
            'duration_days' => (int) $validated['duration_days'],
            'base_price' => (float) $validated['base_price'],
        ];

        $this->tours[] = $newTour;

        return response()->json([
            'status' => 'created',
            'data' => $newTour,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $tourId = (int) $id;

        foreach ($this->tours as $tour) {
            if ($tour['id'] === $tourId) {
                return response()->json([
                    'status' => 'success',
                    'data' => $tour,
                ], 200);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Тур із зазначеним ідентифікатором не знайдено.',
        ], 404);
    }
}
