@extends('layouts.app')

@section('titulo', 'Catálogo de servicios')

@section('contenido')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0 text-ink">Catálogo de servicios</h1>
        <small class="text-muted">Servicios que ofrece el taller y su precio base.</small>
    </div>
    <a href="{{ route('servicios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo servicio
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('servicios.index') }}" class="row g-2" role="search">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" value="{{ $q }}" class="form-control"
                           placeholder="Buscar por nombre o descripción" aria-label="Buscar">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
                @if ($q !== '')
                    <a href="{{ route('servicios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($servicios->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                @if ($q !== '')
                    No se encontraron servicios para «{{ $q }}».
                @else
                    Todavía no hay servicios cargados.
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3">Nombre</th>
                        <th>Descripción</th>
                        <th class="text-end">Precio base</th>
                        <th class="text-center">Turnos</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($servicios as $servicio)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $servicio->nombre }}</td>
                            <td class="text-muted">{{ $servicio->descripcion ?? '—' }}</td>
                            <td class="text-end">{{ $servicio->precio_formateado }}</td>
                            <td class="text-center">
                                <span class="badge {{ $servicio->turnos_count > 0 ? 'badge-exito' : 'text-bg-secondary' }}">
                                    {{ $servicio->turnos_count }}
                                </span>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-outline-secondary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('servicios.destroy', $servicio) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar el servicio «' . $servicio->nombre . '»?'));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card-footer bg-white text-muted small">
        {{ $servicios->count() }} servicio(s){{ $q !== '' ? ' encontrados' : '' }}.
    </div>
</div>
@endsection
