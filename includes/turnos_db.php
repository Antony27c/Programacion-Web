<?php
// ============================================================
// turnos_db.php — Acceso a datos de turnos sobre el esquema
// normalizado del TP6 (usuarios, vehiculos, turnos, turno_servicio).
// Todas las consultas usan sentencias preparadas (PDO).
// ============================================================
require_once __DIR__ . '/usuarios_db.php';

/**
 * Catálogos para selects y listas blancas: ['marcas' => [id => nombre], ...]
 */
function catalogos(PDO $pdo): array
{
    $consultas = [
        'marcas'    => 'SELECT id, nombre FROM marcas ORDER BY nombre',
        'servicios' => 'SELECT id, nombre FROM servicios ORDER BY id',
        'estados'   => 'SELECT id, nombre FROM estados_turno ORDER BY id',
    ];
    return array_map(fn ($sql) => $pdo->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR), $consultas);
}

/**
 * Lista los turnos con cliente, vehículo, estado y servicios (INNER JOIN).
 * $estadoId = 0 lista todos; $usuarioId > 0 limita a los turnos de ese cliente.
 */
function listarTurnos(PDO $pdo, int $estadoId = 0, int $usuarioId = 0): array
{
    $sql = 'SELECT t.id, t.fecha, t.horario, t.sintoma,
                   u.nombre, u.apellido, u.telefono,
                   m.nombre AS marca, v.modelo, v.anio, v.patente,
                   e.nombre AS estado,
                   (SELECT GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR \', \')
                      FROM turno_servicio ts
                      INNER JOIN servicios s ON s.id = ts.servicio_id
                     WHERE ts.turno_id = t.id) AS servicios,
                   (SELECT COALESCE(SUM(ts.precio), 0)
                      FROM turno_servicio ts
                     WHERE ts.turno_id = t.id) AS total
            FROM turnos t
            INNER JOIN usuarios      u ON u.id = t.usuario_id
            INNER JOIN vehiculos     v ON v.id = t.vehiculo_id
            INNER JOIN marcas        m ON m.id = v.marca_id
            INNER JOIN estados_turno e ON e.id = t.estado_id
            WHERE (:estado = 0 OR t.estado_id = :estado2)
              AND (:usuario = 0 OR t.usuario_id = :usuario2)
            ORDER BY t.fecha DESC, t.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estado' => $estadoId, ':estado2' => $estadoId, ':usuario' => $usuarioId, ':usuario2' => $usuarioId]);
    return $stmt->fetchAll();
}

/**
 * Devuelve un turno con la misma forma que los datos del formulario
 * (valores como string) o null si no existe.
 */
function obtenerTurno(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT u.nombre, u.apellido, u.email, u.telefono,
                v.marca_id, v.modelo, v.anio, v.patente,
                t.fecha, t.horario, t.sintoma, t.estado_id
         FROM turnos t
         INNER JOIN usuarios  u ON u.id = t.usuario_id
         INNER JOIN vehiculos v ON v.id = t.vehiculo_id
         WHERE t.id = :id'
    );
    $stmt->execute([':id' => $id]);
    $turno = $stmt->fetch();
    if (!$turno) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT servicio_id FROM turno_servicio WHERE turno_id = :id');
    $stmt->execute([':id' => $id]);
    $turno = array_map('strval', $turno);
    $turno['servicios'] = array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    return $turno;
}

/**
 * Crea ($id = null) o actualiza un turno en una transacción:
 * 1) busca/crea el usuario por email, 2) busca/crea el vehículo por patente,
 * 3) inserta/actualiza el turno, 4) sincroniza sus servicios (tabla N:M).
 * Con $admin = true también se actualizan los datos del usuario y del vehículo
 * existentes; desde el sitio público solo se reutilizan (no se pisan datos ajenos).
 */
