@extends('layouts.app')

@section('title', 'Додати тур')

@section('content')
    <div class="card mx-auto shadow-lg" style="max-width: 760px;">
        <div class="card-header py-3">
            <h2 class="h4 mb-0">Створення нового туру</h2>
        </div>
        <div class="card-body p-4">
            @include('partials.validation-alert')

            <form action="{{ route('tours.store') }}" method="POST" novalidate>
                @csrf

                <x-form.input name="title" label="Назва туру" placeholder="Наприклад: Сонячна Греція" />

                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="destination_country" label="Країна призначення" placeholder="Греція" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="start_date" label="Дата початку" type="date" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="duration_days" label="Тривалість (днів)" type="number" min="1" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="base_price" label="Базова ціна (₴)" type="number" step="0.01" min="0" />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Опис туру</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @include('tours._hotels', ['selected' => old('hotels', [])])

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent px-4">Зберегти</button>
                    <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">Скасувати</a>
                </div>
            </form>
        </div>
    </div>
@endsection