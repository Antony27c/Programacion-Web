<?php
// ============================================================
// funciones.php — Helpers de sanitización y formularios (TP5)
// ============================================================
require_once __DIR__ . '/sesion.php';

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

/**
 * Devuelve 'checked' si la opción está en la lista del campo (para checkboxes).
 */
function marcado(array $datos, string $campo, string $opcion): string
{
    return in_array($opcion, (array) ($datos[$campo] ?? []), true) ? ' checked' : '';
}

// ============================================================
// Turnos (TP6/TP7) — validación compartida
// Servicios, marcas y estados se validan contra los catálogos
// de la base (ver includes/turnos_db.php → catalogos()).
// ============================================================
const HORARIOS_TURNO  = ['Mañana', 'Tarde'];
const ESTADO_INICIAL  = 'Pendiente';
const CAMPOS_TURNO    = ['nombre', 'apellido', 'email', 'telefono', 'marca_id', 'modelo', 'anio', 'patente', 'fecha', 'horario', 'sintoma'];

/**
 * Datos vacíos para inicializar el formulario de turno.
 */
function turnoVacio(array $catalogos): array
{
    return array_fill_keys(CAMPOS_TURNO, '') + [
        'servicios' => [],
        'estado_id' => (string) array_search(ESTADO_INICIAL, $catalogos['estados'], true),
    ];
}

/**
 * Sanitiza y valida los datos de un turno recibidos por POST.
 * Devuelve [$datos, $errores]. Con $admin = true se valida también
 * el estado y se permiten fechas pasadas (edición de turnos viejos);
 * desde el sitio público el turno siempre nace como ESTADO_INICIAL.
 */
function validarTurno(array $entrada, array $catalogos, bool $admin = false): array
{
    $datos = [];
    foreach (CAMPOS_TURNO as $campo) {
        $datos[$campo] = limpiar($entrada[$campo] ?? '');
    }
    $datos['email']     = mb_strtolower($datos['email']);
    $datos['patente']   = strtoupper(preg_replace('/[\s.-]+/', '', $datos['patente']));
    $servicios          = array_filter((array) ($entrada['servicios'] ?? []), 'is_string');
    $datos['servicios'] = array_values(array_unique(array_map('limpiar', $servicios)));
    $datos['estado_id'] = $admin
        ? limpiar($entrada['estado_id'] ?? '')
        : (string) array_search(ESTADO_INICIAL, $catalogos['estados'], true);
    $errores = [];

    foreach (['nombre' => 'el nombre', 'apellido' => 'el apellido'] as $campo => $texto) {
        if ($datos[$campo] === '') {
            $errores[$campo] = "Ingresá $texto.";
        } elseif (mb_strlen($datos[$campo]) < 2 || mb_strlen($datos[$campo]) > 60) {
            $errores[$campo] = 'Debe tener entre 2 y 60 caracteres.';
        }
    }

    if ($datos['email'] === '') {
        $errores['email'] = 'Ingresá un email.';
    } elseif (mb_strlen($datos['email']) > 150 || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresá un email válido.';
    }

    if ($datos['telefono'] === '') {
        $errores['telefono'] = 'Ingresá un teléfono de contacto.';
    } elseif (!preg_match('/^[0-9+\s()-]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'Ingresá un teléfono válido (números, +, espacios o guiones).';
    }

    if (!enCatalogo($datos['marca_id'], $catalogos['marcas'])) {
        $errores['marca_id'] = 'Seleccioná una marca de la lista.';
    }

    if ($datos['modelo'] === '') {
        $errores['modelo'] = 'Indicá el modelo del vehículo.';
    } elseif (mb_strlen($datos['modelo']) > 60) {
        $errores['modelo'] = 'El modelo no puede superar los 60 caracteres.';
    }

    $anioMax = (int) date('Y') + 1;
    if (filter_var($datos['anio'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1950, 'max_range' => $anioMax]]) === false) {
        $errores['anio'] = "Ingresá un año entre 1950 y $anioMax.";
    }

    if ($datos['patente'] === '') {
        $errores['patente'] = 'Ingresá la patente.';
    } elseif (!preg_match('/^([A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2})$/', $datos['patente'])) {
        $errores['patente'] = 'Formato de patente inválido (ABC123 o AB123CD).';
    }

    if ($datos['servicios'] === []) {
        $errores['servicios'] = 'Elegí al menos un servicio.';
    } elseif (array_filter($datos['servicios'], fn ($s) => !enCatalogo($s, $catalogos['servicios']))) {
        $errores['servicios'] = 'Seleccioná servicios de la lista.';
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

    if ($admin && !enCatalogo($datos['estado_id'], $catalogos['estados'])) {
        $errores['estado_id'] = 'Seleccioná un estado válido.';
    }

    // La base es utf8mb4: un texto con bytes inválidos haría fallar el INSERT
    foreach (CAMPOS_TURNO as $campo) {
        if (!mb_check_encoding($datos[$campo], 'UTF-8')) {
            $errores[$campo] = 'Contiene caracteres no válidos.';
        }
    }

    return [$datos, $errores];
}

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
