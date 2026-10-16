<?php
// ============================================================
// admin/index.php — Listado de turnos (Read)
// ============================================================
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../config/conexion.php';

$csrf = csrfToken();

// Filtro opcional por estado (lista blanca + consulta preparada)
$filtro = limpiar(filter_input(INPUT_GET, 'estado') ?? '');
if (!in_array($filtro, ESTADOS_TURNO, true)) {
    $filtro = '';
}

$sql = 'SELECT id, nombre, telefono, vehiculo, servicio, fecha, horario, sintoma, estado
        FROM turnos
        WHERE (:estado = \'\' OR estado = :estado2)
        ORDER BY fecha DESC, id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute([':estado' => $filtro, ':estado2' => $filtro]);
$turnos = $stmt->fetchAll();

$mensajes = [
    'created'  => ['success', 'Turno creado correctamente.'],
    'updated'  => ['success', 'Turno actualizado correctamente.'],
    'deleted'  => ['success', 'Turno eliminado correctamente.'],
    'notfound' => ['warning', 'El turno solicitado no existe.'],
    'invalid'  => ['danger', 'La solicitud no es válida. Volvé a intentarlo.'],
];
$status = $mensajes[filter_input(INPUT_GET, 'status') ?? ''] ?? null;

$colores = [
    'Pendiente'  => 'text-bg-warning',
    'Confirmado' => 'text-bg-primary',
    'Finalizado' => 'text-bg-success',
    'Cancelado'  => 'text-bg-secondary',
];

$titulo      = 'BOXLY | Administración de turnos';
$descripcion = 'Panel de gestión de turnos del taller BOXLY.';
$base        = '../';
$seccion     = 'admin';
require __DIR__ . '/../includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Administración</p>
          <h1>Gestión de turnos</h1>
          <p>Altas, modificaciones y bajas de los turnos del taller.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <?php if ($status): ?>
            <div class="alert alert-<?= e($status[0]) ?>" role="status"><?= e($status[1]) ?></div>
          <?php endif; ?>

          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a href="crear.php" class="btn btn-bx-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo turno</a>
            <form class="d-flex gap-2 flex-grow-1 flex-sm-grow-0" method="get" action="index.php">
              <label class="visually-hidden" for="estado">Filtrar por estado</label>
              <select class="form-select" id="estado" name="estado">
                <option value="">Todos los estados</option>
                <?php foreach (ESTADOS_TURNO as $est): ?>
                  <option value="<?= e($est) ?>"<?= $filtro === $est ? ' selected' : '' ?>><?= e($est) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-bx-outline-dark" type="submit">Filtrar</button>
            </form>
          </div>

          <?php if ($turnos === []): ?>
            <div class="alert alert-info">No hay turnos registrados<?= $filtro !== '' ? ' con estado «' . e($filtro) . '»' : '' ?>.</div>
          <?php else: ?>
            <div class="table-responsive-lg">
              <table class="table table-striped table-hover align-middle bx-table">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Vehículo</th>
                    <th scope="col">Servicio</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Síntoma</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="text-end pe-3">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($turnos as $t): ?>
                    <tr>
                      <td data-label="ID">#<?= e($t['id']) ?></td>
                      <td data-label="Cliente">
                        <span>
                          <strong><?= e($t['nombre']) ?></strong><br />
                          <small class="text-muted text-nowrap"><?= e($t['telefono']) ?></small>
                        </span>
                      </td>
                      <td data-label="Vehículo"><?= e($t['vehiculo']) ?></td>
                      <td data-label="Servicio"><?= e($t['servicio']) ?></td>
                      <td data-label="Fecha" class="text-nowrap"><span><?= e(date('d/m/Y', strtotime($t['fecha']))) ?><br /><small class="text-muted"><?= e($t['horario']) ?></small></span></td>
                      <td data-label="Síntoma" class="small"><?= e(mb_strimwidth($t['sintoma'], 0, 60, '…')) ?></td>
                      <td data-label="Estado"><span><span class="badge <?= e($colores[$t['estado']] ?? 'text-bg-light') ?>"><?= e($t['estado']) ?></span></span></td>
                      <td>
                        <div class="bx-actions">
                          <a href="editar.php?id=<?= e($t['id']) ?>" class="btn btn-sm btn-warning text-nowrap"><i class="bi bi-pencil-square" aria-hidden="true"></i> Editar</a>
                          <form class="m-0" method="post" action="eliminar.php" onsubmit="return confirm('¿Confirma eliminar el turno de <?= e(addslashes($t['nombre'])) ?>?')">
                            <input type="hidden" name="id" value="<?= e($t['id']) ?>" />
                            <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                            <button class="btn btn-sm btn-danger text-nowrap" type="submit"><i class="bi bi-trash" aria-hidden="true"></i> Eliminar</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <p class="text-muted small mb-0"><?= count($turnos) ?> turno(s) listados.</p>
          <?php endif; ?>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
