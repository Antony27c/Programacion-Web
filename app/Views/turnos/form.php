<?php
// Vista: alta / edición de turno en el panel (Create / Update)
// Variables: $encabezado, $bajada, $accion, $textoBoton, $csrf, $datos, $errores, $errorDb, $catalogos, $horarios, $admin, ($id opcional)
// Rediseño: los fieldsets de _campos.php se muestran como secciones en tarjetas
// y el resumen de errores enlaza a cada campo con problemas.
$etiquetas = [
    'nombre'    => 'Nombre',
    'apellido'  => 'Apellido',
    'email'     => 'Email',
    'telefono'  => 'Teléfono / WhatsApp',
    'marca_id'  => 'Marca',
    'modelo'    => 'Modelo',
    'anio'      => 'Año',
    'patente'   => 'Patente',
    'servicios' => 'Servicios',
    'fecha'     => 'Fecha',
    'horario'   => 'Horario',
    'estado_id' => 'Estado',
    'sintoma'   => 'Síntoma / trabajo a realizar',
];
// Ancla de cada campo (los servicios son checkboxes: se enlaza al primero)
$ancla = fn (string $campo): string => $campo === 'servicios'
    ? 'servicio_' . array_key_first($catalogos['servicios'])
    : $campo;
?>
    <main id="contenido" class="bx-admin">
      <section class="bx-page-hero bx-admin-hero">
        <div class="container">
          <a class="bx-volver" href="index.php"><i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al panel</a>
          <p class="eyebrow">Administración</p>
          <h1><?= e($encabezado) ?></h1>
          <p><?= e($bajada) ?></p>
        </div>
      </section>
      <section class="bx-section bx-section-muted bx-admin-body">
        <div class="container bx-admin-form-wrap">
          <?php if ($errorDb): ?>
            <div class="alert alert-danger" role="alert"><?= e($errorDb) ?></div>
          <?php endif; ?>
          <?php if ($errores): ?>
            <div class="alert alert-danger bx-error-resumen" role="alert" tabindex="-1">
              <p class="mb-2"><strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.</p>
              <ul class="mb-0">
                <?php foreach ($errores as $campo => $mensaje): ?>
                  <li><a href="#<?= e($ancla($campo)) ?>"><?= e($etiquetas[$campo] ?? $campo) ?></a>: <?= e($mensaje) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          <form class="bx-form bx-admin-form" method="post" action="<?= e($accion) ?>" novalidate>
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
            <?php if (isset($id)): ?>
              <input type="hidden" name="id" value="<?= e($id) ?>" />
            <?php endif; ?>
            <div class="bx-admin-form-intro">
              <p class="mb-1"><i class="bi bi-info-circle" aria-hidden="true"></i> El cliente se identifica por su <strong>email</strong> y el vehículo por su <strong>patente</strong>: si ya existen se reutilizan y se actualizan sus datos.</p>
              <p class="bx-required-note mb-0">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>
            </div>
            <?php require __DIR__ . '/_campos.php'; ?>
            <div class="bx-form-actions bx-admin-form-actions">
              <a class="btn btn-bx-outline-dark" href="index.php">Cancelar</a>
              <button class="btn btn-bx-primary" type="submit"><i class="bi bi-check2" aria-hidden="true"></i> <?= e($textoBoton) ?></button>
            </div>
          </form>
        </div>
      </section>
    </main>
