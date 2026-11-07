<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('titulo', 'Catálogo de servicios') | {{ config('app.name', 'BOXLY') }}</title>

    {{-- Misma tipografía y sistema visual que el sitio PHP principal (docs/DISENO.md) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"
          integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="{{ asset('css/boxly.css') }}" rel="stylesheet">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>

<div class="bx-lv-notice">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-layers" aria-hidden="true"></i> <strong>Módulo Laravel — TP10</strong> · Catálogo de servicios con Eloquent y Blade</span>
        <span class="d-none d-md-inline">Laravel {{ app()->version() }}</span>
    </div>
</div>

<header class="sticky-top">
    <nav class="navbar navbar-expand-md bx-navbar" aria-label="Principal">
        <div class="container">
            <a class="navbar-brand" href="{{ route('servicios.index') }}">
                <span class="bx-logo-mark" aria-hidden="true">BX</span>
                <span>BOXLY</span>
                <small class="d-none d-sm-inline">Servicios</small>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                    aria-controls="menu" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav ms-auto align-items-md-center gap-md-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" href="{{ route('servicios.index') }}"
                           @if (request()->routeIs('servicios.index')) aria-current="page" @endif>
                            <i class="bi bi-wrench-adjustable" aria-hidden="true"></i> Servicios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('api.servicios.index') }}" target="_blank" rel="noopener">
                            <i class="bi bi-braces" aria-hidden="true"></i> API JSON
                        </a>
                    </li>
                    <li class="nav-item ms-md-2">
                        <a class="btn btn-bx-primary btn-sm w-100" href="{{ route('servicios.create') }}">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo servicio
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="contenido" tabindex="-1">
    @yield('encabezado')

    <section class="bx-section">
        <div class="container">
            @if (session('ok'))
                <div class="alert alert-success alert-dismissible fade show" role="status">
                    {{ session('ok') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar aviso"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar aviso"></button>
                </div>
            @endif

            @yield('contenido')
        </div>
    </section>
</main>

<footer class="bx-footer">
    <div class="container d-flex justify-content-between flex-wrap gap-2">
        <span>© {{ date('Y') }} BOXLY · Taller mecánico en Salta</span>
        <span>TP10 parte B · Laravel {{ app()->version() }} · <a href="{{ route('api.servicios.index') }}">API JSON</a></span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
