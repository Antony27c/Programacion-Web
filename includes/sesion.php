<?php
// ============================================================
// sesion.php — Sesión de usuario con cookie segura (TP8)
// ============================================================
const SESION_INACTIVIDAD = 1800; // 30 minutos sin actividad cierran la sesión

/**
 * Inicia la sesión una sola vez con cookie HttpOnly + SameSite y modo estricto
 * (el servidor rechaza IDs de sesión que no generó él mismo).
 */
function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('BOXLYSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    // Expiración por inactividad: se descartan los datos del usuario
    if (isset($_SESSION['usuario_id']) && time() - ($_SESSION['ultimo_acceso'] ?? 0) > SESION_INACTIVIDAD) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['ultimo_acceso'] = time();
}

/**
 * Datos del usuario logueado o null si no hay sesión iniciada.
 */
function usuarioActual(): ?array
{
    iniciarSesion();
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    return [
        'id'     => (int) $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'],
        'rol'    => $_SESSION['rol'],
    ];
}

/**
 * Login exitoso: nuevo ID de sesión (anti Session Fixation) y datos clave del usuario.
 */
function abrirSesion(array $usuario): void
{
    iniciarSesion();
    session_regenerate_id(true);
    unset($_SESSION['csrf']);
    $_SESSION['usuario_id']     = (int) $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['rol']            = $usuario['rol'];
}

/**
 * Logout: vacía $_SESSION, borra la cookie del navegador y destruye la sesión.
 */
function cerrarSesion(): void
{
    iniciarSesion();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'],
        ]);
    }
    session_destroy();
}

/**
 * Prefijo relativo desde el script actual hasta la raíz del sitio ('' o '../').
 */
function rutaBase(): string
{
    $raiz   = str_replace('\\', '/', dirname(__DIR__));
    $script = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME']) ?: '');
    $rel    = str_starts_with($script, $raiz . '/') ? substr($script, strlen($raiz) + 1) : '';
    return str_repeat('../', substr_count($rel, '/'));
}
