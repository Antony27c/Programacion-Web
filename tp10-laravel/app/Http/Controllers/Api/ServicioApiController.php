<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API JSON de solo lectura del catálogo de servicios.
 */
class ServicioApiController extends Controller
{
    /**
     * GET /api/servicios?q=texto
     */
    public function index(Request $request): JsonResponse
    {
        $servicios = Servicio::query()
            ->buscar($request->query('q'))
            ->withCount('turnos')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Servicio $s) => $this->formatear($s));

        return response()->json([
            'status' => 'success',
            'total'  => $servicios->count(),
            'data'   => $servicios,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/servicios/{id}
     */
    public function show(string $id): JsonResponse
    {
        $servicio = ctype_digit($id)
            ? Servicio::withCount('turnos')->find((int) $id)
            : null;

        if (!$servicio) {
            return response()->json([
                'status'  => 'error',
                'message' => "No existe un servicio con id {$id}.",
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $this->formatear($servicio),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    private function formatear(Servicio $s): array
    {
        return [
            'id'          => $s->id,
            'nombre'      => $s->nombre,
            'descripcion' => $s->descripcion,
            'precio_base' => (float) $s->precio_base,
            'turnos'      => (int) $s->turnos_count,
            'created_at'  => $s->created_at?->toDateTimeString(),
            'updated_at'  => $s->updated_at?->toDateTimeString(),
        ];
    }
}
