<?php
// ============================================================
// Vehiculo — Vehículos de los clientes (tabla vehiculos)
// ============================================================
namespace App\Models;

use App\Core\Model;

class Vehiculo extends Model
{
    /**
     * Devuelve el id del vehículo con esa patente; si no existe lo crea a nombre de $usuarioId.
     * Con $actualizar = true pisa marca, modelo y año del existente.
     */
    public function resolverId(array $d, int $usuarioId, bool $actualizar): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM vehiculos WHERE patente = :patente');
        $stmt->execute([':patente' => $d['patente']]);
        $id = $stmt->fetchColumn();
        $datos = [':marca' => (int) $d['marca_id'], ':modelo' => $d['modelo'], ':anio' => (int) $d['anio']];

        if ($id !== false) {
            if ($actualizar) {
                $this->pdo->prepare('UPDATE vehiculos SET marca_id = :marca, modelo = :modelo, anio = :anio WHERE id = :id')
                    ->execute($datos + [':id' => $id]);
            }
            return (int) $id;
        }

        $this->pdo->prepare(
            'INSERT INTO vehiculos (usuario_id, marca_id, modelo, anio, patente)
             VALUES (:usuario, :marca, :modelo, :anio, :patente)'
        )->execute($datos + [':usuario' => $usuarioId, ':patente' => $d['patente']]);
        return (int) $this->pdo->lastInsertId();
    }
}
