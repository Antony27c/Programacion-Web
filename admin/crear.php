<?php
// ============================================================
// admin/crear.php — Formulario + inserción de turno (Create)
// ============================================================
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/turnos_db.php';
require_once __DIR__ . '/../config/conexion.php';

$csrf      = csrfToken();
$catalogos = catalogos($pdo);
$datos     = turnoVacio($catalogos);
$errores   = [];
$errorDb   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf'] ?? null)) {
        header('Location: index.php?status=invalid');
        exit;
    }

    [$datos, $errores] = validarTurno($_POST, $catalogos, true);

    if ($errores === []) {
        // Inserción en usuarios/vehiculos/turnos/turno_servicio con consultas preparadas
        try {
            guardarTurno($pdo, $datos, true);
            header('Location: index.php?status=created');
            exit;
        } catch (PDOException $ex) {
            // Error controlado: el detalle queda en el log del servidor
            error_log('Error al guardar turno: ' . $ex->getMessage());
            $errorDb = 'No se pudo guardar el turno en la base de datos. Intentá nuevamente.';
        }
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
          <?php if ($errorDb): ?>
            <div class="alert alert-danger" role="alert"><?= e($errorDb) ?></div>
          <?php endif; ?>
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
