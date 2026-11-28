<!DOCTYPE html>
<html lang="uk" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Турагенція')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at top right, #1e1b4b 0%, #090d16 55%);
            min-height: 100vh;
            color: #e2e8f0;
        }
        .navbar-custom {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(56, 189, 248, 0.25);
        }
        .navbar-brand, .text-accent {
            background: linear-gradient(90deg, #38bdf8, #c084fc, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card {
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 1rem;
        }
        .btn-accent {
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
            border: 0;
            color: #fff;
            font-weight: 600;
        }
        .btn-accent:hover { filter: brightness(1.12); color: #fff; }
        .badge-neon {
            background: rgba(168, 85, 247, 0.2);
            color: #f0abfc;
            border: 1px solid rgba(240, 171, 252, 0.35);
        }
        .table th, .table td { white-space: nowrap; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('home') }}">✈ Travel agency</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tours.*') ? 'active text-info' : '' }}" href="{{ route('tours.index') }}">Тури</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hotels.*') ? 'active text-info' : '' }}" href="{{ route('hotels.index') }}">Готелі</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active text-info' : '' }}" href="{{ route('bookings.index') }}">Бронювання</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('promo-codes.*') ? 'active text-info' : '' }}" href="{{ route('promo-codes.index') }}">Промокоди</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @include('partials.alerts')
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>