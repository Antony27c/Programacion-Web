<?php
// ============================================================
// Catalogo — Tablas maestras (marcas, servicios, estados_turno)
// Se usan para armar los <select> y como listas blancas al validar.
// ============================================================
namespace App\Models;

use App\Core\Model;
use PDO;

class Catalogo extends Model
{
    private const CONSULTAS = [
        'marcas'    => 'SELECT id, nombre FROM marcas ORDER BY nombre',
        'servicios' => 'SELECT id, nombre FROM servicios ORDER BY id',
        'estados'   => 'SELECT id, nombre FROM estados_turno ORDER BY id',
    ];

    /**
     * ['marcas' => [id => nombre], 'servicios' => [...], 'estados' => [...]]
     */
    public function todos(): array
    {
        return array_map(
            fn (string $sql) => $this->pdo->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR),
            self::CONSULTAS
        );
    }

    /**
     * Nombre de un estado del catálogo o null si el id no existe (TP9).
     */
    public function nombreEstado(int $estadoId): ?string
    {
        $stmt = $this->pdo->prepare('SELECT nombre FROM estados_turno WHERE id = :id');
        $stmt->execute([':id' => $estadoId]);
        $nombre = $stmt->fetchColumn();
        return $nombre === false ? null : $nombre;
    }
}
