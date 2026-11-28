@php $selected = $selected ?? []; @endphp
<div class="mb-4">
    <label class="form-label fw-semibold d-block">Готелі туру</label>
    <div class="p-3 rounded-3 border border-secondary-subtle">
        @forelse ($hotels as $hotel)
            <div class="form-check form-check-inline mb-2">
                <input class="form-check-input" type="checkbox" name="hotels[]"
                       id="hotel_{{ $hotel->id }}" value="{{ $hotel->id }}"
                       {{ in_array($hotel->id, $selected) ? 'checked' : '' }}>
                <label class="form-check-label" for="hotel_{{ $hotel->id }}">
                    {{ $hotel->name }} ({{ $hotel->city }}, {{ $hotel->stars }}★)
                </label>
            </div>
        @empty
            <span class="text-secondary small">Готелів поки немає.</span>
        @endforelse
    </div>
    @error('hotels')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>