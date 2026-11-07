<?php
// Vista: solicitud pública de turno
// Variables: $datos, $errores, $errorGeneral, $csrf, $catalogos, $horarios, $exito, $enviado, $turnoId, $emailFijo, $admin
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
            </div>
          </div>
        </div>
      </section>
    </main>
