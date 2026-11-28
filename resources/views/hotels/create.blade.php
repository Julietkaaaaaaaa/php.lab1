@extends('layouts.app')

@section('title', 'Додати готель')

@section('content')
    <div class="card mx-auto shadow-lg" style="max-width: 720px;">
        <div class="card-header py-3"><h2 class="h4 mb-0">Додавання нового готелю</h2></div>
        <div class="card-body p-4">
            @include('partials.validation-alert')

            <form action="{{ route('hotels.store') }}" method="POST" novalidate>
                @csrf

                @include('hotels._form')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent px-4">Зберегти</button>
                    <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
@endsection