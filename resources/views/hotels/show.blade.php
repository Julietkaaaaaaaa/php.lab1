@extends('layouts.app')

@section('title', $hotel->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-warning text-dark mb-2">{{ $hotel->stars }}★</span>
            <h1 class="h2 fw-bold mb-0">{{ $hotel->name }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hotels.edit', $hotel->id) }}" class="btn btn-outline-warning">Редагувати</a>
            <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary">Назад до списку</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">Характеристики готелю</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary">Місто</dt>
                        <dd class="col-sm-8">{{ $hotel->city }}</dd>

                        <dt class="col-sm-4 text-secondary">Адреса</dt>
                        <dd class="col-sm-8">{{ $hotel->address }}</dd>

                        <dt class="col-sm-4 text-secondary">Доплата до туру</dt>
                        <dd class="col-sm-8 text-info fw-bold">+{{ number_format((float) $hotel->surcharge, 2, '.', ' ') }} ₴</dd>

                        <dt class="col-sm-4 text-secondary">Опис</dt>
                        <dd class="col-sm-8">{{ $hotel->description ?: 'Опис відсутній.' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">Тури з цим готелем ({{ $hotel->tours->count() }})</h5></div>
                <div class="card-body">
                    @forelse ($hotel->tours as $tour)
                        <div class="p-3 mb-2 rounded-3 border border-secondary-subtle">
                            <a href="{{ route('tours.show', $tour->id) }}" class="fw-semibold text-info text-decoration-none">{{ $tour->title }}</a>
                            <div class="small text-secondary mt-1">{{ $tour->destination_country }} · {{ $tour->duration_days }} дн.</div>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">Цей готель ще не додано до жодного туру.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection