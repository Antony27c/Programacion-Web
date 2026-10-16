<?php
// ============================================================
// funciones.php — Helpers de sanitización y formularios (TP5)
// ============================================================

/**
 * Limpia un valor recibido por GET/POST:
 * quita espacios al inicio/fin y lo devuelve como string plano.
 */
function limpiar($valor): string
{
    return trim((string) ($valor ?? ''));
}

/**
 * Escapa un string para imprimirlo en HTML sin riesgo de XSS.
 * Convierte < > & " ' en entidades HTML.
 */
function e($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Devuelve el valor ya sanitizado de un campo para re-imprimirlo
 * en el formulario (persistencia de datos ante errores).
 */
function valor(array $datos, string $campo): string
{
    return e($datos[$campo] ?? '');
}

/**
 * Devuelve 'selected' si el valor actual coincide (para <select>).
 */
function seleccionado(array $datos, string $campo, string $opcion): string
{
    return ($datos[$campo] ?? '') === $opcion ? ' selected' : '';
}

/**
 * Devuelve la clase Bootstrap 'is-invalid' si el campo tiene error.
 */
function claseError(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? ' is-invalid' : '';
}

/**
 * Imprime el feedback de error de un campo (si existe).
 */
function errorCampo(array $errores, string $campo): string
{
    return isset($errores[$campo])
        ? '<div class="invalid-feedback">' . e($errores[$campo]) . '</div>'
        : '';
}
