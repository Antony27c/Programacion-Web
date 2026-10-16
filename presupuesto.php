<?php
// ============================================================
// presupuesto.php — Formulario POST: solicitud de presupuesto
// ============================================================
require_once __DIR__ . '/includes/funciones.php';

$urgenciasValidas = ['Normal', 'Urgente'];

$datos   = array_fill_keys(['nombre', 'email', 'telefono', 'vehiculo', 'km', 'urgencia', 'problema'], '');
$errores = [];
$exito   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $_) {
        $datos[$campo] = limpiar($_POST[$campo] ?? '');
    }
    if ($datos['urgencia'] === '') {
        $datos['urgencia'] = 'Normal';
    }

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Campo obligatorio.';
    }

    if ($datos['email'] === '') {
        $errores['email'] = 'Ingresá un email.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresá un email válido.';
    }

    if ($datos['telefono'] === '') {
        $errores['telefono'] = 'Campo obligatorio.';
    } elseif (!preg_match('/^[0-9+\s()-]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'Ingresá un teléfono válido.';
    }

    if ($datos['vehiculo'] === '') {
        $errores['vehiculo'] = 'Campo obligatorio.';
    }

    // km es opcional, pero si viene debe ser numérico
    if ($datos['km'] !== '' && !preg_match('/^[0-9.,\s]{1,12}(km)?$/i', $datos['km'])) {
        $errores['km'] = 'Ingresá el kilometraje en números (ej: 85000).';
    }

    if (!in_array($datos['urgencia'], $urgenciasValidas, true)) {
        $errores['urgencia'] = 'Seleccioná una urgencia válida.';
    }

    if ($datos['problema'] === '') {
        $errores['problema'] = 'Describí el problema.';
    } elseif (mb_strlen($datos['problema']) < 10) {
        $errores['problema'] = 'Describí el problema con más detalle (mínimo 10 caracteres).';
    }

    $exito = ($errores === []);
}

$titulo      = 'BOXLY | Presupuesto';
$descripcion = 'Pedí un presupuesto online en BOXLY Salta.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Presupuesto</p>
          <h1>Pedí un presupuesto</h1>
          <p>Contanos el problema y te respondemos con un estimado claro.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">
          <div class="bx-card p-4 p-md-5">
            <h2 class="h3 mb-4">Formulario de presupuesto</h2>

            <?php if ($exito): ?>
              <div class="alert alert-success" role="status">
                <strong>Solicitud enviada.</strong> Te enviaremos el estimado a <strong><?= e($datos['email']) ?></strong> a la brevedad.
              </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
              <div class="alert alert-danger" role="alert">
                <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
              </div>
            <?php endif; ?>

            <form class="bx-form" method="post" action="presupuesto.php" novalidate>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="nombre">Nombre</label>
                  <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" required autocomplete="name" value="<?= valor($datos, 'nombre') ?>" />
                  <?= errorCampo($errores, 'nombre') ?>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="email">Email</label>
                  <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" required autocomplete="email" value="<?= valor($datos, 'email') ?>" />
                  <?= errorCampo($errores, 'email') ?>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="telefono">Teléfono</label>
                  <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" required autocomplete="tel" value="<?= valor($datos, 'telefono') ?>" />
                  <?= errorCampo($errores, 'telefono') ?>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="vehiculo">Vehículo</label>
                  <input class="form-control<?= claseError($errores, 'vehiculo') ?>" id="vehiculo" name="vehiculo" required placeholder="Marca / modelo / año" value="<?= valor($datos, 'vehiculo') ?>" />
                  <?= errorCampo($errores, 'vehiculo') ?>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="km">Kilometraje</label>
                  <input class="form-control<?= claseError($errores, 'km') ?>" id="km" name="km" placeholder="Ej: 85.000 km" value="<?= valor($datos, 'km') ?>" />
                  <?= errorCampo($errores, 'km') ?>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="urgencia">Urgencia</label>
                  <select class="form-select<?= claseError($errores, 'urgencia') ?>" id="urgencia" name="urgencia">
                    <?php foreach ($urgenciasValidas as $u): ?>
                      <option value="<?= e($u) ?>"<?= seleccionado($datos, 'urgencia', $u) ?>><?= e($u) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <?= errorCampo($errores, 'urgencia') ?>
                </div>
                <div class="col-12">
                  <label class="form-label" for="problema">Descripción del problema</label>
                  <textarea class="form-control<?= claseError($errores, 'problema') ?>" id="problema" name="problema" rows="4" required placeholder="Ej: vibra al frenar / check engine / no arranca en frío"><?= valor($datos, 'problema') ?></textarea>
                  <?= errorCampo($errores, 'problema') ?>
                </div>
              </div>
              <button class="btn btn-bx-primary mt-4" type="submit">Enviar solicitud</button>
            </form>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
