<?php
// ============================================================
// usuarios_db.php — Acceso a datos de usuarios (TP8)
// Todas las consultas usan sentencias preparadas (PDO).
// ============================================================
require_once __DIR__ . '/funciones.php';

const ROL_ADMIN   = 'Administrador';
const ROL_CLIENTE = 'Cliente';

/**
 * Usuario por email con el nombre de su rol, o null si no existe.
 */
function buscarUsuarioPorEmail(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare(
        'SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.password_hash, r.nombre AS rol
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.email = :email'
    );
    $stmt->execute([':email' => $email]);
    return $stmt->fetch() ?: null;
}

function obtenerUsuario(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.created_at, r.nombre AS rol
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.id = :id'
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function emailRegistrado(PDO $pdo, string $email): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Alta de cliente con la contraseña hasheada (bcrypt). Devuelve el id nuevo.
 */
function registrarUsuario(PDO $pdo, array $d): int
{
    $pdo->prepare(
        'INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono)
         VALUES ((SELECT id FROM roles WHERE nombre = :rol), :nombre, :apellido, :email, :hash, :telefono)'
    )->execute([
        ':rol'      => ROL_CLIENTE,
        ':nombre'   => $d['nombre'],
        ':apellido' => $d['apellido'],
        ':email'    => $d['email'],
        ':hash'     => password_hash($d['password'], PASSWORD_BCRYPT),
        ':telefono' => $d['telefono'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Verifica credenciales con password_verify(). Devuelve el usuario o null.
 * Si el email no existe se verifica igual contra un hash ficticio para que
 * el tiempo de respuesta no revele qué cuentas existen.
 */
function autenticar(PDO $pdo, string $email, string $password): ?array
{
    $usuario = buscarUsuarioPorEmail($pdo, $email);
    $hash    = $usuario['password_hash'] ?? '$2y$10$tDHEx8Es.XAUdqQJ7vXyiunrm8NeMVFHz76S1Rjr7iVpSBcBHVPJe';

    if (!password_verify($password, $hash) || empty($usuario['password_hash'])) {
        return null;
    }
    if (password_needs_rehash($hash, PASSWORD_BCRYPT)) {
        $pdo->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id')
            ->execute([':hash' => password_hash($password, PASSWORD_BCRYPT), ':id' => $usuario['id']]);
    }
    return $usuario;
}

/**
 * Sanitiza y valida el formulario de registro. Devuelve [$datos, $errores].
 */
function validarRegistro(array $entrada): array
{
    $datos = [];
    foreach (['nombre', 'apellido', 'email', 'telefono'] as $campo) {
        $datos[$campo] = limpiar($entrada[$campo] ?? '');
    }
    $datos['email'] = mb_strtolower($datos['email']);
    // is_scalar: un password[]=x llegaría como array y rompería el casteo
    $password  = is_scalar($entrada['password'] ?? null) ? (string) $entrada['password'] : '';
    $password2 = is_scalar($entrada['password2'] ?? null) ? (string) $entrada['password2'] : '';
    $errores   = [];

    foreach (['nombre' => 'tu nombre', 'apellido' => 'tu apellido'] as $campo => $texto) {
        if ($datos[$campo] === '') {
            $errores[$campo] = "Ingresá $texto.";
        } elseif (mb_strlen($datos[$campo]) < 2 || mb_strlen($datos[$campo]) > 60) {
            $errores[$campo] = 'Debe tener entre 2 y 60 caracteres.';
        } elseif (!preg_match("/^[\p{L}\s'.-]+$/u", $datos[$campo])) {
            // Lista blanca: solo letras, espacios, apóstrofo, punto y guion (sin < > ni etiquetas)
            $errores[$campo] = 'Usá solo letras, espacios o guiones.';
        }
    }
    if ($datos['email'] === '') {
        $errores['email'] = 'Ingresá un email.';
    } elseif (mb_strlen($datos['email']) > 150 || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresá un email válido.';
    }
    if (!preg_match('/^[0-9+\s()-]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'Ingresá un teléfono válido (números, +, espacios o guiones).';
    }
    // bcrypt solo usa los primeros 72 bytes: se limita para no truncar en silencio
    if (mb_strlen($password) < 8 || strlen($password) > 72) {
        $errores['password'] = 'La contraseña debe tener entre 8 y 72 caracteres.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errores['password'] = 'Usá al menos una letra y un número.';
    }
    if ($password !== $password2) {
        $errores['password2'] = 'Las contraseñas no coinciden.';
    }
    foreach ($datos as $campo => $v) {
        if (!mb_check_encoding($v, 'UTF-8')) {
            $errores[$campo] = 'Contiene caracteres no válidos.';
        }
    }

    return [$datos + ['password' => $password], $errores];
}
