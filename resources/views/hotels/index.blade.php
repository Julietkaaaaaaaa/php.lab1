@extends('layouts.app')

@section('title', 'Каталог готелів')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Готелі</h1>
            <p class="text-secondary mb-0">База партнерських готелів та доплати за проживання</p>
        </div>
        <a href="{{ route('hotels.create') }}" class="btn btn-accent px-4">+ Додати готель</a>
    </div>

    <div class="card shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>Зірки</th>
                        <th>Місто</th>
                        <th>Адреса</th>
                        <th>Доплата</th>
                        <th class="text-end">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotels as $hotel)
                        <tr>
                            <td class="text-secondary">#{{ $hotel->id }}</td>
                            <td class="fw-semibold text-white">{{ $hotel->name }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $hotel->stars }}★</span></td>
                            <td>{{ $hotel->city }}</td>
                            <td class="text-secondary">{{ $hotel->address }}</td>
                            <td class="text-info fw-semibold">+{{ number_format((float) $hotel->surcharge, 2, '.', ' ') }} ₴</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('hotels.show', $hotel->id) }}" class="btn btn-sm btn-outline-info">Перегляд</a>
                                <a href="{{ route('hotels.edit', $hotel->id) }}" class="btn btn-sm btn-outline-warning">Редаг.</a>
                                <form action="{{ route('hotels.destroy', $hotel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Видалити цей готель?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Видалити</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">Готелів не знайдено.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $hotels->links('pagination::bootstrap-5') }}
    </div>
@endsection