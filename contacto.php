<?php
// ============================================================
// contacto.php — Formulario POST: mensaje de contacto
// Incluye validación de formato de email con filter_var (TP5)
// ============================================================
require_once __DIR__ . '/includes/funciones.php';

$datos   = array_fill_keys(['nombre', 'email', 'mensaje'], '');
$errores = [];
$exito   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $_) {
        $datos[$campo] = limpiar($_POST[$campo] ?? '');
    }

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Ingresá tu nombre.';
    } elseif (mb_strlen($datos['nombre']) < 3) {
        $errores['nombre'] = 'El nombre es demasiado corto.';
    }

    // Validación de formato específico: email
    if ($datos['email'] === '') {
        $errores['email'] = 'Ingresá tu email.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El formato del email no es válido.';
    }

    if ($datos['mensaje'] === '') {
        $errores['mensaje'] = 'Escribí un mensaje.';
    } elseif (mb_strlen($datos['mensaje']) < 10) {
        $errores['mensaje'] = 'El mensaje es muy corto (mínimo 10 caracteres).';
    }

    $exito = ($errores === []);
}

$titulo      = 'BOXLY | Contacto';
$descripcion = 'Contacto y ubicación del taller BOXLY en Salta.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Contacto</p>
          <h1>Dónde estamos</h1>
          <p>Av. Bolivia 2450, Salta Capital. Escribinos o pasá al taller.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row g-5">
            <div class="col-lg-7">
              <div class="bx-placeholder bx-placeholder--tall" role="img" aria-label="Mapa de ubicación en Salta Capital">MAPA · SALTA CAPITAL<br />Av. Bolivia 2450</div>
            </div>
            <div class="col-lg-5">
              <h2 class="h3">Datos de contacto</h2>
              <dl class="row mb-4">
                <dt class="col-sm-4" style="color:#0052ff">Dirección</dt>
                <dd class="col-sm-8">Av. Bolivia 2450, Salta Capital</dd>
                <dt class="col-sm-4" style="color:#0052ff">Teléfono</dt>
                <dd class="col-sm-8"><a href="tel:+543875550199">+54 387 555-0199</a></dd>
                <dt class="col-sm-4" style="color:#0052ff">WhatsApp</dt>
                <dd class="col-sm-8"><a href="https://wa.me/5493875550199" rel="noopener noreferrer">+54 9 387 555-0199</a></dd>
                <dt class="col-sm-4" style="color:#0052ff">Email</dt>
                <dd class="col-sm-8"><a href="mailto:turnos@boxly.com.ar">turnos@boxly.com.ar</a></dd>
                <dt class="col-sm-4" style="color:#0052ff">Horario</dt>
                <dd class="col-sm-8">Lun–Vie 8–18 · Sáb 8–13</dd>
              </dl>
              <h2 class="h4">Escribinos</h2>

              <?php if ($exito): ?>
                <div class="alert alert-success" role="status">
                  <strong>Mensaje enviado.</strong> Gracias <?= e($datos['nombre']) ?>, te responderemos a <strong><?= e($datos['email']) ?></strong> a la brevedad.
                </div>
              <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>No pudimos enviar el mensaje:</strong> corregí los campos marcados.
                </div>
              <?php endif; ?>

              <form class="bx-form" method="post" action="contacto.php" novalidate>
                <div class="mb-3">
                  <label class="form-label" for="nombre">Nombre</label>
                  <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" required autocomplete="name" value="<?= valor($datos, 'nombre') ?>" />
                  <?= errorCampo($errores, 'nombre') ?>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="email">Email</label>
                  <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" required autocomplete="email" placeholder="tu@correo.com" value="<?= valor($datos, 'email') ?>" />
                  <?= errorCampo($errores, 'email') ?>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="mensaje">Mensaje</label>
                  <textarea class="form-control<?= claseError($errores, 'mensaje') ?>" id="mensaje" name="mensaje" rows="4" required placeholder="¿En qué podemos ayudarte?"><?= valor($datos, 'mensaje') ?></textarea>
                  <?= errorCampo($errores, 'mensaje') ?>
                </div>
                <button class="btn btn-bx-primary" type="submit">Enviar mensaje</button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
