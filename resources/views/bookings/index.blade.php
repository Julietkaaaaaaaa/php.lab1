@extends('layouts.app')

@section('title', 'Бронювання')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Бронювання</h1>
            <p class="text-secondary mb-0">Заявки клієнтів, обрані тури, готелі та статуси оплати</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn btn-accent px-4">+ Нове бронювання</a>
    </div>

    <div class="card shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Тур</th>
                        <th>Готель</th>
                        <th>Клієнт</th>
                        <th>Туристів</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th class="text-end">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="text-secondary">#{{ $booking->id }}</td>
                            <td class="fw-semibold text-white">{{ $booking->tour->title ?? '—' }}</td>
                            <td>{{ $booking->hotel->name ?? '—' }}</td>
                            <td>{{ $booking->client->user->first_name ?? 'Клієнт' }} {{ $booking->client->user->last_name ?? '' }}</td>
                            <td>{{ $booking->tourist_count }}</td>
                            <td class="text-info fw-semibold">{{ number_format((float) $booking->total_price, 2, '.', ' ') }} ₴</td>
                            <td><span class="badge badge-neon">{{ $booking->status }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-info">Перегляд</a>
                                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning">Редаг.</a>
                                <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Видалити бронювання?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Видалити</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">Бронювань поки немає.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($bookings, 'links'))
        <div class="mt-4">
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection