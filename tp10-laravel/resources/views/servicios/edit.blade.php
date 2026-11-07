@extends('layouts.app')

@section('titulo', 'Editar servicio')

@section('contenido')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
        <li class="breadcrumb-item"><a href="{{ route('servicios.show', $servicio) }}">{{ $servicio->nombre }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <h1 class="h4 mb-4 text-ink"><i class="bi bi-pencil-square"></i> Editar servicio</h1>
                @include('servicios._form', [
                    'accion'     => route('servicios.update', $servicio),
                    'metodo'     => 'PUT',
                    'textoBoton' => 'Guardar cambios',
                ])
            </div>
        </div>
    </div>
</div>
@endsection
