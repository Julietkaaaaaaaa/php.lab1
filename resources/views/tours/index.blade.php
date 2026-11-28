@extends('layouts.app')

@section('title', 'Каталог турів')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Каталог турів</h1>
            <p class="text-secondary mb-0">Усі доступні напрямки та прив'язані готелі</p>
        </div>
        <a href="{{ route('tours.create') }}" class="btn btn-accent px-4">+ Додати новий тур</a>
    </div>

    <div class="card shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>Країна</th>
                        <th>Дата виїзду</th>
                        <th>Днів</th>
                        <th>Готелі (M:M)</th>
                        <th>Базова ціна</th>
                        <th class="text-end">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tours as $tour)
                        <tr>
                            <td class="text-secondary">#{{ $tour->id }}</td>
                            <td class="fw-semibold text-white">{{ $tour->title }}</td>
                            <td>{{ $tour->destination_country }}</td>
                            <td>{{ $tour->start_date?->format('d.m.Y') ?? '—' }}</td>
                            <td>{{ $tour->duration_days }}</td>
                            <td>
                                @forelse ($tour->hotels as $hotel)
                                    <span class="badge badge-neon me-1 mb-1">{{ $hotel->name }} ({{ $hotel->stars }}★)</span>
                                @empty
                                    <span class="text-secondary small">—</span>
                                @endforelse
                            </td>
                            <td class="fw-semibold text-info">{{ number_format((float) $tour->base_price, 2, '.', ' ') }} ₴</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('bookings.create', ['tour_id' => $tour->id]) }}" class="btn btn-sm btn-accent">Забронювати</a>
                                <a href="{{ route('tours.show', $tour) }}" class="btn btn-sm btn-outline-info">Перегляд</a>
                                <a href="{{ route('tours.edit', $tour) }}" class="btn btn-sm btn-outline-warning">Редаг.</a>
                                <form action="{{ route('tours.destroy', $tour) }}" method="POST" class="d-inline" onsubmit="return confirm('Дійсно видалити цей тур?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Видалити</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">Записів не знайдено.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $tours->links('pagination::bootstrap-5') }}
    </div>
@endsection