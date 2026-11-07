<?php
// ============================================================
// mi-cuenta.php — Vista protegida para cualquier usuario logueado (TP8)
// ============================================================
require __DIR__ . '/includes/auth_guard.php';
require __DIR__ . '/app/bootstrap.php';

use App\Models\Turno;
use App\Models\Usuario;

$usuario = (new Usuario())->obtener((int) $_SESSION['usuario_id']);
if (!$usuario) {
    // La cuenta fue eliminada mientras la sesión seguía abierta
    cerrarSesion();
    header('Location: login.php?error=acceso_denegado');
    exit;
}
$turnos  = (new Turno())->listar(0, (int) $usuario['id']);
$sinPermiso = filter_input(INPUT_GET, 'error') === 'sin_permiso';

$titulo      = 'BOXLY | Mi cuenta';
$descripcion = 'Tus datos y el estado de tus turnos en BOXLY.';
$seccion     = 'mi-cuenta.php';
$estilos     = ['css/auth.css'];

// Presentación: mismas insignias de estado que el panel admin y fecha tipo calendario
$colores  = [
    'Pendiente'  => 'text-bg-warning',
    'Confirmado' => 'text-bg-primary',
    'Finalizado' => 'text-bg-success',
    'Cancelado'  => 'text-bg-secondary',
];
$meses    = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$iniciales = mb_strtoupper(mb_substr($usuario['nombre'], 0, 1) . mb_substr($usuario['apellido'], 0, 1));
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Mi cuenta</p>
          <h1>Hola, <?= e($usuario['nombre']) ?></h1>
          <p>Tus datos y el estado de tus turnos en el taller.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <?php if ($sinPermiso): ?>
            <div class="alert alert-warning" role="alert">No tenés permisos para acceder a esa sección (solo administradores).</div>
          <?php endif; ?>
          <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
              <aside class="bx-profile-card bx-sticky" aria-labelledby="tituloPerfil">
                <div class="bx-profile-head">
                  <span class="bx-avatar" aria-hidden="true"><?= e($iniciales) ?></span>
                  <div>
                    <h2 id="tituloPerfil"><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h2>
                    <span class="badge text-bg-primary"><?= e($usuario['rol']) ?></span>
                  </div>
                </div>
                <ul class="bx-profile-data">
                  <li><i class="bi bi-envelope" aria-hidden="true"></i><div><small>Email</small><span><?= e($usuario['email']) ?></span></div></li>
                  <li><i class="bi bi-telephone" aria-hidden="true"></i><div><small>Teléfono</small><span><?= e($usuario['telefono']) ?></span></div></li>
                  <li><i class="bi bi-calendar-heart" aria-hidden="true"></i><div><small>Cliente desde</small><span><?= e(date('d/m/Y', strtotime($usuario['created_at']))) ?></span></div></li>
                </ul>
              </aside>
            </div>
            <div class="col-lg-8">
              <div class="bx-turnos-head">
                <h2>Mis turnos</h2>
                <?php if ($turnos !== []): ?>
                  <a class="btn btn-bx-primary btn-sm" href="turnos.php"><i class="bi bi-calendar-plus" aria-hidden="true"></i> Nuevo turno</a>
                <?php endif; ?>
              </div>
              <?php if ($turnos === []): ?>
                <div class="bx-empty">
                  <img src="img/ilustraciones/estado-vacio.svg" width="480" height="320" alt="" />
                  <h3>Todavía no tenés turnos</h3>
                  <p>Cuando pidas un turno lo vas a ver acá con su estado: pendiente, confirmado o finalizado.</p>
                  <a class="btn btn-bx-primary" href="turnos.php"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno</a>
                </div>
              <?php else: ?>
                <ul class="bx-turnos">
                  <?php foreach ($turnos as $t): $fecha = strtotime($t['fecha']); ?>
                    <li class="bx-turno">
                      <span class="bx-turno-fecha" aria-hidden="true">
                        <span class="bx-turno-mes"><?= e($meses[(int) date('n', $fecha) - 1]) ?></span>
                        <span class="bx-turno-dia"><?= e(date('d', $fecha)) ?></span>
                        <span class="bx-turno-anio"><?= e(date('Y', $fecha)) ?></span>
                      </span>
                      <div>
                        <h3>Turno #<?= e($t['id']) ?> · <?= e(date('d/m/Y', $fecha)) ?> (<?= e($t['horario']) ?>)</h3>
                        <ul class="bx-turno-meta">
                          <li><i class="bi bi-car-front" aria-hidden="true"></i><?= e($t['marca'] . ' ' . $t['modelo'] . ' — ' . $t['patente']) ?></li>
                          <li><i class="bi bi-wrench-adjustable" aria-hidden="true"></i><?= e($t['servicios'] ?? '—') ?></li>
                        </ul>
                      </div>
                      <span class="badge <?= e($colores[$t['estado']] ?? 'text-bg-light') ?>"><span class="visually-hidden">Estado: </span><?= e($t['estado']) ?></span>
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
