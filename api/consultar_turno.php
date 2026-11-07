<?php
// ============================================================
// api/consultar_turno.php — Consulta pública del estado de un turno (TP9)
// GET ?id=N&patente=ABC123 → {status, data: {id, fecha, horario, estado,
// vehiculo, patente, servicios[]}}
// 200 OK · 400 datos inválidos · 404 sin coincidencia · 405 método · 500 base
// ============================================================
require_once __DIR__ . '/../includes/api.php';
require_once __DIR__ . '/../includes/turnos_db.php';

exigirMetodo('GET');

$idCrudo      = $_GET['id'] ?? '';
$patenteCruda = $_GET['patente'] ?? '';
if (!is_string($idCrudo) || !is_string($patenteCruda)) {
    errorJson(400, 'Los parámetros id y patente deben ser texto.');
}

$id      = enteroPositivo(trim($idCrudo));
$patente = normalizarPatente(trim($patenteCruda));
if ($id === null) {
    errorJson(400, 'Ingresá un número de turno válido (entero positivo).');
}
if ($patente === null) {
    errorJson(400, 'Ingresá una patente válida (ABC123 o AB123CD).');
}

try {
    $turno = consultarEstadoTurno(conexionApi(), $id, $patente);
} catch (PDOException $e) {
    error_log('api/consultar_turno: ' . $e->getMessage());
    errorJson(500, 'No pudimos consultar el turno. Intentá de nuevo más tarde.');
}

// Mismo mensaje si el turno no existe o la patente no coincide: no se filtra qué falló
if ($turno === null) {
    errorJson(404, "No encontramos el turno N° $id para la patente $patente.");
}

responderJson(200, ['status' => 'success', 'data' => $turno]);
