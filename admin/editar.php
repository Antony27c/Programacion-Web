<?php
// ============================================================
// admin/editar.php — Formulario + actualización de turno (Update)
// ============================================================
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/turnos_db.php';
require_once __DIR__ . '/../config/conexion.php';

$esPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$id     = filter_input($esPost ? INPUT_POST : INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$id) {
    header('Location: index.php?status=notfound');
    exit;
}

// Lectura segura del registro a editar (JOIN con usuarios y vehiculos)
$turno = obtenerTurno($pdo, $id);

if (!$turno) {
    header('Location: index.php?status=notfound');
    exit;
}

$csrf      = csrfToken();
$catalogos = catalogos($pdo);
$datos     = $turno;
$errores   = [];

if ($esPost) {
    if (!csrfValido($_POST['csrf'] ?? null)) {
        header('Location: index.php?status=invalid');
        exit;
    }

    [$datos, $errores] = validarTurno($_POST, $catalogos, true);

    if ($errores === []) {
        guardarTurno($pdo, $datos, true, $id);
        header('Location: index.php?status=updated');
        exit;
    }
}

$titulo      = 'BOXLY | Editar turno #' . $id;
$descripcion = 'Edición de turno en el panel de administración.';
$base        = '../';
$seccion     = 'admin';
$accion      = 'editar.php';
$textoBoton  = 'Guardar cambios';
require __DIR__ . '/../includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Administración</p>
          <h1>Editar turno #<?= e($id) ?></h1>
          <p>Modificá los datos o actualizá el estado del turno.</p>
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
