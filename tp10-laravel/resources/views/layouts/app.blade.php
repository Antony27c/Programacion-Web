<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Catálogo de servicios') | {{ config('app.name', 'BOXLY') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bx-ink: #0F172A;
            --bx-acento: #0052FF;
            --bx-fondo: #F8F9FA;
            --bx-exito: #10B981;
            --bs-primary: #0052FF;
            --bs-primary-rgb: 0, 82, 255;
            --bs-success: #10B981;
            --bs-success-rgb: 16, 185, 129;
        }
        body { background: var(--bx-fondo); color: var(--bx-ink); min-height: 100vh; display: flex; flex-direction: column; }
        main { flex: 1; }
        .navbar-bx { background: var(--bx-ink); }
        .marca-bx { font-weight: 800; letter-spacing: .04em; }
        .marca-bx .logo {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem; border-radius: .5rem; margin-right: .5rem;
            background: var(--bx-acento); color: #fff; font-size: .9rem;
        }
        .btn-primary { --bs-btn-bg: #0052FF; --bs-btn-border-color: #0052FF; --bs-btn-hover-bg: #0043d1; --bs-btn-hover-border-color: #0043d1; --bs-btn-active-bg: #003bb8; }
        .btn-outline-primary { --bs-btn-color: #0052FF; --bs-btn-border-color: #0052FF; --bs-btn-hover-bg: #0052FF; --bs-btn-hover-border-color: #0052FF; }
        .text-ink { color: var(--bx-ink) !important; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(15, 23, 42, .08); border-radius: .75rem; }
        .table thead th { font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .badge-exito { background: var(--bx-exito); }
        footer { color: #64748b; font-size: .875rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-bx">
    <div class="container">
        <a class="navbar-brand marca-bx" href="{{ route('servicios.index') }}">
            <span class="logo">BX</span>BOXLY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                aria-controls="menu" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" href="{{ route('servicios.index') }}">
                        <i class="bi bi-wrench-adjustable"></i> Servicios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('api.servicios.index') }}" target="_blank">
                        <i class="bi bi-braces"></i> API JSON
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if (session('ok'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('ok') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @yield('contenido')
</main>

<footer class="py-3 border-top bg-white">
    <div class="container d-flex justify-content-between flex-wrap gap-2">
        <span>BOXLY · Taller mecánico</span>
        <span>TP10 · Laravel {{ app()->version() }}</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
