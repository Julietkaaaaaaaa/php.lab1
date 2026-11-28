@extends('layouts.app')

@section('title', 'Головна — Turahentsiya')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 fw-bold mb-0">Доступні тури</h2>
    </div>

    <div class="row g-4">
        @forelse ($featuredTours as $tour)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="badge badge-neon">{{ $tour->destination_country }}</span>
                            <span class="text-secondary small">{{ $tour->duration_days }} дн.</span>
                        </div>

                        <h3 class="h5 fw-bold text-white">{{ $tour->title }}</h3>
                        <p class="text-secondary small flex-grow-1">
                            {{ \Illuminate\Support\Str::limit($tour->description ?? 'Опис туру уточнюється.', 110) }}
                        </p>

                        <div class="mb-3">
                            <div class="small text-secondary mb-1">Готелі:</div>
                            @forelse ($tour->hotels as $hotel)
                                <span class="badge bg-dark border border-secondary-subtle text-light me-1 mb-1">
                                    {{ $hotel->name }} ({{ $hotel->stars }}★)
                                </span>
                            @empty
                                <span class="text-secondary small">Обирається на етапі бронювання</span>
                            @endforelse
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary-subtle">
                            <div>
                                <div class="text-secondary small">Від</div>
                                <div class="fs-5 fw-bold text-info">{{ number_format((float) $tour->base_price, 0, '.', ' ') }} ₴</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('tours.show', $tour) }}" class="btn btn-sm btn-outline-secondary">Деталі</a>
                                <a href="{{ route('bookings.create', ['tour_id' => $tour->id]) }}" class="btn btn-sm btn-accent">Забронювати</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-4 text-center text-secondary">
                    Турів поки немає. Додайте перший тур через меню «Тури».
                </div>
            </div>
        @endforelse
        <div class="mt-4">
            {{ $featuredTours->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection