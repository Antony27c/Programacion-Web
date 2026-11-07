<?php
// ============================================================
// app/bootstrap.php — Arranque de la estructura MVC (TP10)
// Autoload PSR-4 simple: App\Models\Turno → app/Models/Turno.php
// y helpers genéricos compartidos con el resto del sitio.
// ============================================================
define('BOXLY_RAIZ', dirname(__DIR__));

require_once BOXLY_RAIZ . '/includes/funciones.php';
require_once BOXLY_RAIZ . '/includes/usuarios_db.php';

spl_autoload_register(function (string $clase): void {
    $prefijo = 'App\\';
    if (!str_starts_with($clase, $prefijo)) {
        return;
    }
    $archivo = __DIR__ . '/' . str_replace('\\', '/', substr($clase, strlen($prefijo))) . '.php';
    if (is_file($archivo)) {
        require $archivo;
    }
});
