<?php
// Vista: solicitud pública de turno
// Variables: $datos, $errores, $errorGeneral, $csrf, $catalogos, $horarios, $exito, $enviado, $turnoId, $emailFijo, $admin
// TP9: $consultaId, $consultaPatente, $consulta, $consultaError (panel «Consultá tu turno»)
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Turnos</p>
          <h1>Agendá tu visita</h1>
          <p>Elegí día, horario y servicio. Te confirmamos por WhatsApp.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row g-4">
            <div class="col-lg-8">
              <h2 class="h3 mb-3">Datos del turno</h2>

              <?php if ($exito): ?>
                <div class="alert alert-success" role="status">
                  <strong>Solicitud N° <?= e($turnoId) ?> registrada correctamente.</strong> Te contactaremos para confirmar el turno.
                  Con este número y tu patente podés consultar el estado en el panel «Consultá tu turno».
                </div>
                <div class="bx-card p-4 mb-4">
                  <h3 class="h5">Resumen de tu solicitud</h3>
                  <dl class="row mb-0">
                    <dt class="col-sm-4">Cliente</dt>
                    <dd class="col-sm-8"><?= e($datos['nombre'] . ' ' . $datos['apellido']) ?></dd>
                    <dt class="col-sm-4">Contacto</dt>
                    <dd class="col-sm-8"><?= e($datos['email']) ?> · <?= e($datos['telefono']) ?></dd>
                    <dt class="col-sm-4">Vehículo</dt>
                    <dd class="col-sm-8"><?= e($catalogos['marcas'][(int) $datos['marca_id']] . ' ' . $datos['modelo'] . ' ' . $datos['anio']) ?> (<?= e($datos['patente']) ?>)</dd>
                    <dt class="col-sm-4">Servicios</dt>
                    <dd class="col-sm-8"><?= e(implode(', ', array_map(fn ($s) => $catalogos['servicios'][(int) $s], $datos['servicios']))) ?></dd>
                    <dt class="col-sm-4">Fecha preferida</dt>
                    <dd class="col-sm-8"><?= e($datos['fecha']) ?> (<?= e($datos['horario']) ?>)</dd>
                    <dt class="col-sm-4">Síntoma</dt>
                    <dd class="col-sm-8"><?= e($datos['sintoma']) ?></dd>
                  </dl>
                </div>
              <?php elseif ($errorGeneral): ?>
                <div class="alert alert-danger" role="alert"><?= e($errorGeneral) ?></div>
              <?php elseif ($enviado): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
                </div>
              <?php endif; ?>

              <form class="bx-form" method="post" action="turnos.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                <?php require __DIR__ . '/_campos.php'; ?>
                <button class="btn btn-bx-primary mt-4" type="submit">Confirmar turno</button>
              </form>
            </div>
            <div class="col-lg-4">
              <aside class="bx-side-panel">
                <h2 class="h4">Horarios</h2>
                <ul class="mb-0 ps-3">
                  <li>Lun a Vie: 8:00 – 18:00</li>
                  <li>Sábados: 8:00 – 13:00</li>
                  <li>Domingos: cerrado</li>
                  <li>Confirmación en menos de 2 hs hábiles</li>
                </ul>
              </aside>

              <aside class="bx-side-panel mt-4" aria-labelledby="titulo-consulta">
                <h2 class="h4" id="titulo-consulta">Consultá tu turno</h2>
                <p class="small text-secondary">Ingresá el número de solicitud y la patente del vehículo.</p>
                <form id="form-consulta-turno" method="get" action="turnos.php#titulo-consulta"
                      data-api="api/consultar_turno.php" novalidate>
                  <div class="mb-2">
                    <label class="form-label" for="consulta_id">N° de turno</label>
                    <input class="form-control" id="consulta_id" name="consulta_id" type="text" inputmode="numeric"
                           pattern="[0-9]+" maxlength="10" autocomplete="off" required value="<?= e($consultaId) ?>" />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="consulta_patente">Patente</label>
                    <input class="form-control text-uppercase" id="consulta_patente" name="patente" type="text"
                           maxlength="10" autocomplete="off" placeholder="ABC123 o AB123CD" required value="<?= e($consultaPatente) ?>" />
                  </div>
                  <button class="btn btn-bx-primary w-100" type="submit">Consultar estado</button>
                </form>
                <div class="mt-3" id="resultado-consulta" aria-live="polite">
                  <?php if ($consultaError !== null): ?>
                    <div class="alert alert-danger mb-0" role="alert"><?= e($consultaError) ?></div>
                  <?php elseif ($consulta !== null): ?>
                    <div class="alert alert-info mb-0">
                      <p class="fw-semibold mb-2">Turno N° <?= e($consulta['id']) ?>: <span class="badge text-bg-dark"><?= e($consulta['estado']) ?></span></p>
                      <dl class="row small mb-0">
                        <dt class="col-5">Vehículo</dt>
                        <dd class="col-7"><?= e($consulta['vehiculo']) ?> (<?= e($consulta['patente']) ?>)</dd>
                        <dt class="col-5">Fecha</dt>
                        <dd class="col-7"><?= e(date('d/m/Y', strtotime($consulta['fecha']))) ?> (<?= e($consulta['horario']) ?>)</dd>
                        <dt class="col-5">Servicios</dt>
                        <dd class="col-7 mb-0"><?= e(implode(', ', $consulta['servicios']) ?: '—') ?></dd>
                      </dl>
                    </div>
                  <?php endif; ?>
                </div>
              </aside>
            </div>
          </div>
        </div>
      </section>
    </main>
