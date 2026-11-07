@extends('layouts.app')

@section('titulo', 'Editar servicio')

@section('encabezado')
<section class="bx-page-hero">
    <div class="container">
        <nav aria-label="Ruta de navegación">
            <ol class="breadcrumb bx-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                <li class="breadcrumb-item"><a href="{{ route('servicios.show', $servicio) }}">{{ $servicio->nombre }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
        <h1>Editar servicio</h1>
        <p>Modificá los datos de «{{ $servicio->nombre }}». Los turnos ya cargados conservan su precio.</p>
    </div>
</section>
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="bx-form-card">
            @include('servicios._form', [
                'accion'     => route('servicios.update', $servicio),
                'metodo'     => 'PUT',
                'textoBoton' => 'Guardar cambios',
            ])
        </div>
    </div>
</div>
@endsection
