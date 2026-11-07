<?php
// ============================================================
// Database — Punto único de acceso a la conexión PDO (TP10)
// Envuelve config/conexion.php (TP7), que sigue siendo el lugar
// donde se crea el PDO (credenciales en config/conexion.local.php,
// try/catch con error_log). Acá solo se garantiza una única
// conexión por request y se la entrega a los modelos.
// ============================================================
namespace App\Core;

use PDO;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function conexion(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::conectar();
        }
        return self::$pdo;
    }

    /**
     * Incluye el archivo de conexión del TP7 en un ámbito aislado y toma el $pdo
     * que define. BOXLY_CONEXION permite indicar otra ruta (por ejemplo, para pruebas).
     */
    private static function conectar(): PDO
    {
        $archivo = \defined('BOXLY_CONEXION') ? \BOXLY_CONEXION : BOXLY_RAIZ . '/config/conexion.php';

        $pdo = (static function (string $__archivo) {
            require $__archivo;
            return $pdo ?? null;
        })($archivo);

        if (!$pdo instanceof PDO) {
            throw new RuntimeException('config/conexion.php no definió la conexión $pdo.');
        }
        return $pdo;
    }
}
