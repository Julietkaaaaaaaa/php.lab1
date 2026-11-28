@php
    $booking = $booking ?? null;
    $currentTour = $tour ?? ($booking?->tour ?? null);
    $availableHotels = $currentTour
        ? ($currentTour->relationLoaded('hotels') ? $currentTour->hotels : $currentTour->hotels()->get())
        : ($hotels ?? collect());
    $selectedHotelId = old('hotel_id', $booking?->hotel_id ?? '');
    $selectedPromoCode = old('promo_code', $booking?->promoCode?->code ?? '');
    $selectedPaymentType = old('payment_type', $booking?->payment_type ?? 'card');
@endphp

@if ($currentTour)
    <input type="hidden" name="tour_id" value="{{ $currentTour->id }}">
    <div class="p-3 mb-4 rounded-3 border border-info-subtle" style="background: rgba(56, 189, 248, 0.08);">
        <div class="small text-info fw-semibold">Обраний тур</div>
        <div class="fs-5 fw-bold text-white">{{ $currentTour->title }}</div>
        <div class="small text-secondary mt-1">
            {{ $currentTour->destination_country }} · {{ $currentTour->duration_days }} дн. · Базова ціна:
            <span class="text-info fw-semibold">{{ number_format((float) $currentTour->base_price, 0, '.', ' ') }} ₴</span>
        </div>
    </div>
    @error('tour_id')
        <div class="text-danger small mb-3">{{ $message }}</div>
    @enderror
@endif

<div class="mb-3">
    <label for="hotel_id" class="form-label fw-semibold">Готель</label>
    <select name="hotel_id" id="hotel_id" class="form-select @error('hotel_id') is-invalid @enderror">
        <option value="">-- Оберіть готель --</option>
        @forelse ($availableHotels as $hotel)
            <option value="{{ $hotel->id }}" {{ (string) $selectedHotelId === (string) $hotel->id ? 'selected' : '' }}>
                {{ $hotel->name }} ({{ $hotel->stars }}★, доплата +{{ number_format((float) $hotel->surcharge, 0, '.', ' ') }} ₴)
            </option>
        @empty
            <option value="" disabled>До цього туру готелі не прив'язані</option>
        @endforelse
    </select>
    @error('hotel_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4">
        <x-form.input name="tourist_count" label="Кількість туристів" type="number" min="1" max="20" :value="$booking?->tourist_count ?? 1" />
    </div>

    <div class="col-md-4">
        <x-form.input name="promo_code" label="Промокод" :value="$selectedPromoCode" placeholder="Введіть промокод" />
    </div>

    <div class="col-md-4 mb-3">
        <label for="payment_type" class="form-label fw-semibold">Спосіб оплати</label>
        <select name="payment_type" id="payment_type" class="form-select @error('payment_type') is-invalid @enderror">
            <option value="card" {{ $selectedPaymentType === 'card' ? 'selected' : '' }}>Картка</option>
            <option value="cash" {{ $selectedPaymentType === 'cash' ? 'selected' : '' }}>Готівка</option>
        </select>
        @error('payment_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>