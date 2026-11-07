<?php
// ============================================================
// api.php — Helpers comunes de los endpoints JSON de api/ (TP9)
// Cada endpoint responde siempre JSON con su código HTTP: nunca
// redirige ni imprime HTML (ni siquiera si falla la conexión).
// ============================================================
// TP10: carga el autoload de app/ (modelos) y los helpers del sitio
require_once __DIR__ . '/../app/bootstrap.php';

/**
 * Envía la respuesta JSON con el código HTTP indicado y termina el script.
 */
function responderJson(int $codigo, array $payload): never
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

/**
 * Respuesta de error con formato uniforme: {status: 'error', message}.
 */
function errorJson(int $codigo, string $mensaje, array $cabeceras = []): never
{
    foreach ($cabeceras as $cabecera) {
        header($cabecera);
    }
    responderJson($codigo, ['status' => 'error', 'message' => $mensaje]);
}

/**
 * Corta con 405 (+ cabecera Allow) si el método HTTP no es el esperado.
 */
function exigirMetodo(string $metodo): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== $metodo) {
        errorJson(405, "Método no permitido. Usá $metodo.", ["Allow: $metodo"]);
    }
}

/**
 * Variante JSON de auth_guard.php: sin sesión → 401, rol no permitido → 403.
 * Devuelve el usuario logueado.
 */
function exigirRolApi(array $rolesPermitidos = []): array
{
    $usuario = usuarioActual();
    if ($usuario === null) {
        errorJson(401, 'Necesitás iniciar sesión para realizar esta acción.');
    }
    if ($rolesPermitidos !== [] && !in_array($usuario['rol'], $rolesPermitidos, true)) {
        errorJson(403, 'No tenés permisos para realizar esta acción.');
    }
    return $usuario;
}

/**
 * Lee el cuerpo de la petición como objeto JSON. Corta con 415 si el
 * Content-Type no es application/json y con 400 si el JSON es inválido.
 */
function leerCuerpoJson(): array
{
    $tipo = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
    if ($tipo !== 'application/json') {
        errorJson(415, 'El cuerpo debe enviarse como application/json.');
    }
    try {
        $datos = json_decode((string) file_get_contents('php://input'), true, 8, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        errorJson(400, 'El cuerpo de la petición no es un JSON válido.');
    }
    if (!is_array($datos) || array_is_list($datos) && $datos !== []) {
        errorJson(400, 'Se esperaba un objeto JSON.');
    }
    return $datos;
}

/**
 * Entero positivo recibido en JSON (número entero o string de dígitos), o null.
 * Rechaza floats, booleanos y otros tipos que PHP convertiría en silencio.
 */
function enteroPositivo(mixed $valor): ?int
{
    if (is_string($valor) && ctype_digit($valor)) {
        $valor = (int) $valor;
    }
    return is_int($valor) && $valor > 0 ? $valor : null;
}

// La conexión la toman los modelos con App\Core\Database::conexion(). Si falla,
// config/conexion.php detecta responderJson() y devuelve 500 en JSON en lugar del texto plano.
