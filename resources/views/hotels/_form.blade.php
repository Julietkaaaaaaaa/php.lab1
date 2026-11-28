@php $hotel = $hotel ?? null; @endphp

<x-form.input name="name" label="Назва готелю" :value="$hotel?->name ?? ''" placeholder="Grand Resort & Spa" />

<div class="row">
    <div class="col-md-6">
        <x-form.input name="city" label="Місто" :value="$hotel?->city ?? ''" placeholder="Анталія" />
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="stars" class="form-label fw-semibold">Кількість зірок</label>
            @php $starsVal = old('stars', $hotel?->stars ?? ''); @endphp
            <select name="stars" id="stars" class="form-select @error('stars') is-invalid @enderror">
                <option value="">-- Оберіть зірковість --</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ (string) $starsVal === (string) $i ? 'selected' : '' }}>{{ $i }} ★</option>
                @endfor
            </select>
            @error('stars')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <x-form.input name="address" label="Адреса" :value="$hotel?->address ?? ''" placeholder="вул. Приморська, 12" />
    </div>
    <div class="col-md-4">
        <x-form.input name="surcharge" label="Доплата (₴)" type="number" step="0.01" min="0" :value="$hotel?->surcharge ?? 0" />
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label fw-semibold">Опис готелю</label>
    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $hotel?->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>