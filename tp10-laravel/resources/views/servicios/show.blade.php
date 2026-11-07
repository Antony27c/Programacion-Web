@extends('layouts.app')

@section('titulo', $servicio->nombre)

@section('contenido')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $servicio->nombre }}</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h4 mb-0 text-ink">{{ $servicio->nombre }}</h1>
                    <span class="fs-4 fw-bold text-primary">{{ $servicio->precio_formateado }}</span>
                </div>

                <p class="text-muted">{{ $servicio->descripcion ?? 'Sin descripción.' }}</p>

                <dl class="row small mb-4">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $servicio->id }}</dd>

                    <dt class="col-sm-4">Turnos que lo usan</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $servicio->turnos_count > 0 ? 'badge-exito' : 'text-bg-secondary' }}">
                            {{ $servicio->turnos_count }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Creado</dt>
                    <dd class="col-sm-8">{{ $servicio->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                    <dt class="col-sm-4">Última modificación</dt>
                    <dd class="col-sm-8">{{ $servicio->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </dl>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('servicios.destroy', $servicio) }}" method="POST"
                          onsubmit="return confirm(@js('¿Eliminar el servicio «' . $servicio->nombre . '»?'));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" @disabled($servicio->turnos_count > 0)
                                title="{{ $servicio->turnos_count > 0 ? 'No se puede eliminar: tiene turnos asociados' : 'Eliminar' }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                    <a href="{{ route('servicios.index') }}" class="btn btn-outline-secondary ms-auto">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
