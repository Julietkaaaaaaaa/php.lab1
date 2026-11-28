<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $tourId = $request->query('tour_id');

        if ($tourId !== null) {
            $tour = Tour::with('hotels')->find((int) $tourId);

            if ($tour === null) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Тур не знайдено.',
                    ], 404);
                }

                abort(404);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $tour->hotels,
                ], 200);
            }

            $hotels = $tour->hotels()->orderBy('stars', 'desc')->paginate(10)->withQueryString();

            return view('hotels.index', compact('hotels'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => Hotel::orderBy('stars', 'desc')->get(),
            ], 200);
        }

        $hotels = Hotel::withCount('tours')->orderBy('stars', 'desc')->paginate(10);

        return view('hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        return view('hotels.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
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

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'created',
                'data' => $hotel,
            ], 201);
        }

        return redirect()
            ->route('hotels.index')
            ->with('success', 'Готель успішно додано!');
    }

    public function show(Request $request, string $id): View|JsonResponse
    {
        $hotel = Hotel::with('tours')->find((int) $id);

        if ($hotel === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Готель не знайдено.',
                ], 404);
            }

            abort(404);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $hotel,
            ], 200);
        }

        return view('hotels.show', compact('hotel'));
    }

    public function edit(string $id): View
    {
        $hotel = Hotel::findOrFail((int) $id);

        return view('hotels.edit', compact('hotel'));
    }

    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $hotel = Hotel::find((int) $id);

        if ($hotel === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Готель не знайдено.',
                ], 404);
            }

            abort(404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'stars' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'city' => ['sometimes', 'required', 'string', 'min:2', 'max:100'],
            'address' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'surcharge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $hotel->update([
            ...$validated,
            'surcharge' => $validated['surcharge'] ?? $hotel->surcharge,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'updated',
                'data' => $hotel,
            ], 200);
        }

        return redirect()
            ->route('hotels.index')
            ->with('success', 'Готель успішно оновлено!');
    }

    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $hotel = Hotel::find((int) $id);

        if ($hotel === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Готель не знайдено.',
                ], 404);
            }

            abort(404);
        }

        $hotel->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'deleted',
            ], 200);
        }

        return redirect()
            ->route('hotels.index')
            ->with('success', 'Готель успішно видалено!');
    }
}
