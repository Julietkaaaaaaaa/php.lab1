@extends('layouts.app')

@section('title', 'Редагувати готель')

@section('content')
    <div class="card mx-auto shadow-lg" style="max-width: 720px;">
        <div class="card-header py-3"><h2 class="h4 mb-0">Редагування готелю #{{ $hotel->id }}</h2></div>
        <div class="card-body p-4">
            @include('partials.validation-alert')

            <form action="{{ route('hotels.update', $hotel->id) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                @include('hotels._form', ['hotel' => $hotel])

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent px-4">Оновити</button>
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
@endsection