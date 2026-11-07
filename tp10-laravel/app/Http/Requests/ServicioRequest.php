<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación del alta y la edición de servicios.
 */
class ServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza los datos antes de validar (recorta espacios y acepta coma decimal).
     */
    protected function prepareForValidation(): void
    {
        $precio = $this->input('precio_base');
        if (is_string($precio)) {
            $precio = trim($precio);
            // "45.000,50" -> "45000.50"; "45000,5" -> "45000.5"
            if (str_contains($precio, ',')) {
                $precio = str_replace(['.', ','], ['', '.'], $precio);
            }
        }

        $descripcion = trim((string) $this->input('descripcion'));

        $this->merge([
            'nombre'      => trim((string) $this->input('nombre')),
            'descripcion' => $descripcion === '' ? null : $descripcion,
            'precio_base' => $precio,
        ]);
    }

    public function rules(): array
    {
        // En update, la ruta trae el modelo {servicio}: se ignora su propio nombre.
        $servicio = $this->route('servicio');
        $id = is_object($servicio) ? $servicio->id : $servicio;

        return [
            'nombre' => [
                'required', 'string', 'min:2', 'max:60',
                Rule::unique('servicios', 'nombre')->ignore($id),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'precio_base' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre del servicio es obligatorio.',
            'nombre.string'        => 'El nombre debe ser texto.',
            'descripcion.string'   => 'La descripción debe ser texto.',
            'nombre.min'           => 'El nombre debe tener al menos :min caracteres.',
            'nombre.max'           => 'El nombre no puede superar los :max caracteres.',
            'nombre.unique'        => 'Ya existe un servicio con ese nombre.',
            'descripcion.max'      => 'La descripción no puede superar los :max caracteres.',
            'precio_base.required' => 'El precio base es obligatorio.',
            'precio_base.numeric'  => 'El precio base debe ser un número.',
            'precio_base.min'      => 'El precio base no puede ser negativo.',
            'precio_base.max'      => 'El precio base es demasiado alto.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'      => 'nombre',
            'descripcion' => 'descripción',
            'precio_base' => 'precio base',
        ];
    }
}
