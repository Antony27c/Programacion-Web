<?php
// ============================================================
// Usuario — Clientes asociados a los turnos (tabla usuarios)
// ============================================================
namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    /**
     * Usuario con el nombre de su rol, o null si no existe.
     */
    public function obtener(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.created_at, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Devuelve el id del usuario con ese email; si no existe lo crea como Cliente.
     * Con $actualizar = true pisa nombre, apellido y teléfono del existente.
     */
    public function resolverId(array $d, bool $actualizar): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
        $stmt->execute([':email' => $d['email']]);
        $id = $stmt->fetchColumn();
        $datos = [':nombre' => $d['nombre'], ':apellido' => $d['apellido'], ':telefono' => $d['telefono']];

        if ($id !== false) {
            if ($actualizar) {
                $this->pdo->prepare('UPDATE usuarios SET nombre = :nombre, apellido = :apellido, telefono = :telefono WHERE id = :id')
                    ->execute($datos + [':id' => $id]);
            }
            return (int) $id;
        }

        $this->pdo->prepare(
            'INSERT INTO usuarios (rol_id, nombre, apellido, email, telefono)
             VALUES ((SELECT id FROM roles WHERE nombre = :rol), :nombre, :apellido, :email, :telefono)'
        )->execute($datos + [':rol' => \ROL_CLIENTE, ':email' => $d['email']]);
        return (int) $this->pdo->lastInsertId();
    }
}
