<?php
// ============================================================
// Model — Clase base de los modelos: recibe/obtiene el PDO
// ============================================================
namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::conexion();
    }
}
