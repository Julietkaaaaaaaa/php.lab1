@php
    $promo = $promo ?? null;
    $activeVal = old('is_active', $promo->is_active ?? true);
@endphp

<x-form.input name="code" label="Код промокоду" :value="$promo->code ?? ''" placeholder="SUMMER2026" />

<div class="row">
    <div class="col-md-6">
        <x-form.input name="discount_percent" label="Знижка (%)" type="number" step="0.01" min="0" max="100" :value="$promo->discount_percent ?? 10" />
    </div>
    <div class="col-md-6">
        <x-form.input name="valid_until" label="Дійсний до" type="date" :value="isset($promo?->valid_until) ? $promo->valid_until->format('Y-m-d') : ''" />
    </div>
</div>

<div class="form-check form-switch mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $activeVal ? 'checked' : '' }}>
    <label class="form-check-label fw-semibold" for="is_active">Активний промокод</label>
</div>