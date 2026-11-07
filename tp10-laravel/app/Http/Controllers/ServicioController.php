<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioRequest;
use App\Models\Servicio;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD del catálogo de servicios (controlador resource).
 */
class ServicioController extends Controller
{
    /**
     * Listado con búsqueda opcional ?q= por nombre o descripción.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $servicios = Servicio::query()
            ->buscar($q)
            ->withCount('turnos')
            ->orderBy('nombre')
            ->get();

        return view('servicios.index', compact('servicios', 'q'));
    }

    public function create(): View
    {
        return view('servicios.create', ['servicio' => new Servicio()]);
    }

    public function store(ServicioRequest $request): RedirectResponse
    {
        $servicio = Servicio::create($request->validated());

        return redirect()
            ->route('servicios.index')
            ->with('ok', "Servicio «{$servicio->nombre}» creado correctamente.");
    }

    public function show(Servicio $servicio): View
    {
        $servicio->loadCount('turnos');

        return view('servicios.show', compact('servicio'));
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(ServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($request->validated());

        return redirect()
            ->route('servicios.show', $servicio)
            ->with('ok', "Servicio «{$servicio->nombre}» actualizado correctamente.");
    }

    /**
     * Elimina un servicio solo si ningún turno lo usa
     * (la FK turno_servicio.servicio_id es ON DELETE RESTRICT).
     */
    public function destroy(Servicio $servicio): RedirectResponse
    {
        $cantidad = $servicio->turnos()->count();
        if ($cantidad > 0) {
            return redirect()
                ->route('servicios.index')
                ->with('error', "No se puede eliminar «{$servicio->nombre}»: está asociado a {$cantidad} turno(s).");
        }

        try {
            $servicio->delete();
        } catch (QueryException $e) {
            // Por si otra operación lo asoció a un turno entre la verificación y el borrado.
            return redirect()
                ->route('servicios.index')
                ->with('error', "No se pudo eliminar «{$servicio->nombre}» porque tiene registros relacionados.");
        }

        return redirect()
            ->route('servicios.index')
            ->with('ok', "Servicio «{$servicio->nombre}» eliminado.");
    }
}
