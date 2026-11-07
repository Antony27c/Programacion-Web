<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo mínimo de turnos.
 * Se usa para contar los turnos asociados a cada servicio.
 */
class Turno extends Model
{
    protected $table = 'turnos';

    protected $fillable = ['fecha_hora', 'estado'];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'turno_servicio', 'turno_id', 'servicio_id')
            ->withPivot('precio');
    }
}
