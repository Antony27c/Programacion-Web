<?php
// Vista: alta / edición de turno en el panel (Create / Update)
// Variables: $encabezado, $bajada, $accion, $textoBoton, $csrf, $datos, $errores, $errorDb, $catalogos, $horarios, $admin, ($id opcional)
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Administración</p>
          <h1><?= e($encabezado) ?></h1>
          <p><?= e($bajada) ?></p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <?php if ($errorDb): ?>
            <div class="alert alert-danger" role="alert"><?= e($errorDb) ?></div>
          <?php endif; ?>
          <?php if ($errores): ?>
            <div class="alert alert-danger" role="alert">
              <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
            </div>
          <?php endif; ?>
          <form class="bx-form bx-card p-4" method="post" action="<?= e($accion) ?>" novalidate>
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
            <?php if (isset($id)): ?>
              <input type="hidden" name="id" value="<?= e($id) ?>" />
            <?php endif; ?>
            <p class="text-muted small">El cliente se identifica por su <strong>email</strong> y el vehículo por su <strong>patente</strong>: si ya existen se reutilizan y se actualizan sus datos.</p>
            <?php require __DIR__ . '/_campos.php'; ?>
            <div class="d-flex flex-wrap gap-2 mt-4 bx-form-actions">
              <button class="btn btn-bx-primary" type="submit"><?= e($textoBoton) ?></button>
              <a class="btn btn-bx-outline-dark" href="index.php">Cancelar</a>
            </div>
          </form>
        </div>
      </section>
    </main>
