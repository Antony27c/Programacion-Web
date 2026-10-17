<?php
// ============================================================
// admin/crear.php — Formulario + inserción de turno (Create)
// ============================================================
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../config/conexion.php';

$csrf    = csrfToken();
$datos   = array_fill_keys(CAMPOS_TURNO, '') + ['estado' => 'Pendiente'];
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf'] ?? null)) {
        header('Location: index.php?status=invalid');
        exit;
    }

    [$datos, $errores] = validarTurno($_POST, true);

    if ($errores === []) {
        // Consulta preparada (anti SQL Injection)
        $sql = 'INSERT INTO turnos (nombre, telefono, vehiculo, servicio, fecha, horario, sintoma, estado)
                VALUES (:nombre, :telefono, :vehiculo, :servicio, :fecha, :horario, :sintoma, :estado)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos);
        header('Location: index.php?status=created');
        exit;
    }
}

$titulo      = 'BOXLY | Nuevo turno';
$descripcion = 'Alta de turno en el panel de administración.';
$base        = '../';
$seccion     = 'admin';
$accion      = 'crear.php';
$textoBoton  = 'Guardar turno';
require __DIR__ . '/../includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Administración</p>
          <h1>Nuevo turno</h1>
          <p>Cargá los datos del cliente y del vehículo.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <?php if ($errores): ?>
            <div class="alert alert-danger" role="alert">
              <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
            </div>
          <?php endif; ?>
          <?php require __DIR__ . '/_formulario.php'; ?>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
