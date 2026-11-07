@extends('layouts.app')

@section('titulo', $servicio->nombre)

@section('encabezado')
<section class="bx-page-hero">
    <div class="container">
        <nav aria-label="Ruta de navegación">
            <ol class="breadcrumb bx-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $servicio->nombre }}</li>
            </ol>
        </nav>
        <h1>{{ $servicio->nombre }}</h1>
        <p>Detalle del servicio y su uso en los turnos del taller.</p>
    </div>
</section>
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <article class="bx-card">
            <div class="bx-detail-head">
                <h2>Precio base</h2>
                <span class="bx-price-tag">{{ $servicio->precio_formateado }}</span>
            </div>

            <p class="text-muted mb-0">{{ $servicio->descripcion ?? 'Sin descripción.' }}</p>

            <dl class="bx-dl">
                <div>
                    <dt>ID</dt>
                    <dd>{{ $servicio->id }}</dd>
                </div>
                <div>
                    <dt>Turnos que lo usan</dt>
                    <dd>
                        <span class="badge {{ $servicio->turnos_count > 0 ? 'bx-badge-uso' : 'bx-badge-libre' }}">{{ $servicio->turnos_count }}</span>
                    </dd>
                </div>
                <div>
                    <dt>Creado</dt>
                    <dd>{{ $servicio->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Última modificación</dt>
                    <dd>{{ $servicio->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>

            <div class="bx-actions-bar">
                <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-bx-primary">
                    <i class="bi bi-pencil" aria-hidden="true"></i> Editar
                </a>
                <form action="{{ route('servicios.destroy', $servicio) }}" method="POST"
                      onsubmit="return confirm(@js('¿Eliminar el servicio «' . $servicio->nombre . '»?'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-bx-danger" @disabled($servicio->turnos_count > 0)
                            @if ($servicio->turnos_count > 0) aria-describedby="avisoBorrado" @endif>
                        <i class="bi bi-trash" aria-hidden="true"></i> Eliminar
                    </button>
                </form>
                <a href="{{ route('servicios.index') }}" class="btn btn-bx-outline-dark ms-md-auto">Volver al catálogo</a>
            </div>
            @if ($servicio->turnos_count > 0)
                <p class="form-text text-muted mt-3 mb-0" id="avisoBorrado">
                    <i class="bi bi-info-circle" aria-hidden="true"></i> No se puede eliminar: tiene turnos asociados.
                </p>
            @endif
        </article>
    </div>
</div>
@endsection
