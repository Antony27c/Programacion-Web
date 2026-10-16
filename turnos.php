<?php
// ============================================================
// turnos.php — Formulario POST: solicitud de turno
// Sanitización + validación en servidor + persistencia (TP5)
// ============================================================
require_once __DIR__ . '/includes/funciones.php';

$serviciosValidos = ['Service', 'Frenos', 'Diagnóstico', 'Otro'];
$horariosValidos  = ['Mañana', 'Tarde'];

$datos   = array_fill_keys(['nombre', 'telefono', 'vehiculo', 'servicio', 'fecha', 'horario', 'sintoma'], '');
$errores = [];
$exito   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Sanitizar TODAS las entradas antes de usarlas
    foreach ($datos as $campo => $_) {
        $datos[$campo] = limpiar($_POST[$campo] ?? '');
    }

    // 2) Validación en servidor
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Ingresá tu nombre y apellido.';
    } elseif (mb_strlen($datos['nombre']) < 3) {
        $errores['nombre'] = 'El nombre es demasiado corto.';
    }

    if ($datos['telefono'] === '') {
        $errores['telefono'] = 'Ingresá un teléfono de contacto.';
    } elseif (!preg_match('/^[0-9+\s()-]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'Ingresá un teléfono válido (números, +, espacios o guiones).';
    }

    if ($datos['vehiculo'] === '') {
        $errores['vehiculo'] = 'Indicá marca, modelo y año del vehículo.';
    }

    if (!in_array($datos['servicio'], $serviciosValidos, true)) {
        $errores['servicio'] = 'Seleccioná un servicio de la lista.';
    }

    if ($datos['fecha'] === '') {
        $errores['fecha'] = 'Elegí una fecha.';
    } else {
        $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha']);
        if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha']) {
            $errores['fecha'] = 'La fecha ingresada no es válida.';
        } elseif ($fecha < new DateTime('today')) {
            $errores['fecha'] = 'La fecha no puede ser anterior a hoy.';
        }
    }

    if (!in_array($datos['horario'], $horariosValidos, true)) {
        $errores['horario'] = 'Elegí un horario.';
    }

    if ($datos['sintoma'] === '') {
        $errores['sintoma'] = 'Contanos qué le pasa al auto.';
    } elseif (mb_strlen($datos['sintoma']) < 10) {
        $errores['sintoma'] = 'Describí el problema con más detalle (mínimo 10 caracteres).';
    }

    $exito = ($errores === []);
}

$titulo      = 'BOXLY | Agendar turno';
$descripcion = 'Agendá tu turno en el taller BOXLY de Salta.';
require __DIR__ . '/includes/header.php';
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
                  <strong>Solicitud enviada correctamente.</strong> Te contactaremos para confirmar el turno.
                </div>
                <div class="bx-card p-4 mb-4">
                  <h3 class="h5">Resumen de tu solicitud</h3>
                  <dl class="row mb-0">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8"><?= e($datos['nombre']) ?></dd>
                    <dt class="col-sm-4">Teléfono</dt>
                    <dd class="col-sm-8"><?= e($datos['telefono']) ?></dd>
                    <dt class="col-sm-4">Vehículo</dt>
                    <dd class="col-sm-8"><?= e($datos['vehiculo']) ?></dd>
                    <dt class="col-sm-4">Servicio</dt>
                    <dd class="col-sm-8"><?= e($datos['servicio']) ?></dd>
                    <dt class="col-sm-4">Fecha preferida</dt>
                    <dd class="col-sm-8"><?= e($datos['fecha']) ?> (<?= e($datos['horario']) ?>)</dd>
                    <dt class="col-sm-4">Síntoma</dt>
                    <dd class="col-sm-8"><?= e($datos['sintoma']) ?></dd>
                  </dl>
                </div>
              <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
                </div>
              <?php endif; ?>

              <form class="bx-form" method="post" action="turnos.php" novalidate>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="nombre">Nombre y apellido</label>
                    <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" required autocomplete="name" placeholder="Ej: Antonio Chocobar" value="<?= valor($datos, 'nombre') ?>" />
                    <?= errorCampo($errores, 'nombre') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
                    <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" required autocomplete="tel" placeholder="+54 387 ..." value="<?= valor($datos, 'telefono') ?>" />
                    <?= errorCampo($errores, 'telefono') ?>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="vehiculo">Vehículo (marca, modelo, año)</label>
                    <input class="form-control<?= claseError($errores, 'vehiculo') ?>" id="vehiculo" name="vehiculo" required placeholder="Ej: Volkswagen Gol 2018" value="<?= valor($datos, 'vehiculo') ?>" />
                    <?= errorCampo($errores, 'vehiculo') ?>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label" for="servicio">Servicio</label>
                    <select class="form-select<?= claseError($errores, 'servicio') ?>" id="servicio" name="servicio" required>
                      <option value="">Elegí una opción</option>
                      <?php foreach ($serviciosValidos as $s): ?>
                        <option value="<?= e($s) ?>"<?= seleccionado($datos, 'servicio', $s) ?>><?= e($s) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <?= errorCampo($errores, 'servicio') ?>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label" for="fecha">Fecha preferida</label>
                    <input class="form-control<?= claseError($errores, 'fecha') ?>" id="fecha" name="fecha" type="date" required value="<?= valor($datos, 'fecha') ?>" />
                    <?= errorCampo($errores, 'fecha') ?>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label" for="horario">Horario</label>
                    <select class="form-select<?= claseError($errores, 'horario') ?>" id="horario" name="horario" required>
                      <option value="">Elegí</option>
                      <?php foreach ($horariosValidos as $h): ?>
                        <option value="<?= e($h) ?>"<?= seleccionado($datos, 'horario', $h) ?>><?= e($h) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <?= errorCampo($errores, 'horario') ?>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="sintoma">¿Qué le pasa al auto?</label>
                    <textarea class="form-control<?= claseError($errores, 'sintoma') ?>" id="sintoma" name="sintoma" rows="4" required placeholder="Describí el ruido, luz en tablero o síntoma..."><?= valor($datos, 'sintoma') ?></textarea>
                    <?= errorCampo($errores, 'sintoma') ?>
                  </div>
                </div>
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
<?php require __DIR__ . '/includes/footer.php'; ?>
