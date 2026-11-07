<?php
// Vista: solicitud pública de turno
// Variables: $datos, $errores, $errorGeneral, $csrf, $catalogos, $horarios, $exito, $enviado, $turnoId, $emailFijo, $admin
// TP9: $consultaId, $consultaPatente, $consulta, $consultaError (panel «Consultá tu turno»)
// Presentación: $serviciosDetalle, $fechaMinima

// Badge de color por estado del turno (mismo criterio que js/turnos.js)
$claseEstado = fn (string $estado): string => [
    'Pendiente'  => 'text-bg-warning',
    'Confirmado' => 'text-bg-primary',
    'Finalizado' => 'text-bg-success',
    'Cancelado'  => 'text-bg-secondary',
][$estado] ?? 'text-bg-secondary';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Turnos</p>
          <h1><?= $exito ? '¡Turno solicitado!' : 'Sacá tu turno' ?></h1>
          <p><?= $exito ? 'Recibimos tu solicitud. Te contactamos por WhatsApp para confirmar día y horario.' : 'Completá tus datos, los del auto y elegí el servicio. Te confirmamos por WhatsApp en menos de 2 hs hábiles.' ?></p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">
          <div class="row g-4 g-lg-5">
            <div class="col-lg-8">
              <?php if ($exito): ?>
                <div class="bx-form-card bx-success-card" role="status">
                  <div class="row g-4 align-items-center">
                    <div class="col-md-5">
                      <img src="img/ilustraciones/auto-entregado.svg" width="480" height="320" alt="" />
                    </div>
                    <div class="col-md-7">
                      <p class="bx-badge-ok mb-3"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Solicitud registrada</p>
                      <h2 class="h3 mb-2">Tu número de turno es <span class="bx-turno-num">N° <?= e($turnoId) ?></span></h2>
                      <p class="text-secondary mb-0">Guardalo: con este número y tu patente podés consultar el estado en el panel «Consultá tu turno».</p>
                    </div>
                  </div>
                  <hr class="my-4" />
                  <h3 class="h5">Resumen de tu solicitud</h3>
                  <dl class="bx-resumen mb-4">
                    <div><dt>Cliente</dt><dd><?= e($datos['nombre'] . ' ' . $datos['apellido']) ?></dd></div>
                    <div><dt>Contacto</dt><dd><?= e($datos['email']) ?> · <?= e($datos['telefono']) ?></dd></div>
                    <div><dt>Vehículo</dt><dd><?= e($catalogos['marcas'][(int) $datos['marca_id']] . ' ' . $datos['modelo'] . ' ' . $datos['anio']) ?> (<?= e($datos['patente']) ?>)</dd></div>
                    <div><dt>Servicios</dt><dd><?= e(implode(', ', array_map(fn ($s) => $catalogos['servicios'][(int) $s], $datos['servicios']))) ?></dd></div>
                    <div><dt>Fecha preferida</dt><dd><?= e(date('d/m/Y', strtotime($datos['fecha']))) ?> (<?= e($datos['horario']) ?>)</dd></div>
                    <div><dt>Síntoma</dt><dd><?= e($datos['sintoma']) ?></dd></div>
                  </dl>
                  <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-bx-primary" href="turnos.php?consulta_id=<?= e($turnoId) ?>&amp;patente=<?= e($datos['patente']) ?>#titulo-consulta"><i class="bi bi-search" aria-hidden="true"></i> Ver estado del turno</a>
                    <a class="btn btn-bx-outline-dark" href="turnos.php"><i class="bi bi-plus-lg" aria-hidden="true"></i> Sacar otro turno</a>
                  </div>
                </div>
              <?php else: ?>
                <div class="bx-form-card">
                  <?php if ($errorGeneral): ?>
                    <div class="alert alert-danger" role="alert"><?= e($errorGeneral) ?></div>
                  <?php elseif ($enviado): ?>
                    <div class="alert alert-danger" role="alert">
                      <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
                    </div>
                  <?php endif; ?>

                  <ol class="bx-stepper" aria-label="Pasos del formulario">
                    <li><span aria-hidden="true">1</span> Tus datos</li>
                    <li><span aria-hidden="true">2</span> Vehículo</li>
                    <li><span aria-hidden="true">3</span> Turno</li>
                  </ol>

                  <form class="bx-form" method="post" action="turnos.php" novalidate>
                    <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                    <p class="bx-required-note">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>
                    <?php require __DIR__ . '/_campos.php'; ?>
                    <button class="btn btn-bx-primary btn-lg mt-4" type="submit"><i class="bi bi-calendar-check" aria-hidden="true"></i> Confirmar turno</button>
                  </form>
                </div>
              <?php endif; ?>
            </div>
            <div class="col-lg-4">
              <aside class="bx-side-panel mb-4" aria-labelledby="titulo-consulta">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <span class="bx-card-icon mb-0" aria-hidden="true"><span class="bx-icon bx-icon--turno"></span></span>
                  <h2 class="h4 mb-0" id="titulo-consulta">Consultá tu turno</h2>
                </div>
                <p class="small text-secondary">Ingresá el número de solicitud y la patente del vehículo.</p>
                <form class="bx-form" id="form-consulta-turno" method="get" action="turnos.php#titulo-consulta"
                      data-api="api/consultar_turno.php" novalidate>
                  <div class="row g-2">
                    <div class="col-5 col-lg-12 col-xl-5">
                      <label class="form-label" for="consulta_id">N° de turno</label>
                      <input class="form-control" id="consulta_id" name="consulta_id" type="text" inputmode="numeric"
                             pattern="[0-9]+" maxlength="10" autocomplete="off" placeholder="Ej: 12" required value="<?= e($consultaId) ?>" />
                    </div>
                    <div class="col-7 col-lg-12 col-xl-7">
                      <label class="form-label" for="consulta_patente">Patente</label>
                      <input class="form-control text-uppercase" id="consulta_patente" name="patente" type="text"
                             maxlength="10" autocomplete="off" placeholder="ABC123" required value="<?= e($consultaPatente) ?>" />
                    </div>
                  </div>
                  <button class="btn btn-bx-outline-dark w-100 mt-3" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Consultar estado</button>
                </form>
                <div class="mt-3" id="resultado-consulta" aria-live="polite">
                  <?php if ($consultaError !== null): ?>
                    <div class="alert alert-danger mb-0" role="alert"><?= e($consultaError) ?></div>
                  <?php elseif ($consulta !== null): ?>
                    <div class="bx-turno-card">
                      <div class="bx-turno-card-head">
                        <p class="mb-0">Turno <strong>N° <?= e($consulta['id']) ?></strong></p>
                        <span class="badge <?= e($claseEstado($consulta['estado'])) ?>"><?= e($consulta['estado']) ?></span>
                      </div>
                      <dl class="bx-resumen bx-resumen--compacto mb-0">
                        <div><dt>Vehículo</dt><dd><?= e($consulta['vehiculo']) ?> (<?= e($consulta['patente']) ?>)</dd></div>
                        <div><dt>Fecha</dt><dd><?= e(date('d/m/Y', strtotime($consulta['fecha']))) ?> (<?= e($consulta['horario']) ?>)</dd></div>
                        <div><dt>Servicios</dt><dd><?= e(implode(', ', $consulta['servicios']) ?: '—') ?></dd></div>
                      </dl>
                    </div>
                  <?php endif; ?>
                </div>
              </aside>

              <aside class="bx-side-panel" aria-labelledby="titulo-horarios">
                <h2 class="h5" id="titulo-horarios">Horarios del taller</h2>
                <ul class="bx-hours mb-3">
                  <li><span>Lunes a viernes</span> <strong>8:00 – 18:00</strong></li>
                  <li><span>Sábados</span> <strong>8:00 – 13:00</strong></li>
                  <li><span>Domingos y feriados</span> <strong>Cerrado</strong></li>
                </ul>
                <p class="small text-secondary mb-0"><i class="bi bi-whatsapp" aria-hidden="true"></i> Confirmación en menos de 2 hs hábiles.</p>
              </aside>
            </div>
          </div>
        </div>
      </section>
    </main>
