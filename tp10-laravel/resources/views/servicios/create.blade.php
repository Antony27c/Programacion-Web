@extends('layouts.app')

@section('titulo', 'Nuevo servicio')

@section('encabezado')
<section class="bx-page-hero">
    <div class="container">
        <nav aria-label="Ruta de navegación">
            <ol class="breadcrumb bx-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
            </ol>
        </nav>
        <h1>Nuevo servicio</h1>
        <p>Cargá el nombre, una descripción breve y el precio base del servicio.</p>
    </div>
</section>
@endsection

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="bx-form-card">
            @include('servicios._form', [
                'accion'     => route('servicios.store'),
                'metodo'     => 'POST',
                'textoBoton' => 'Guardar servicio',
            ])
        </div>
    </div>
</div>
@endsection
