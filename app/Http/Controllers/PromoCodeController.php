<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PromoCodeController extends Controller
{
    public function index(): View
    {
        $promoCodes = PromoCode::latest('id')->paginate(10);

        return view('promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('promo-codes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:promo_codes,code'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'valid_until' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        PromoCode::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_percent' => (float) $validated['discount_percent'],
            'valid_until' => $validated['valid_until'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route('promo-codes.index')
            ->with('success', 'Промокод успішно створено!');
    }

    public function edit(PromoCode $promoCode): View
    {
        return view('promo-codes.edit', ['promo' => $promoCode]);
    }

    public function update(Request $request, PromoCode $promoCode): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('promo_codes', 'code')->ignore($promoCode->id)],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'valid_until' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $promoCode->update([
            'code' => strtoupper(trim($validated['code'])),
            'discount_percent' => (float) $validated['discount_percent'],
            'valid_until' => $validated['valid_until'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()
            ->route('promo-codes.index')
            ->with('success', 'Промокод успішно оновлено!');
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        $promoCode->delete();

        return redirect()
            ->route('promo-codes.index')
            ->with('success', 'Промокод успішно видалено!');
    }
}
