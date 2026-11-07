<?php
// ============================================================
// api/buscar_servicios.php — Endpoint público JSON (TP9)
// GET ?q=texto → {status, total, data: [{id, nombre, descripcion, precio_base}]}
// 200 OK · 400 término inválido · 405 método no permitido · 500 error de base
// ============================================================
use App\Models\Servicio;

require_once __DIR__ . '/../includes/api.php';

exigirMetodo('GET');

$q = $_GET['q'] ?? '';
if (!is_string($q)) {
    errorJson(400, 'El parámetro q debe ser un texto.');
}
$q = trim($q);
if (($error = Servicio::errorBusqueda($q)) !== null) {
    errorJson(400, $error);
}

try {
    $servicios = (new Servicio())->buscar($q);
} catch (PDOException $e) {
    error_log('api/buscar_servicios: ' . $e->getMessage());
    errorJson(500, 'No pudimos consultar los servicios. Intentá de nuevo más tarde.');
}

responderJson(200, [
    'status' => 'success',
    'total'  => count($servicios),
    'data'   => $servicios,
]);
