@extends('layouts.app')

@section('title', 'Новий промокод')

@section('content')
    <div class="card mx-auto shadow-lg" style="max-width: 640px;">
        <div class="card-header py-3"><h2 class="h4 mb-0">Створення промокоду</h2></div>
        <div class="card-body p-4">
            @include('partials.validation-alert')

            <form action="{{ route('promo-codes.store') }}" method="POST" novalidate>
                @csrf

                @include('promo-codes._form')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent px-4">Зберегти</button>
                    <a href="{{ route('promo-codes.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
@endsection