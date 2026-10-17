<?php
// ============================================================
// admin/eliminar.php — Eliminación de turno (Delete)
// Solo acepta POST con token CSRF: un link GET no puede borrar datos.
// ============================================================
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValido($_POST['csrf'] ?? null)) {
    header('Location: index.php?status=invalid');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id) {
    $stmt = $pdo->prepare('DELETE FROM turnos WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: index.php?status=' . ($id && $stmt->rowCount() > 0 ? 'deleted' : 'notfound'));
exit;
