<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Tour::with('hotels');

        if ($request->filled('country')) {
            $query->where('destination_country', 'like', '%'.$request->string('country').'%');
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->input('max_price'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $query->latest('start_date')->get(),
            ], 200);
        }

        $tours = $query->latest('start_date')->paginate(5)->withQueryString();

        return view('tours.index', compact('tours'));
    }

    public function create(): View
    {
        $hotels = Hotel::orderBy('name')->get();

        return view('tours.create', compact('hotels'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
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
            'hotels' => ['nullable', 'array'],
            'hotels.*' => ['integer', 'exists:hotels,id'],
        ]);

        $tour = Tour::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'destination_country' => $validated['destination_country'],
            'start_date' => $validated['start_date'],
            'duration_days' => (int) $validated['duration_days'],
            'base_price' => (float) $validated['base_price'],
        ]);

        $hotelIds = $validated['hotels'] ?? $validated['hotel_ids'] ?? [];
        $tour->hotels()->sync($hotelIds);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'created',
                'data' => $tour->load('hotels'),
            ], 201);
        }

        return redirect()
            ->route('tours.index')
            ->with('success', 'Тур успішно створено!');
    }

    public function show(Request $request, string $id): View|JsonResponse
    {
        $tour = Tour::with('hotels')->find((int) $id);

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
                'data' => $tour,
            ], 200);
        }

        return view('tours.show', compact('tour'));
    }

    public function edit(string $id): View
    {
        $tour = Tour::with('hotels')->findOrFail((int) $id);
        $hotels = Hotel::orderBy('name')->get();

        return view('tours.edit', compact('tour', 'hotels'));
    }

    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $tour = Tour::find((int) $id);

        if ($tour === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Тур не знайдено.',
                ], 404);
            }

            abort(404);
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
            'hotels' => ['nullable', 'array'],
            'hotels.*' => ['integer', 'exists:hotels,id'],
        ]);

        $tour->update($validated);

        if ($request->expectsJson()) {
            if (array_key_exists('hotel_ids', $validated)) {
                $tour->hotels()->sync($validated['hotel_ids'] ?? []);
            }

            return response()->json([
                'status' => 'updated',
                'data' => $tour->load('hotels'),
            ], 200);
        }

        $tour->hotels()->sync($validated['hotels'] ?? []);

        return redirect()
            ->route('tours.index')
            ->with('success', 'Тур успішно оновлено!');
    }

    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $tour = Tour::find((int) $id);

        if ($tour === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Тур не знайдено.',
                ], 404);
            }

            abort(404);
        }

        $tour->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'deleted',
            ], 200);
        }

        return redirect()
            ->route('tours.index')
            ->with('success', 'Тур успішно видалено!');
    }

    public function home(): View
    {
        $featuredTours = Tour::with('hotels')->latest('start_date')->paginate(6);

        return view('home', compact('featuredTours'));
    }
}
