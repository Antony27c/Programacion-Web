<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Eloquent del catálogo de servicios del taller.
 * Tabla `servicios` de la base propia del módulo (`boxly_laravel`).
 */
class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_base',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
    ];

    /**
     * Turnos que incluyen este servicio (tabla pivote turno_servicio).
     * El pivote guarda el precio cobrado en ese turno.
     */
    public function turnos(): BelongsToMany
    {
        return $this->belongsToMany(Turno::class, 'turno_servicio', 'servicio_id', 'turno_id')
            ->withPivot('precio');
    }

    /**
     * Filtro de búsqueda por nombre o descripción.
     */
    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        $texto = trim((string) $texto);
        if ($texto === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($texto) {
            $q->where('nombre', 'like', "%{$texto}%")
              ->orWhere('descripcion', 'like', "%{$texto}%");
        });
    }

    /**
     * Precio formateado al estilo argentino: $ 45.000 (o $ 45.000,50 si tiene centavos).
     */
    public function getPrecioFormateadoAttribute(): string
    {
        $valor = (float) $this->precio_base;
        $decimales = fmod($valor, 1.0) == 0.0 ? 0 : 2;

        return '$ ' . number_format($valor, $decimales, ',', '.');
    }
}
