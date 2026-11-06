<?php
// ============================================================
// auth_guard.php — Guardián de rutas (TP8)
// Uso: definir opcionalmente $rolesPermitidos = [ROL_ADMIN] y luego
//      require __DIR__ . '/../includes/auth_guard.php';
// Sin sesión → login.php?error=acceso_denegado
// Rol no permitido → mi-cuenta.php?error=sin_permiso
// ============================================================
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/usuarios_db.php';

iniciarSesion();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . rutaBase() . 'login.php?error=acceso_denegado');
    exit;
}

if (!empty($rolesPermitidos) && !in_array($_SESSION['rol'], $rolesPermitidos, true)) {
    header('Location: ' . rutaBase() . 'mi-cuenta.php?error=sin_permiso');
    exit;
}
unset($rolesPermitidos);
