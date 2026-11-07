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