function guardarTurno(PDO $pdo, array $d, bool $admin, ?int $id = null): int
{
    $pdo->beginTransaction();
    try {
        $usuarioId  = idUsuario($pdo, $d, $admin);
        $vehiculoId = idVehiculo($pdo, $d, $usuarioId, $admin);

        $params = [
            ':usuario'  => $usuarioId,
            ':vehiculo' => $vehiculoId,
            ':estado'   => (int) $d['estado_id'],
            ':fecha'    => $d['fecha'],
            ':horario'  => $d['horario'],
            ':sintoma'  => $d['sintoma'],
        ];
        if ($id === null) {
            $pdo->prepare(
                'INSERT INTO turnos (usuario_id, vehiculo_id, estado_id, fecha, horario, sintoma)
                 VALUES (:usuario, :vehiculo, :estado, :fecha, :horario, :sintoma)'
            )->execute($params);
            $id = (int) $pdo->lastInsertId();
        } else {
            $pdo->prepare(
                'UPDATE turnos
                 SET usuario_id = :usuario, vehiculo_id = :vehiculo, estado_id = :estado,
                     fecha = :fecha, horario = :horario, sintoma = :sintoma
                 WHERE id = :id'
            )->execute($params + [':id' => $id]);
        }

        sincronizarServicios($pdo, $id, array_map('intval', $d['servicios']));
        $pdo->commit();
        return $id;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function idUsuario(PDO $pdo, array $d, bool $actualizar): int
{
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $d['email']]);
    $id = $stmt->fetchColumn();
    $datos = [':nombre' => $d['nombre'], ':apellido' => $d['apellido'], ':telefono' => $d['telefono']];

    if ($id !== false) {
        if ($actualizar) {
            $pdo->prepare('UPDATE usuarios SET nombre = :nombre, apellido = :apellido, telefono = :telefono WHERE id = :id')
                ->execute($datos + [':id' => $id]);
        }
        return (int) $id;
    }

    $pdo->prepare(
        'INSERT INTO usuarios (rol_id, nombre, apellido, email, telefono)
         VALUES ((SELECT id FROM roles WHERE nombre = :rol), :nombre, :apellido, :email, :telefono)'
    )->execute($datos + [':rol' => ROL_CLIENTE, ':email' => $d['email']]);
    return (int) $pdo->lastInsertId();
}

function idVehiculo(PDO $pdo, array $d, int $usuarioId, bool $actualizar): int
{
    $stmt = $pdo->prepare('SELECT id FROM vehiculos WHERE patente = :patente');
    $stmt->execute([':patente' => $d['patente']]);
    $id = $stmt->fetchColumn();
    $datos = [':marca' => (int) $d['marca_id'], ':modelo' => $d['modelo'], ':anio' => (int) $d['anio']];

    if ($id !== false) {
        if ($actualizar) {
            $pdo->prepare('UPDATE vehiculos SET marca_id = :marca, modelo = :modelo, anio = :anio WHERE id = :id')
                ->execute($datos + [':id' => $id]);
        }
        return (int) $id;
    }

    $pdo->prepare(
        'INSERT INTO vehiculos (usuario_id, marca_id, modelo, anio, patente)
         VALUES (:usuario, :marca, :modelo, :anio, :patente)'
    )->execute($datos + [':usuario' => $usuarioId, ':patente' => $d['patente']]);
    return (int) $pdo->lastInsertId();
}

/**
 * Deja en turno_servicio exactamente los servicios elegidos. Los que ya
 * estaban conservan el precio congelado; los nuevos toman el precio_base actual.
 */
function sincronizarServicios(PDO $pdo, int $turnoId, array $servicios): void
{
    // Sin servicios elegidos se deja la relación vacía (IN () sería SQL inválido)
    if ($servicios === []) {
        $pdo->prepare('DELETE FROM turno_servicio WHERE turno_id = ?')->execute([$turnoId]);
        return;
    }

    $in = implode(', ', array_fill(0, count($servicios), '?'));

    $pdo->prepare("DELETE FROM turno_servicio WHERE turno_id = ? AND servicio_id NOT IN ($in)")
        ->execute([$turnoId, ...$servicios]);

    $pdo->prepare(
        "INSERT INTO turno_servicio (turno_id, servicio_id, precio)
         SELECT ?, s.id, s.precio_base
         FROM servicios s
         WHERE s.id IN ($in)
           AND NOT EXISTS (SELECT 1 FROM turno_servicio ts WHERE ts.turno_id = ? AND ts.servicio_id = s.id)"
    )->execute([$turnoId, ...$servicios, $turnoId]);
}
