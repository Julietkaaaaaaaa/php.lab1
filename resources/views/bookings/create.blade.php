@extends('layouts.app')

@section('title', 'Бронювання туру')

@section('content')
    <div class="card mx-auto shadow-lg" style="max-width: 720px;">
        <div class="card-header py-3">
            <h2 class="h4 mb-0">Бронювання туру</h2>
        </div>
        <div class="card-body p-4">
            @include('partials.validation-alert')

            <form action="{{ route('bookings.store') }}" method="POST" novalidate>
                @csrf

                @include('bookings._form', ['tour' => $tour ?? null])

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-accent px-4">Підтвердити бронювання</button>
                    <a href="{{ $tour ? route('tours.show', $tour) : route('bookings.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
@endsection