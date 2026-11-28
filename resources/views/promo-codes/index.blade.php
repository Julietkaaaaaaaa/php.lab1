@extends('layouts.app')

@section('title', 'Промокоди')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Промокоди</h1>
            <p class="text-secondary mb-0">Знижки та терміни дії для бронювань</p>
        </div>
        <a href="{{ route('promo-codes.create') }}" class="btn btn-accent px-4">+ Додати промокод</a>
    </div>

    <div class="card shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Код</th>
                        <th>Знижка</th>
                        <th>Дійсний до</th>
                        <th>Статус</th>
                        <th class="text-end">Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promoCodes as $promo)
                        <tr>
                            <td class="text-secondary">#{{ $promo->id }}</td>
                            <td class="fw-semibold text-white">{{ $promo->code }}</td>
                            <td class="text-info fw-semibold">-{{ $promo->discount_percent }}%</td>
                            <td>{{ $promo->valid_until?->format('d.m.Y') ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $promo->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $promo->is_active ? 'Активний' : 'Вимкнений' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('promo-codes.edit', $promo) }}" class="btn btn-sm btn-outline-warning">Редаг.</a>
                                <form action="{{ route('promo-codes.destroy', $promo) }}" method="POST" class="d-inline" onsubmit="return confirm('Видалити промокод?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Видалити</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">Промокодів поки немає.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($promoCodes, 'links'))
        <div class="mt-4">
            {{ $promoCodes->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection