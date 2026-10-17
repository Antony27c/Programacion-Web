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

// ============================================================
// Turnos (TP7) — listas blancas y validación compartida
// ============================================================
const SERVICIOS_TURNO = ['Service', 'Frenos', 'Diagnóstico', 'Otro'];
const HORARIOS_TURNO  = ['Mañana', 'Tarde'];
const ESTADOS_TURNO   = ['Pendiente', 'Confirmado', 'Finalizado', 'Cancelado'];
const CAMPOS_TURNO    = ['nombre', 'telefono', 'vehiculo', 'servicio', 'fecha', 'horario', 'sintoma'];

/**
 * Sanitiza y valida los datos de un turno recibidos por POST.
 * Devuelve [$datos, $errores]. Con $admin = true se valida también
 * el estado y se permiten fechas pasadas (edición de turnos viejos).
 */
function validarTurno(array $entrada, bool $admin = false): array
{
    $campos = $admin ? [...CAMPOS_TURNO, 'estado'] : CAMPOS_TURNO;
    $datos  = [];
    foreach ($campos as $campo) {
        $datos[$campo] = limpiar($entrada[$campo] ?? '');
    }
    $errores = [];

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Ingresá el nombre y apellido.';
    } elseif (mb_strlen($datos['nombre']) < 3 || mb_strlen($datos['nombre']) > 120) {
        $errores['nombre'] = 'El nombre debe tener entre 3 y 120 caracteres.';
    }

    if ($datos['telefono'] === '') {
        $errores['telefono'] = 'Ingresá un teléfono de contacto.';
    } elseif (!preg_match('/^[0-9+\s()-]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'Ingresá un teléfono válido (números, +, espacios o guiones).';
    }

    if ($datos['vehiculo'] === '') {
        $errores['vehiculo'] = 'Indicá marca, modelo y año del vehículo.';
    } elseif (mb_strlen($datos['vehiculo']) > 150) {
        $errores['vehiculo'] = 'El vehículo no puede superar los 150 caracteres.';
    }

    if (!in_array($datos['servicio'], SERVICIOS_TURNO, true)) {
        $errores['servicio'] = 'Seleccioná un servicio de la lista.';
    }

    if ($datos['fecha'] === '') {
        $errores['fecha'] = 'Elegí una fecha.';
    } else {
        $fecha = DateTime::createFromFormat('!Y-m-d', $datos['fecha']);
        if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha']) {
            $errores['fecha'] = 'La fecha ingresada no es válida.';
        } elseif (!$admin && $fecha < new DateTime('today')) {
            $errores['fecha'] = 'La fecha no puede ser anterior a hoy.';
        }
    }

    if (!in_array($datos['horario'], HORARIOS_TURNO, true)) {
        $errores['horario'] = 'Elegí un horario.';
    }

    if ($datos['sintoma'] === '') {
        $errores['sintoma'] = 'Contanos qué le pasa al auto.';
    } elseif (mb_strlen($datos['sintoma']) < 10) {
        $errores['sintoma'] = 'Describí el problema con más detalle (mínimo 10 caracteres).';
    }

    if ($admin && !in_array($datos['estado'], ESTADOS_TURNO, true)) {
        $errores['estado'] = 'Seleccioná un estado válido.';
    }

    return [$datos, $errores];
}

// ============================================================
// Token CSRF para acciones del panel admin
// ============================================================
function csrfToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrfValido(?string $token): bool
{
    return is_string($token) && hash_equals(csrfToken(), $token);
}
