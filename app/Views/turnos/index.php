<?php
// Vista: listado de turnos del panel (Read)
// Variables: $turnos, $conteos, $catalogos, $filtro, $status, $csrf
// TP9: el estado de cada turno se cambia desde la tabla con fetch
//      a api/turno_estado.php (js/admin-turnos.js).
// Rediseño: saludo + acción principal, tarjetas resumen por estado, filtros
// en píldoras, acciones con íconos y baja confirmada en un modal (sigue siendo POST + CSRF).
$colores = [
    'Pendiente'  => 'text-bg-warning',
    'Confirmado' => 'text-bg-primary',
    'Finalizado' => 'text-bg-success',
    'Cancelado'  => 'text-bg-secondary',
];
// Ícono y tono de cada estado para las tarjetas resumen
$aspecto = [
    'Pendiente'  => ['bi-hourglass-split', 'pendiente'],
    'Confirmado' => ['bi-calendar-check', 'confirmado'],
    'Finalizado' => ['bi-check2-circle', 'finalizado'],
    'Cancelado'  => ['bi-x-circle', 'cancelado'],
];
$total      = array_sum($conteos);
$usuarioAdm = usuarioActual();
$nombreFiltro = $filtro !== '' ? $catalogos['estados'][(int) $filtro] : null;
?>
    <main id="contenido" class="bx-admin">
      <section class="bx-page-hero bx-admin-hero">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4">
          <div>
            <p class="eyebrow">Panel de administración</p>
            <h1>Hola, <?= e($usuarioAdm['nombre'] ?? 'equipo') ?></h1>
            <p>Estos son los turnos del taller. Cambiá el estado desde la tabla o editá los datos de cada uno.</p>
          </div>
          <a href="crear.php" class="btn btn-bx-primary btn-lg flex-shrink-0"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo turno</a>
        </div>
      </section>

      <section class="bx-section bx-section-muted bx-admin-body">
        <div class="container">
          <?php if ($status): ?>
            <div class="alert alert-<?= e($status[0]) ?>" role="status"><?= e($status[1]) ?></div>
          <?php endif; ?>

          <h2 class="visually-hidden">Resumen de turnos por estado</h2>
          <ul class="bx-stats list-unstyled" aria-label="Turnos por estado">
            <li class="bx-stat bx-stat--total">
              <span class="bx-stat-icon" aria-hidden="true"><i class="bi bi-calendar3"></i></span>
              <span class="bx-stat-num" data-conteo="total"><?= e($total) ?></span>
              <span class="bx-stat-label">Turnos en total</span>
            </li>
            <?php foreach ($catalogos['estados'] as $idEstado => $est): ?>
              <?php [$icono, $tono] = $aspecto[$est] ?? ['bi-circle', 'cancelado']; ?>
              <li class="bx-stat bx-stat--<?= e($tono) ?>">
                <span class="bx-stat-icon" aria-hidden="true"><i class="bi <?= e($icono) ?>"></i></span>
                <span class="bx-stat-num" data-conteo="<?= e($idEstado) ?>"><?= e($conteos[$idEstado] ?? 0) ?></span>
                <span class="bx-stat-label"><?= e($est) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>

          <div class="bx-admin-card">
            <div class="bx-admin-card-head">
              <div>
                <h2 class="h5 mb-1">Turnos<?= $nombreFiltro ? ' · ' . e($nombreFiltro) : '' ?></h2>
                <p class="mb-0 small text-secondary" id="conteo-turnos"><?= count($turnos) ?> turno(s) listados</p>
              </div>
              <nav aria-label="Filtrar turnos por estado">
                <ul class="bx-chips list-unstyled mb-0">
                  <li><a class="bx-chip<?= $filtro === '' ? ' active' : '' ?>" href="index.php"<?= $filtro === '' ? ' aria-current="page"' : '' ?>>Todos <span class="bx-chip-num"><?= e($total) ?></span></a></li>
                  <?php foreach ($catalogos['estados'] as $idEstado => $est): ?>
                    <?php $activo = $filtro === (string) $idEstado; ?>
                    <li><a class="bx-chip<?= $activo ? ' active' : '' ?>" href="index.php?estado=<?= e($idEstado) ?>"<?= $activo ? ' aria-current="page"' : '' ?>><?= e($est) ?> <span class="bx-chip-num" data-conteo="<?= e($idEstado) ?>"><?= e($conteos[$idEstado] ?? 0) ?></span></a></li>
                  <?php endforeach; ?>
                </ul>
              </nav>
            </div>

            <?php if ($turnos === []): ?>
              <div class="bx-empty">
                <img src="../img/ilustraciones/estado-vacio.svg" alt="" width="200" height="150" />
                <h3 class="h5">No hay turnos<?= $nombreFiltro ? ' con estado «' . e($nombreFiltro) . '»' : ' registrados' ?></h3>
                <p class="text-secondary">Cuando un cliente pida un turno o lo cargues vos, va a aparecer acá.</p>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                  <?php if ($nombreFiltro): ?>
                    <a class="btn btn-bx-outline-dark" href="index.php">Ver todos los turnos</a>
                  <?php endif; ?>
                  <a class="btn btn-bx-primary" href="crear.php"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo turno</a>
                </div>
              </div>
            <?php else: ?>
              <div class="table-responsive-lg">
                <table class="table table-hover align-middle bx-table bx-admin-table" id="tabla-turnos"
                       data-api="../api/turno_estado.php" data-csrf="<?= e($csrf) ?>"
                       data-colores="<?= e(json_encode($colores, JSON_UNESCAPED_UNICODE)) ?>">
                  <caption class="visually-hidden">Turnos del taller<?= $nombreFiltro ? ' con estado ' . e($nombreFiltro) : '' ?></caption>
                  <thead class="table-dark">
                    <tr>
                      <th scope="col">ID</th>
                      <th scope="col">Cliente</th>
                      <th scope="col">Vehículo</th>
                      <th scope="col">Servicios</th>
                      <th scope="col">Fecha</th>
                      <th scope="col">Síntoma</th>
                      <th scope="col">Estado</th>
                      <th scope="col" class="text-end pe-3">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($turnos as $t): ?>
                      <tr>
                        <td data-label="ID" class="bx-col-id">#<?= e($t['id']) ?></td>
                        <td data-label="Cliente">
                          <span>
                            <strong><?= e($t['nombre'] . ' ' . $t['apellido']) ?></strong><br />
                            <small class="text-secondary text-nowrap"><i class="bi bi-telephone" aria-hidden="true"></i> <?= e($t['telefono']) ?></small>
                          </span>
                        </td>
                        <td data-label="Vehículo">
                          <span>
                            <?= e($t['marca'] . ' ' . $t['modelo'] . ' ' . $t['anio']) ?><br />
                            <small class="bx-patente"><?= e($t['patente']) ?></small>
                          </span>
                        </td>
                        <td data-label="Servicios">
                          <span>
                            <?= e($t['servicios'] ?? '—') ?><br />
                            <small class="text-secondary text-nowrap">$ <?= e(number_format((float) $t['total'], 0, ',', '.')) ?></small>
                          </span>
                        </td>
                        <td data-label="Fecha" class="text-nowrap"><span><?= e(date('d/m/Y', strtotime($t['fecha']))) ?><br /><small class="text-secondary"><?= e($t['horario']) ?></small></span></td>
                        <td data-label="Síntoma" class="small bx-col-sintoma"><?= e(mb_strimwidth($t['sintoma'], 0, 60, '…')) ?></td>
                        <td data-label="Estado">
                          <span>
                            <span class="badge bx-estado-badge <?= e($colores[$t['estado']] ?? 'text-bg-light') ?>"><?= e($t['estado']) ?></span>
                            <?php // TP9: con JS el badge se reemplaza por este select (cambio vía fetch, sin recargar) ?>
                            <select class="form-select form-select-sm w-auto bx-estado-select <?= e($colores[$t['estado']] ?? 'text-bg-light') ?>" data-id="<?= e($t['id']) ?>" aria-label="Estado del turno #<?= e($t['id']) ?>" hidden>
                              <?php foreach ($catalogos['estados'] as $idEstado => $est): ?>
                                <option value="<?= e($idEstado) ?>"<?= $est === $t['estado'] ? ' selected' : '' ?>><?= e($est) ?></option>
                              <?php endforeach; ?>
                            </select>
                          </span>
                        </td>
                        <td>
                          <div class="bx-actions">
                            <a href="editar.php?id=<?= e($t['id']) ?>" class="btn btn-sm bx-btn-icon" data-bs-toggle="tooltip" data-bs-title="Editar turno #<?= e($t['id']) ?>">
                              <i class="bi bi-pencil-square" aria-hidden="true"></i> <span class="bx-accion-texto">Editar<span class="visually-hidden"> turno #<?= e($t['id']) ?></span></span>
                            </a>
                            <form class="m-0 bx-form-baja" method="post" action="eliminar.php"
                                  data-turno="<?= e($t['id']) ?>" data-cliente="<?= e($t['nombre'] . ' ' . $t['apellido']) ?>"
                                  data-detalle="<?= e($t['marca'] . ' ' . $t['modelo'] . ' · ' . date('d/m/Y', strtotime($t['fecha']))) ?>">
                              <input type="hidden" name="id" value="<?= e($t['id']) ?>" />
                              <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                              <button class="btn btn-sm bx-btn-icon bx-btn-icon--danger" type="submit" data-bs-toggle="tooltip" data-bs-title="Eliminar turno #<?= e($t['id']) ?>">
                                <i class="bi bi-trash3" aria-hidden="true"></i> <span class="bx-accion-texto">Eliminar<span class="visually-hidden"> turno #<?= e($t['id']) ?></span></span>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>

          <div class="toast-container position-fixed bottom-0 end-0 p-3" id="avisos-turnos" aria-live="polite" aria-atomic="true"></div>
        </div>
      </section>

      <?php // Confirmación de baja: js/admin-turnos.js lo abre y, al confirmar, envía el formulario POST de esa fila ?>
      <div class="modal fade bx-modal" id="modal-baja" tabindex="-1" aria-labelledby="modal-baja-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body text-center">
              <span class="bx-modal-icon" aria-hidden="true"><i class="bi bi-trash3"></i></span>
              <h2 class="h5" id="modal-baja-titulo">¿Eliminar el turno <span data-baja="turno"></span>?</h2>
              <p class="mb-1"><strong data-baja="cliente"></strong></p>
              <p class="text-secondary small" data-baja="detalle"></p>
              <p class="small mb-0">Se borra el turno y sus servicios. Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-bx-outline-dark" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn bx-btn-danger" id="confirmar-baja"><i class="bi bi-trash3" aria-hidden="true"></i> Sí, eliminar</button>
            </div>
          </div>
        </div>
      </div>
    </main>
