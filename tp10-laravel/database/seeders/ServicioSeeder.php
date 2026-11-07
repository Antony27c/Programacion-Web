<?php

namespace Database\Seeders;

use App\Models\Servicio;
use App\Models\Turno;
use Illuminate\Database\Seeder;

/**
 * Carga el catálogo inicial del taller y un turno de ejemplo
 * (sirve para probar que no se puede borrar un servicio en uso).
 */
class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo = [
            ['nombre' => 'Service',     'descripcion' => 'Cambio de aceite, filtros y control de fluidos.',            'precio_base' => 48000],
            ['nombre' => 'Frenos',      'descripcion' => 'Revisión y cambio de pastillas, discos y líquido de freno.', 'precio_base' => 60000],
            ['nombre' => 'Diagnóstico', 'descripcion' => 'Escaneo computarizado y revisión de testigos del tablero.',  'precio_base' => 25000],
            ['nombre' => 'Otro',        'descripcion' => 'Trabajo a cotizar según inspección.',                         'precio_base' => 0],
        ];

        foreach ($catalogo as $fila) {
            Servicio::updateOrCreate(['nombre' => $fila['nombre']], $fila);
        }

        if (Turno::count() === 0) {
            $turno = Turno::create(['fecha_hora' => now()->addDay()->setTime(9, 0), 'estado' => 'pendiente']);
            $service = Servicio::where('nombre', 'Service')->first();
            $turno->servicios()->attach($service->id, ['precio' => $service->precio_base]);
        }
    }
}
