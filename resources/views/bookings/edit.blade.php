@extends('layouts.app')

@section('title', 'Редагувати бронювання')

@section('content')
    <div class="card mx-auto shadow-lg" style="max-width: 720px;">
        <div class="card-header py-3">
            <h2 class="h4 mb-0">Редагування бронювання #{{ $booking->id }}</h2>
        </div>
        <div class="card-body p-4">
            @include('partials.validation-alert')

            <form action="{{ route('bookings.update', $booking) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                @include('bookings._form', ['booking' => $booking, 'tour' => $booking->tour])

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-accent px-4">Оновити</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
@endsection