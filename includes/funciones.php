<?php
// ============================================================
// funciones.php — Helpers de sanitización y formularios (TP5)
// ============================================================
require_once __DIR__ . '/sesion.php';

/**
 * Limpia un valor recibido por GET/POST:
 * quita espacios al inicio/fin y lo devuelve como string plano.
 * Si llega un array (ej: nombre[]=x) se descarta: evita el aviso
 * "Array to string conversion" y que el campo pase como "Array".
 */
function limpiar($valor): string
{
    if (!is_scalar($valor)) {
        return '';
    }
    return trim((string) $valor);
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

/**
 * Devuelve 'checked' si la opción está en la lista del campo (para checkboxes).
 */
function marcado(array $datos, string $campo, string $opcion): string
{
    return in_array($opcion, (array) ($datos[$campo] ?? []), true) ? ' checked' : '';
}

// ============================================================
// Listas blancas contra catálogos de la base [id => nombre]
// (la validación de turnos vive en app/Models/Turno.php — TP10)
// ============================================================
/**
 * Lista blanca: el valor debe ser un id entero existente en el catálogo [id => nombre].
 */
function enCatalogo(string $valor, array $catalogo): bool
{
    return ctype_digit($valor) && isset($catalogo[(int) $valor]);
}

// ============================================================
// Token CSRF para formularios que modifican datos (admin, login, logout)
// ============================================================
function csrfToken(): string
{
    iniciarSesion();
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrfValido(?string $token): bool
{
    return is_string($token) && hash_equals(csrfToken(), $token);
}
