<?php
// ============================================================
// admin/eliminar.php — Eliminación de turno (Delete)
// Solo acepta POST con token CSRF: un link GET no puede borrar datos.
// ============================================================
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValido($_POST['csrf'] ?? null)) {
    header('Location: index.php?status=invalid');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$status = 'notfound';
if ($id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM turnos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $status = $stmt->rowCount() > 0 ? 'deleted' : 'notfound';
    } catch (PDOException $ex) {
        // Error controlado: el detalle queda en el log del servidor
        error_log('Error al eliminar turno: ' . $ex->getMessage());
        $status = 'dberror';
    }
}

header('Location: index.php?status=' . $status);
exit;
