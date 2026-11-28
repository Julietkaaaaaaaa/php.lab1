@extends('layouts.app')

@section('title', $tour->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge badge-neon mb-2">Тур #{{ $tour->id }}</span>
            <h1 class="h2 fw-bold mb-0">{{ $tour->title }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.create', ['tour_id' => $tour->id]) }}" class="btn btn-accent">Забронювати тур</a>
            <a href="{{ route('tours.edit', $tour) }}" class="btn btn-outline-warning">Редагувати</a>
            <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">Назад до списку</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Детальна інформація</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary">Країна призначення</dt>
                        <dd class="col-sm-8">{{ $tour->destination_country }}</dd>

                        <dt class="col-sm-4 text-secondary">Дата початку</dt>
                        <dd class="col-sm-8">{{ $tour->start_date?->format('d.m.Y') ?? '—' }}</dd>

                        <dt class="col-sm-4 text-secondary">Тривалість</dt>
                        <dd class="col-sm-8">{{ $tour->duration_days }} днів</dd>

                        <dt class="col-sm-4 text-secondary">Базова вартість</dt>
                        <dd class="col-sm-8 fs-5 fw-bold text-info">{{ number_format((float) $tour->base_price, 2, '.', ' ') }} ₴</dd>

                        <dt class="col-sm-4 text-secondary">Опис</dt>
                        <dd class="col-sm-8">{{ $tour->description ?: 'Опис відсутній.' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Доступні готелі ({{ $tour->hotels->count() }})</h5>
                </div>
                <div class="card-body">
                    @forelse ($tour->hotels as $hotel)
                        <div class="p-3 mb-2 rounded-3 border border-secondary-subtle">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('hotels.show', $hotel) }}" class="fw-semibold text-info text-decoration-none">{{ $hotel->name }}</a>
                                <span class="badge bg-warning text-dark">{{ $hotel->stars }}★</span>
                            </div>
                            <div class="small text-secondary mt-1">{{ $hotel->city }} · доплата +{{ number_format((float) $hotel->surcharge, 2, '.', ' ') }} ₴</div>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">Готелі ще не прив'язані до цього туру.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection