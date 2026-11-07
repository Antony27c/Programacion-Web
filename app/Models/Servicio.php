<?php
// ============================================================
// Servicio — Catálogo de servicios del taller (TP9)
// Lo usan servicios.php (render en servidor) y
// api/buscar_servicios.php (respuesta JSON para fetch).
// ============================================================
namespace App\Models;

use App\Core\Model;

class Servicio extends Model
{
    public const BUSQUEDA_MAX = 60;

    /**
     * Servicios cuyo nombre o descripción contienen $q (todos si $q está vacío).
     * Los comodines % y _ del término se escapan: se buscan como texto literal.
     */
    public function buscar(string $q = ''): array
    {
        $sql    = 'SELECT id, nombre, descripcion, precio_base FROM servicios';
        $params = [];
        if ($q !== '') {
            $termino = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q) . '%';
            $sql    .= " WHERE nombre LIKE :q1 ESCAPE '!' OR descripcion LIKE :q2 ESCAPE '!'";
            $params  = [':q1' => $termino, ':q2' => $termino];
        }
        $stmt = $this->pdo->prepare($sql . ' ORDER BY id');
        $stmt->execute($params);

        return array_map(fn ($s) => [
            'id'          => (int) $s['id'],
            'nombre'      => $s['nombre'],
            'descripcion' => (string) $s['descripcion'],
            'precio_base' => (float) $s['precio_base'],
        ], $stmt->fetchAll());
    }

    /**
     * Un servicio por id (consulta preparada) o null si no existe.
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre, descripcion, precio_base FROM servicios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $s = $stmt->fetch();

        return $s ? [
            'id'          => (int) $s['id'],
            'nombre'      => $s['nombre'],
            'descripcion' => (string) $s['descripcion'],
            'precio_base' => (float) $s['precio_base'],
        ] : null;
    }

    /**
     * Ícono de img/iconos/ según el nombre del servicio (solo presentación).
     * La misma tabla está replicada en js/servicios.js para la búsqueda en vivo.
     */
    public static function icono(string $nombre): string
    {
        $nombre = mb_strtolower($nombre);
        $mapa = [
            'fren'       => 'frenos',
            'suspens'    => 'suspension',
            'amortig'    => 'suspension',
            'alinea'     => 'alineacion',
            'diagn'      => 'diagnostico',
            'escan'      => 'diagnostico',
            'bater'      => 'bateria',
            'electr'     => 'bateria',
            'aire'       => 'aire',
            'neum'       => 'neumaticos',
            'cubiert'    => 'neumaticos',
            'service'    => 'aceite',
            'aceite'     => 'aceite',
        ];
        foreach ($mapa as $clave => $icono) {
            if (str_contains($nombre, $clave)) {
                return $icono;
            }
        }
        return 'presupuesto';
    }

    /**
     * Valida el término de búsqueda. Devuelve el mensaje de error o null si es válido.
     */
    public static function errorBusqueda(string $q): ?string
    {
        if (!mb_check_encoding($q, 'UTF-8')) {
            return 'La búsqueda contiene caracteres no válidos.';
        }
        if (mb_strlen($q) > self::BUSQUEDA_MAX) {
            return 'La búsqueda no puede superar los ' . self::BUSQUEDA_MAX . ' caracteres.';
        }
        return null;
    }

    /**
     * Precio para mostrar: "$ 45.000" o "A cotizar" si es 0.
     */
    public static function precio(float $precio): string
    {
        return $precio > 0 ? '$ ' . number_format($precio, 0, ',', '.') : 'A cotizar';
    }
}
