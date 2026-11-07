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

    // Longitud máxima y codificación válida (htmlspecialchars devuelve '' ante UTF-8 inválido)
    foreach (['nombre' => 60, 'email' => 150, 'mensaje' => 2000] as $campo => $max) {
        if (!mb_check_encoding($datos[$campo], 'UTF-8')) {
            $errores[$campo] = 'Contiene caracteres no válidos.';
        } elseif (!isset($errores[$campo]) && mb_strlen($datos[$campo]) > $max) {
            $errores[$campo] = "No puede superar los $max caracteres.";
        }
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
          <h1>Hablemos de tu auto</h1>
          <p>Escribinos, llamanos o pasá por el taller en Av. Bolivia 2450, Salta Capital. Respondemos en el día hábil.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row g-5">
            <div class="col-lg-5 order-lg-2">
              <div class="bx-side-panel mb-4">
                <h2 class="h4 mb-1">Datos de contacto</h2>
                <dl class="mb-0">
                  <div class="bx-info-item">
                    <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--ubicacion"></span></span>
                    <div><dt>Dirección</dt><dd>Av. Bolivia 2450, Salta Capital</dd></div>
                  </div>
                  <div class="bx-info-item">
                    <span class="bx-card-icon" aria-hidden="true"><i class="bi bi-telephone"></i></span>
                    <div><dt>Teléfono</dt><dd><a href="tel:+543875550199">+54 387 555-0199</a></dd></div>
                  </div>
                  <div class="bx-info-item">
                    <span class="bx-card-icon" aria-hidden="true"><i class="bi bi-whatsapp"></i></span>
                    <div><dt>WhatsApp</dt><dd><a href="https://wa.me/5493875550199" target="_blank" rel="noopener noreferrer">+54 9 387 555-0199</a></dd></div>
                  </div>
                  <div class="bx-info-item">
                    <span class="bx-card-icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                    <div><dt>Email</dt><dd><a href="mailto:turnos@boxly.com.ar">turnos@boxly.com.ar</a></dd></div>
                  </div>
                  <div class="bx-info-item">
                    <span class="bx-card-icon" aria-hidden="true"><i class="bi bi-clock"></i></span>
                    <div><dt>Horario</dt><dd>Lun a Vie 8:00–18:00 · Sáb 8:00–13:00</dd></div>
                  </div>
                </dl>
              </div>
              <figure class="mb-0">
                <img class="bx-img" src="img/mapa-salta.svg" width="800" height="500" loading="lazy" decoding="async" alt="Mapa ilustrado de la ubicación del taller BOXLY en Av. Bolivia 2450, Salta Capital" />
                <figcaption class="mt-3">
                  <a class="btn btn-bx-outline-dark w-100" href="https://www.google.com/maps/search/?api=1&amp;query=Av.+Bolivia+2450,+Salta,+Argentina" target="_blank" rel="noopener noreferrer"><i class="bi bi-signpost-split" aria-hidden="true"></i> Cómo llegar (abre Google Maps)</a>
                </figcaption>
              </figure>
            </div>

            <div class="col-lg-7 order-lg-1">
              <div class="bx-form-card">
                <h2 class="h3 mb-1">Escribinos</h2>
                <p class="text-secondary mb-4">Contanos en qué podemos ayudarte y te respondemos por email.</p>

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
                  <p class="bx-required-note">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>
                  <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" required maxlength="60" autocomplete="name" placeholder="Ej: Laura Martínez" value="<?= valor($datos, 'nombre') ?>" />
                    <?= errorCampo($errores, 'nombre') ?>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" required maxlength="150" autocomplete="email" placeholder="tu@correo.com" aria-describedby="ayudaEmail" value="<?= valor($datos, 'email') ?>" />
                    <?= errorCampo($errores, 'email') ?>
                    <div class="form-text" id="ayudaEmail">Lo usamos solo para responderte.</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="mensaje">Mensaje</label>
                    <textarea class="form-control<?= claseError($errores, 'mensaje') ?>" id="mensaje" name="mensaje" rows="5" required maxlength="2000" data-contador="contadorMensaje" aria-describedby="ayudaMensaje contadorMensaje" placeholder="¿En qué podemos ayudarte?"><?= valor($datos, 'mensaje') ?></textarea>
                    <?= errorCampo($errores, 'mensaje') ?>
                    <div class="form-text d-flex justify-content-between gap-3">
                      <span id="ayudaMensaje">Mínimo 10 caracteres. Si es por una falla, contanos marca, modelo y qué notás.</span>
                      <span id="contadorMensaje" class="text-nowrap"></span>
                    </div>
                  </div>
                  <button class="btn btn-bx-primary btn-lg mt-2" type="submit"><i class="bi bi-send" aria-hidden="true"></i> Enviar mensaje</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
