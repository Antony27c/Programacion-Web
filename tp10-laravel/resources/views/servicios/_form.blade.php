{{-- Formulario compartido por create y edit. Recibe $servicio, $accion, $metodo y $textoBoton. --}}
<form action="{{ $accion }}" method="POST" novalidate>
    @csrf
    @if ($metodo !== 'POST')
        @method($metodo)
    @endif

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" id="nombre" name="nombre" maxlength="60" required
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $servicio->nombre) }}">
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3" maxlength="255"
                  class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $servicio->descripcion) }}</textarea>
        <div class="form-text">Opcional, hasta 255 caracteres.</div>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="precio_base" class="form-label">Precio base <span class="text-danger">*</span></label>
        <div class="input-group has-validation">
            <span class="input-group-text">$</span>
            <input type="number" id="precio_base" name="precio_base" min="0" step="0.01" required
                   class="form-control @error('precio_base') is-invalid @enderror"
                   value="{{ old('precio_base', $servicio->precio_base ?? 0) }}">
            @error('precio_base')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> {{ $textoBoton }}
        </button>
        <a href="{{ route('servicios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
