<?php
// ============================================================
// conexion.php — Conexión segura a MySQL con PDO (TP7)
// Las credenciales reales van en config/conexion.local.php
// (no se versiona). Ver conexion.local.example.php.
// ============================================================
$config = array_merge(
    [
        'host'    => 'localhost',
        'port'    => 3306,
        'db'      => 'boxly',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    is_file(__DIR__ . '/conexion.local.php') ? require __DIR__ . '/conexion.local.php' : []
);

$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset={$config['charset']}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
} catch (\PDOException $e) {
    // El detalle técnico va al log del servidor, nunca al navegador
    error_log('Error de conexión PDO: ' . $e->getMessage());
    http_response_code(500);
    die('Error crítico de conexión con la base de datos. Verificá que MySQL esté iniciado y las credenciales en config/conexion.local.php.');
}
unset($config);
