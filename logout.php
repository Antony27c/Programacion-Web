<?php
// ============================================================
// logout.php — Cierre de sesión (TP8)
// Solo por POST con token CSRF: un link externo no puede desloguear.
// ============================================================
require_once __DIR__ . '/includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValido($_POST['csrf'] ?? null)) {
    header('Location: index.php');
    exit;
}

cerrarSesion();
header('Location: login.php?ok=logout');
exit;
