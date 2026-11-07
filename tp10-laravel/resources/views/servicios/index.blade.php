@extends('layouts.app')

@section('titulo', 'Catálogo de servicios')

@section('encabezado')
<section class="bx-page-hero">
    <div class="container d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <p class="eyebrow">Módulo Laravel</p>
            <h1>Catálogo de servicios</h1>
            <p>Servicios que ofrece el taller y su precio base de referencia.</p>
        </div>
        <a href="{{ route('servicios.create') }}" class="btn btn-bx-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo servicio
        </a>
    </div>
</section>
@endsection

@section('contenido')
<form method="GET" action="{{ route('servicios.index') }}" class="bx-toolbar" role="search">
    <div class="bx-search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label for="q" class="visually-hidden">Buscar servicios</label>
        <input type="search" id="q" name="q" value="{{ $q }}" class="form-control"
               placeholder="Buscar por nombre o descripción">
    </div>
    <button type="submit" class="btn btn-bx-outline-dark">Buscar</button>
    @if ($q !== '')
        <a href="{{ route('servicios.index') }}" class="btn btn-link">Limpiar búsqueda</a>
    @endif
</form>

@if ($servicios->isEmpty())
    <div class="bx-card bx-empty">
        <i class="bi bi-inbox" aria-hidden="true"></i>
        @if ($q !== '')
            <h2 class="h5">Sin resultados</h2>
            <p class="mb-3">No se encontraron servicios para «{{ $q }}».</p>
            <a href="{{ route('servicios.index') }}" class="btn btn-bx-outline-dark">Ver todos</a>
        @else
            <h2 class="h5">Todavía no hay servicios cargados</h2>
            <p class="mb-3">Cargá el primero para que aparezca en el catálogo.</p>
            <a href="{{ route('servicios.create') }}" class="btn btn-bx-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo servicio</a>
        @endif
    </div>
@else
    <div class="bx-table-wrap">
        <table class="table table-hover align-middle bx-table">
            <caption class="visually-hidden">Servicios del taller con precio base y cantidad de turnos</caption>
            <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Descripción</th>
                <th scope="col" class="text-end">Precio base</th>
                <th scope="col" class="text-center">Turnos</th>
                <th scope="col" class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servicios as $servicio)
                <tr>
                    <td class="bx-nombre">{{ $servicio->nombre }}</td>
                    <td data-label="Descripción" class="text-muted">{{ $servicio->descripcion ?? '—' }}</td>
                    <td data-label="Precio base" class="text-end bx-precio">{{ $servicio->precio_formateado }}</td>
                    <td data-label="Turnos" class="text-center">
                        <span><span class="badge {{ $servicio->turnos_count > 0 ? 'bx-badge-uso' : 'bx-badge-libre' }}">{{ $servicio->turnos_count }}</span></span>
                    </td>
                    <td class="bx-actions">
                        <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-bx-outline-dark">
                            <i class="bi bi-eye" aria-hidden="true"></i> Ver<span class="visually-hidden"> {{ $servicio->nombre }}</span>
                        </a>
                        <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-sm btn-bx-primary">
                            <i class="bi bi-pencil" aria-hidden="true"></i> Editar<span class="visually-hidden"> {{ $servicio->nombre }}</span>
                        </a>
                        <form action="{{ route('servicios.destroy', $servicio) }}" method="POST"
                              onsubmit="return confirm(@js('¿Eliminar el servicio «' . $servicio->nombre . '»?'));">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-bx-danger">
                                <i class="bi bi-trash" aria-hidden="true"></i> Eliminar<span class="visually-hidden"> {{ $servicio->nombre }}</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<p class="bx-count">{{ $servicios->count() }} servicio(s){{ $q !== '' ? ' encontrados' : '' }}.</p>
@endsection
