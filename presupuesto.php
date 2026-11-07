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

    // Longitud máxima y codificación válida (htmlspecialchars devuelve '' ante UTF-8 inválido)
    foreach (['nombre' => 60, 'email' => 150, 'vehiculo' => 80, 'problema' => 2000] as $campo => $max) {
        if (!mb_check_encoding($datos[$campo], 'UTF-8')) {
            $errores[$campo] = 'Contiene caracteres no válidos.';
        } elseif (!isset($errores[$campo]) && mb_strlen($datos[$campo]) > $max) {
            $errores[$campo] = "No puede superar los $max caracteres.";
        }
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
          <h1>Pedí un presupuesto sin compromiso</h1>
          <p>Contanos qué le pasa al auto y te respondemos con un estimado claro. El precio final lo confirmamos por escrito después del diagnóstico.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">
          <div class="row g-4 g-lg-5">
            <div class="col-lg-8">
              <div class="bx-form-card">
                <h2 class="h3 mb-1">Formulario de presupuesto</h2>
                <p class="text-secondary mb-4">Te lleva menos de 2 minutos.</p>

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
                  <p class="bx-required-note">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>
                  <fieldset class="mb-4">
                    <legend>Tus datos</legend>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" required maxlength="60" autocomplete="name" placeholder="Ej: Diego Romero" value="<?= valor($datos, 'nombre') ?>" />
                        <?= errorCampo($errores, 'nombre') ?>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" required maxlength="150" autocomplete="email" placeholder="tu@correo.com" aria-describedby="ayudaEmail" value="<?= valor($datos, 'email') ?>" />
                        <?= errorCampo($errores, 'email') ?>
                        <div class="form-text" id="ayudaEmail">Ahí te mandamos el estimado.</div>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
                        <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" required maxlength="20" autocomplete="tel" placeholder="+54 387 ..." aria-describedby="ayudaTelefono" value="<?= valor($datos, 'telefono') ?>" />
                        <?= errorCampo($errores, 'telefono') ?>
                        <div class="form-text" id="ayudaTelefono">Solo números, espacios, +, ( ) o guiones.</div>
                      </div>
                    </div>
                  </fieldset>
                  <fieldset class="mb-4">
                    <legend>Tu auto</legend>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label" for="vehiculo">Vehículo</label>
                        <input class="form-control<?= claseError($errores, 'vehiculo') ?>" id="vehiculo" name="vehiculo" required maxlength="80" placeholder="Marca / modelo / año" value="<?= valor($datos, 'vehiculo') ?>" />
                        <?= errorCampo($errores, 'vehiculo') ?>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="km">Kilometraje <span class="bx-opcional">(opcional)</span></label>
                        <input class="form-control<?= claseError($errores, 'km') ?>" id="km" name="km" inputmode="numeric" placeholder="Ej: 85.000 km" value="<?= valor($datos, 'km') ?>" />
                        <?= errorCampo($errores, 'km') ?>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label" for="urgencia">Urgencia</label>
                        <select class="form-select<?= claseError($errores, 'urgencia') ?>" id="urgencia" name="urgencia" aria-describedby="ayudaUrgencia">
                          <?php foreach ($urgenciasValidas as $u): ?>
                            <option value="<?= e($u) ?>"<?= seleccionado($datos, 'urgencia', $u) ?>><?= e($u) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <?= errorCampo($errores, 'urgencia') ?>
                        <div class="form-text" id="ayudaUrgencia">Elegí «Urgente» si el auto no arranca o no es seguro manejarlo.</div>
                      </div>
                      <div class="col-12">
                        <label class="form-label" for="problema">Descripción del problema</label>
                        <textarea class="form-control<?= claseError($errores, 'problema') ?>" id="problema" name="problema" rows="5" required maxlength="2000" data-contador="contadorProblema" aria-describedby="ayudaProblema contadorProblema" placeholder="Ej: vibra al frenar / check engine / no arranca en frío"><?= valor($datos, 'problema') ?></textarea>
                        <?= errorCampo($errores, 'problema') ?>
                        <div class="form-text d-flex justify-content-between gap-3">
                          <span id="ayudaProblema">Mínimo 10 caracteres. Cuanto más detalle (cuándo pasa, ruidos, testigos), mejor el estimado.</span>
                          <span id="contadorProblema" class="text-nowrap"></span>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                  <button class="btn btn-bx-primary btn-lg" type="submit"><i class="bi bi-send" aria-hidden="true"></i> Enviar solicitud</button>
                </form>
              </div>
            </div>
            <div class="col-lg-4">
              <aside class="bx-side-panel bx-sticky" aria-labelledby="despues-title">
                <h2 id="despues-title" class="h5">¿Qué pasa después?</h2>
                <ol class="bx-steps bx-steps--compact">
                  <li><strong>Revisamos tu consulta</strong><span>Un técnico lee lo que nos contaste.</span></li>
                  <li><strong>Te mandamos el estimado</strong><span>Por email, en el día hábil.</span></li>
                  <li><strong>Sacás turno si te sirve</strong><span>El presupuesto final se confirma por escrito tras el diagnóstico.</span></li>
                </ol>
                <hr />
                <p class="small text-secondary mb-2">¿Preferís hablar con alguien?</p>
                <a class="btn btn-bx-outline-dark w-100" href="https://wa.me/5493875550199" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Escribinos por WhatsApp</a>
              </aside>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
