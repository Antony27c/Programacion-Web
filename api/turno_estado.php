<?php
// ============================================================
// api/turno_estado.php — Cambio de estado de un turno (TP9)
// POST JSON {id, estado_id} + cabecera X-CSRF-Token. Solo Administrador.
// 200 OK · 400 datos inválidos · 401 sin sesión · 403 sin permiso o CSRF
// inválido · 404 turno inexistente · 405 método · 415 no es JSON · 500 base
// ============================================================
use App\Models\Catalogo;
use App\Models\Turno;

require_once __DIR__ . '/../includes/api.php';

exigirMetodo('POST');
exigirRolApi([ROL_ADMIN]);

// El token viaja en una cabecera propia: un formulario de otro sitio no puede enviarla
if (!csrfValido($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    errorJson(403, 'Token de seguridad inválido. Recargá la página e intentá de nuevo.');
}

$datos    = leerCuerpoJson();
$id       = enteroPositivo($datos['id'] ?? null);
$estadoId = enteroPositivo($datos['estado_id'] ?? null);
if ($id === null || $estadoId === null) {
    errorJson(400, 'Los campos id y estado_id deben ser enteros positivos.');
}

try {
    $estado = (new Catalogo())->nombreEstado($estadoId);
    if ($estado === null) {
        errorJson(400, 'El estado indicado no existe.');
    }
    if (!(new Turno())->cambiarEstado($id, $estadoId)) {
        errorJson(404, "El turno #$id no existe.");
    }
} catch (PDOException $e) {
    error_log('api/turno_estado: ' . $e->getMessage());
    errorJson(500, 'No pudimos actualizar el turno. Intentá de nuevo más tarde.');
}

responderJson(200, [
    'status' => 'success',
    'data'   => ['id' => $id, 'estado_id' => $estadoId, 'estado' => $estado],
]);
