<?php
// ============================================================
// admin/editar.php — Formulario + actualización de turno (Update)
// ============================================================
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../config/conexion.php';

$esPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$id     = filter_input($esPost ? INPUT_POST : INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$id) {
    header('Location: index.php?status=notfound');
    exit;
}

// Lectura segura del registro a editar
$stmt = $pdo->prepare('SELECT id, nombre, telefono, vehiculo, servicio, fecha, horario, sintoma, estado FROM turnos WHERE id = :id');
$stmt->execute([':id' => $id]);
$turno = $stmt->fetch();

if (!$turno) {
    header('Location: index.php?status=notfound');
    exit;
}

$csrf    = csrfToken();
$datos   = $turno;
$errores = [];

if ($esPost) {
    if (!csrfValido($_POST['csrf'] ?? null)) {
        header('Location: index.php?status=invalid');
        exit;
    }

    [$datos, $errores] = validarTurno($_POST, true);

    if ($errores === []) {
        $sql = 'UPDATE turnos
                SET nombre = :nombre, telefono = :telefono, vehiculo = :vehiculo, servicio = :servicio,
                    fecha = :fecha, horario = :horario, sintoma = :sintoma, estado = :estado
                WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($datos + [':id' => $id]);
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
