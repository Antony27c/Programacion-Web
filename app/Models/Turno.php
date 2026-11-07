<?php
// ============================================================
// Turno — Modelo de turnos sobre el esquema normalizado del TP6
// (usuarios, vehiculos, turnos, turno_servicio).
// Todas las consultas usan sentencias preparadas (PDO).
// ============================================================
namespace App\Models;

use App\Core\Model;
use DateTime;
use PDO;
use Throwable;

class Turno extends Model
{
    public const HORARIOS       = ['Mañana', 'Tarde'];
    public const ESTADO_INICIAL = 'Pendiente';
    public const CAMPOS         = ['nombre', 'apellido', 'email', 'telefono', 'marca_id', 'modelo', 'anio', 'patente', 'fecha', 'horario', 'sintoma'];

    // ------------------------------------------------------------
    // Consultas
    // ------------------------------------------------------------

    /**
     * Lista los turnos con cliente, vehículo, estado y servicios (INNER JOIN).
     * $estadoId = 0 lista todos; $usuarioId > 0 limita a los turnos de ese cliente.
     */
    public function listar(int $estadoId = 0, int $usuarioId = 0): array
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':estado' => $estadoId, ':estado2' => $estadoId, ':usuario' => $usuarioId, ':usuario2' => $usuarioId]);
        return $stmt->fetchAll();
    }

    /**
     * Devuelve un turno con la misma forma que los datos del formulario
     * (valores como string) o null si no existe.
     */
    public function obtener(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
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

        $stmt = $this->pdo->prepare('SELECT servicio_id FROM turno_servicio WHERE turno_id = :id');
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
    public function guardar(array $d, bool $admin, ?int $id = null): int
    {
        $this->pdo->beginTransaction();
        try {
            $usuarioId  = (new Usuario($this->pdo))->resolverId($d, $admin);
            $vehiculoId = (new Vehiculo($this->pdo))->resolverId($d, $usuarioId, $admin);

            $params = [
                ':usuario'  => $usuarioId,
                ':vehiculo' => $vehiculoId,
                ':estado'   => (int) $d['estado_id'],
                ':fecha'    => $d['fecha'],
                ':horario'  => $d['horario'],
                ':sintoma'  => $d['sintoma'],
            ];
            if ($id === null) {
                $this->pdo->prepare(
                    'INSERT INTO turnos (usuario_id, vehiculo_id, estado_id, fecha, horario, sintoma)
                     VALUES (:usuario, :vehiculo, :estado, :fecha, :horario, :sintoma)'
                )->execute($params);
                $id = (int) $this->pdo->lastInsertId();
            } else {
                $this->pdo->prepare(
                    'UPDATE turnos
                     SET usuario_id = :usuario, vehiculo_id = :vehiculo, estado_id = :estado,
                         fecha = :fecha, horario = :horario, sintoma = :sintoma
                     WHERE id = :id'
                )->execute($params + [':id' => $id]);
            }

            $this->sincronizarServicios($id, array_map('intval', $d['servicios']));
            $this->pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina un turno (turno_servicio se borra en cascada). true si existía.
     */
    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM turnos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Cambia solo el estado de un turno (TP9, panel con fetch).
     * Devuelve false si el turno no existe.
     */
    public function cambiarEstado(int $id, int $estadoId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM turnos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        if (!$stmt->fetchColumn()) {
            return false;
        }
        $this->pdo->prepare('UPDATE turnos SET estado_id = :estado WHERE id = :id')
            ->execute([':estado' => $estadoId, ':id' => $id]);
        return true;
    }

    /**
     * Consulta pública del estado de un turno (TP9). Exige número de turno
     * y patente del vehículo: sin la patente correcta no se revela nada.
     * No devuelve datos personales del cliente. null si no hay coincidencia.
     */
    public function consultarEstado(int $id, string $patente): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.id, t.fecha, t.horario, e.nombre AS estado,
                    m.nombre AS marca, v.modelo, v.patente,
                    (SELECT GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR \', \')
                       FROM turno_servicio ts
                       INNER JOIN servicios s ON s.id = ts.servicio_id
                      WHERE ts.turno_id = t.id) AS servicios
             FROM turnos t
             INNER JOIN vehiculos     v ON v.id = t.vehiculo_id
             INNER JOIN marcas        m ON m.id = v.marca_id
             INNER JOIN estados_turno e ON e.id = t.estado_id
             WHERE t.id = :id AND v.patente = :patente'
        );
        $stmt->execute([':id' => $id, ':patente' => $patente]);
        $turno = $stmt->fetch();
        if (!$turno) {
            return null;
        }
        return [
            'id'        => (int) $turno['id'],
            'fecha'     => $turno['fecha'],
            'horario'   => $turno['horario'],
            'estado'    => $turno['estado'],
            'vehiculo'  => trim($turno['marca'] . ' ' . $turno['modelo']),
            'patente'   => $turno['patente'],
            'servicios' => $turno['servicios'] === null ? [] : explode(', ', $turno['servicios']),
        ];
    }

    /**
     * Deja en turno_servicio exactamente los servicios elegidos. Los que ya
     * estaban conservan el precio congelado; los nuevos toman el precio_base actual.
     */
    private function sincronizarServicios(int $turnoId, array $servicios): void
    {
        // Sin servicios elegidos se deja la relación vacía (IN () sería SQL inválido)
        if ($servicios === []) {
            $this->pdo->prepare('DELETE FROM turno_servicio WHERE turno_id = ?')->execute([$turnoId]);
            return;
        }

        $in = implode(', ', array_fill(0, count($servicios), '?'));

        $this->pdo->prepare("DELETE FROM turno_servicio WHERE turno_id = ? AND servicio_id NOT IN ($in)")
            ->execute([$turnoId, ...$servicios]);

        $this->pdo->prepare(
            "INSERT INTO turno_servicio (turno_id, servicio_id, precio)
             SELECT ?, s.id, s.precio_base
             FROM servicios s
             WHERE s.id IN ($in)
               AND NOT EXISTS (SELECT 1 FROM turno_servicio ts WHERE ts.turno_id = ? AND ts.servicio_id = s.id)"
        )->execute([$turnoId, ...$servicios, $turnoId]);
    }

    // ------------------------------------------------------------
    // Reglas de negocio / validación
    // Servicios, marcas y estados se validan contra los catálogos
    // de la base (ver Catalogo::todos()).
    // ------------------------------------------------------------

    /**
     * Normaliza una patente como en el formulario de turnos: sin espacios,
     * puntos ni guiones y en mayúsculas. Devuelve null si el formato no es válido.
     */
    public static function normalizarPatente(string $patente): ?string
    {
        $patente = strtoupper(preg_replace('/[\s.-]+/', '', $patente));
        return preg_match('/^([A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2})$/', $patente) ? $patente : null;
    }

    /**
     * Datos vacíos para inicializar el formulario de turno.
     */
    public static function vacio(array $catalogos): array
    {
        return array_fill_keys(self::CAMPOS, '') + [
            'servicios' => [],
            'estado_id' => self::estadoInicial($catalogos),
        ];
    }

    /**
     * Sanitiza y valida los datos de un turno recibidos por POST.
     * Devuelve [$datos, $errores]. Con $admin = true se valida también
     * el estado y se permiten fechas pasadas (edición de turnos viejos);
     * desde el sitio público el turno siempre nace como ESTADO_INICIAL.
     */
    public static function validar(array $entrada, array $catalogos, bool $admin = false): array
    {
        $datos = [];
        foreach (self::CAMPOS as $campo) {
            $datos[$campo] = limpiar($entrada[$campo] ?? '');
        }
        $datos['email']     = mb_strtolower($datos['email']);
        $datos['patente']   = strtoupper(preg_replace('/[\s.-]+/', '', $datos['patente']));
        $servicios          = array_filter((array) ($entrada['servicios'] ?? []), 'is_string');
        $datos['servicios'] = array_values(array_unique(array_map('limpiar', $servicios)));
        $datos['estado_id'] = $admin
            ? limpiar($entrada['estado_id'] ?? '')
            : self::estadoInicial($catalogos);
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

        if (!in_array($datos['horario'], self::HORARIOS, true)) {
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
        foreach (self::CAMPOS as $campo) {
            if (!mb_check_encoding($datos[$campo], 'UTF-8')) {
                $errores[$campo] = 'Contiene caracteres no válidos.';
            }
        }

        return [$datos, $errores];
    }

    private static function estadoInicial(array $catalogos): string
    {
        return (string) array_search(self::ESTADO_INICIAL, $catalogos['estados'], true);
    }
}
