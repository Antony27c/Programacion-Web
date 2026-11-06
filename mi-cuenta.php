<?php
// ============================================================
// mi-cuenta.php — Vista protegida para cualquier usuario logueado (TP8)
// ============================================================
require __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/turnos_db.php';
require_once __DIR__ . '/config/conexion.php';

$usuario = obtenerUsuario($pdo, (int) $_SESSION['usuario_id']);
if (!$usuario) {
    // La cuenta fue eliminada mientras la sesión seguía abierta
    cerrarSesion();
    header('Location: login.php?error=acceso_denegado');
    exit;
}
$turnos  = listarTurnos($pdo, 0, (int) $usuario['id']);
$sinPermiso = filter_input(INPUT_GET, 'error') === 'sin_permiso';

$titulo      = 'BOXLY | Mi cuenta';
$descripcion = 'Tus datos y el estado de tus turnos en BOXLY.';
$seccion     = 'mi-cuenta.php';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Mi cuenta</p>
          <h1>Hola, <?= e($usuario['nombre']) ?></h1>
          <p>Tus datos y el estado de tus turnos.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <?php if ($sinPermiso): ?>
            <div class="alert alert-warning" role="alert">No tenés permisos para acceder a esa sección (solo administradores).</div>
          <?php endif; ?>
          <div class="row g-4">
            <div class="col-lg-4">
              <aside class="bx-side-panel">
                <h2 class="h4">Mis datos</h2>
                <dl class="mb-0">
                  <dt>Nombre</dt>
                  <dd><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></dd>
                  <dt>Email</dt>
                  <dd><?= e($usuario['email']) ?></dd>
                  <dt>Teléfono</dt>
                  <dd><?= e($usuario['telefono']) ?></dd>
                  <dt>Rol</dt>
                  <dd><?= e($usuario['rol']) ?></dd>
                  <dt>Cliente desde</dt>
                  <dd class="mb-0"><?= e(date('d/m/Y', strtotime($usuario['created_at']))) ?></dd>
                </dl>
              </aside>
            </div>
            <div class="col-lg-8">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0">Mis turnos</h2>
                <a class="btn btn-bx-primary btn-sm" href="turnos.php"><i class="bi bi-calendar-plus" aria-hidden="true"></i> Nuevo turno</a>
              </div>
              <?php if ($turnos === []): ?>
                <div class="alert alert-info mb-0">Todavía no tenés turnos. ¡Agendá el primero!</div>
              <?php else: ?>
                <ul class="list-group">
                  <?php foreach ($turnos as $t): ?>
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2">
                      <span>
                        <strong>#<?= e($t['id']) ?> · <?= e(date('d/m/Y', strtotime($t['fecha']))) ?></strong> (<?= e($t['horario']) ?>)<br />
                        <small class="text-muted"><?= e($t['marca'] . ' ' . $t['modelo'] . ' — ' . $t['patente']) ?> · <?= e($t['servicios'] ?? '—') ?></small>
                      </span>
                      <span class="badge text-bg-dark"><?= e($t['estado']) ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
