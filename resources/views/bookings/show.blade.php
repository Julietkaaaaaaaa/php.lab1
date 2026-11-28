@extends('layouts.app')

@section('title', 'Бронювання #' . $booking->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge badge-neon mb-2">Статус: {{ $booking->status }}</span>
            <h1 class="h2 fw-bold mb-0">Бронювання #{{ $booking->id }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-warning">Редагувати</a>
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Назад до списку</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h5 class="mb-0">Деталі бронювання</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary">Тур</dt>
                        <dd class="col-sm-8">{{ $booking->tour->title ?? '—' }}</dd>

                        <dt class="col-sm-4 text-secondary">Готель</dt>
                        <dd class="col-sm-8">{{ $booking->hotel->name ?? '—' }}</dd>

                        <dt class="col-sm-4 text-secondary">Клієнт</dt>
                        <dd class="col-sm-8">{{ $booking->client->user->first_name ?? 'Клієнт' }} {{ $booking->client->user->last_name ?? '' }}</dd>

                        <dt class="col-sm-4 text-secondary">Менеджер</dt>
                        <dd class="col-sm-8">{{ $booking->manager->user->first_name ?? 'Не призначено' }} {{ $booking->manager->user->last_name ?? '' }}</dd>

                        <dt class="col-sm-4 text-secondary">Кількість туристів</dt>
                        <dd class="col-sm-8">{{ $booking->tourist_count }}</dd>

                        <dt class="col-sm-4 text-secondary">Промокод</dt>
                        <dd class="col-sm-8">{{ $booking->promoCode->code ?? 'Не застосовано' }}</dd>

                        <dt class="col-sm-4 text-secondary">Спосіб оплати</dt>
                        <dd class="col-sm-8">{{ $booking->payment_type }}</dd>

                        <dt class="col-sm-4 text-secondary">Загальна сума</dt>
                        <dd class="col-sm-8 fs-5 fw-bold text-info">{{ number_format((float) $booking->total_price, 2, '.', ' ') }} ₴</dd>
                    </dl>
                </div>
            </div>
        </div>

      <div class="col-lg-4">
    <div class="card shadow-sm h-100">
        <div class="card-header"><h5 class="mb-0">Оплати та ваучер</h5></div>
        <div class="card-body">
            <p class="small text-secondary mb-1">Оплат у системі:</p>
            <p class="fw-semibold mb-3">{{ $booking->payments->count() }}</p>

            @if ($booking->status !== 'paid')
                <form action="{{ route('payments.process', $booking->id) }}" method="POST" class="mb-4">
                    @csrf
                    <button type="submit" class="btn btn-accent w-100">Оплатити бронювання</button>
                </form>
            @else
                <div class="alert alert-success py-2 small mb-4">Бронювання оплачено</div>
            @endif

            <p class="small text-secondary mb-1">Ваучер:</p>
            <p class="fw-semibold mb-3">
                {{ $booking->voucher?->qr_code_hash ? 'Сформовано' : 'Ще не сформовано' }}
            </p>

            <form action="{{ route('vouchers.generate', $booking->id) }}" method="POST" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-outline-info w-100">Сформувати ваучер</button>
            </form>

            @if ($booking->voucher)
                <a href="{{ route('vouchers.download', $booking->voucher->id) }}" class="btn btn-outline-light w-100">
                    Завантажити ваучер
                </a>
            @endif
        </div>
    </div>
</div>
    </div>
@endsection