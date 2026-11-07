{{-- Formulario compartido por create y edit. Recibe $servicio, $accion, $metodo y $textoBoton. --}}
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Revisá el formulario:</strong> encontramos {{ $errors->count() }} error(es) de validación.
    </div>
@endif

<form action="{{ $accion }}" method="POST" class="bx-form" novalidate>
    @csrf
    @if ($metodo !== 'POST')
        @method($metodo)
    @endif

    <p class="bx-required-note">Los campos marcados con <span>*</span> son obligatorios.</p>

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" id="nombre" name="nombre" maxlength="60" required
               class="form-control @error('nombre') is-invalid @enderror"
               @error('nombre') aria-describedby="nombreError" aria-invalid="true" @enderror
               value="{{ old('nombre', $servicio->nombre) }}">
        @error('nombre')
            <div class="invalid-feedback" id="nombreError">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción <span class="bx-opcional">(opcional)</span></label>
        <textarea id="descripcion" name="descripcion" rows="3" maxlength="255"
                  class="form-control @error('descripcion') is-invalid @enderror"
                  aria-describedby="descripcionAyuda @error('descripcion') descripcionError @enderror">{{ old('descripcion', $servicio->descripcion) }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback" id="descripcionError">{{ $message }}</div>
        @enderror
        <div class="form-text" id="descripcionAyuda">Hasta 255 caracteres.</div>
    </div>

    <div class="mb-2">
        <label for="precio_base" class="form-label">Precio base</label>
        <div class="input-group has-validation">
            <span class="input-group-text" aria-hidden="true">$</span>
            <input type="number" id="precio_base" name="precio_base" min="0" step="0.01" required
                   class="form-control @error('precio_base') is-invalid @enderror"
                   @error('precio_base') aria-describedby="precioError" aria-invalid="true" @enderror
                   value="{{ old('precio_base', $servicio->precio_base ?? 0) }}">
            @error('precio_base')
                <div class="invalid-feedback" id="precioError">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="bx-form-actions">
        <button type="submit" class="btn btn-bx-primary">
            <i class="bi bi-check-lg" aria-hidden="true"></i> {{ $textoBoton }}
        </button>
        <a href="{{ route('servicios.index') }}" class="btn btn-bx-outline-dark">Cancelar</a>
    </div>
</form>
