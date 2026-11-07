<?php
// Vista parcial: campos del formulario de turno (solicitar.php y form.php)
// Variables: $datos, $errores, $catalogos, $horarios, $admin (bool) y $emailFijo (opcional)
?>
<fieldset class="mb-4">
  <legend class="h5 mb-3">Datos del cliente</legend>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label" for="nombre">Nombre</label>
      <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" maxlength="60" required autocomplete="given-name" placeholder="Ej: Antonio" value="<?= valor($datos, 'nombre') ?>" />
      <?= errorCampo($errores, 'nombre') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="apellido">Apellido</label>
      <input class="form-control<?= claseError($errores, 'apellido') ?>" id="apellido" name="apellido" maxlength="60" required autocomplete="family-name" placeholder="Ej: Chocobar" value="<?= valor($datos, 'apellido') ?>" />
      <?= errorCampo($errores, 'apellido') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="email">Email</label>
      <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" maxlength="150" required autocomplete="email" placeholder="nombre@correo.com" value="<?= valor($datos, 'email') ?>"<?= !empty($emailFijo) ? ' readonly' : '' ?> />
      <?= errorCampo($errores, 'email') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
      <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" maxlength="20" required autocomplete="tel" placeholder="+54 387 ..." value="<?= valor($datos, 'telefono') ?>" />
      <?= errorCampo($errores, 'telefono') ?>
    </div>
  </div>
</fieldset>

<fieldset class="mb-4">
  <legend class="h5 mb-3">Vehículo</legend>
  <div class="row g-3">
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="marca_id">Marca</label>
      <select class="form-select<?= claseError($errores, 'marca_id') ?>" id="marca_id" name="marca_id" required>
        <option value="">Elegí</option>
        <?php foreach ($catalogos['marcas'] as $idMarca => $marca): ?>
          <option value="<?= e($idMarca) ?>"<?= seleccionado($datos, 'marca_id', (string) $idMarca) ?>><?= e($marca) ?></option>
        <?php endforeach; ?>
      </select>
      <?= errorCampo($errores, 'marca_id') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="modelo">Modelo</label>
      <input class="form-control<?= claseError($errores, 'modelo') ?>" id="modelo" name="modelo" maxlength="60" required placeholder="Ej: Gol" value="<?= valor($datos, 'modelo') ?>" />
      <?= errorCampo($errores, 'modelo') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="anio">Año</label>
      <input class="form-control<?= claseError($errores, 'anio') ?>" id="anio" name="anio" type="number" min="1950" max="<?= e((int) date('Y') + 1) ?>" required placeholder="Ej: 2018" value="<?= valor($datos, 'anio') ?>" />
      <?= errorCampo($errores, 'anio') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="patente">Patente</label>
      <input class="form-control text-uppercase<?= claseError($errores, 'patente') ?>" id="patente" name="patente" maxlength="10" required placeholder="AB123CD" value="<?= valor($datos, 'patente') ?>" />
      <?= errorCampo($errores, 'patente') ?>
    </div>
  </div>
</fieldset>

<fieldset>
  <legend class="h5 mb-3">Turno</legend>
  <div class="row g-3">
    <div class="col-12">
      <span class="form-label d-block">Servicios <small class="text-muted fw-normal">(podés elegir más de uno)</small></span>
      <?php foreach ($catalogos['servicios'] as $idServicio => $servicio): ?>
        <div class="form-check form-check-inline">
          <input class="form-check-input<?= claseError($errores, 'servicios') ?>" type="checkbox" id="servicio_<?= e($idServicio) ?>" name="servicios[]" value="<?= e($idServicio) ?>"<?= marcado($datos, 'servicios', (string) $idServicio) ?> />
          <label class="form-check-label" for="servicio_<?= e($idServicio) ?>"><?= e($servicio) ?></label>
        </div>
      <?php endforeach; ?>
      <?php if (isset($errores['servicios'])): ?>
        <div class="invalid-feedback d-block"><?= e($errores['servicios']) ?></div>
      <?php endif; ?>
    </div>
    <div class="<?= $admin ? 'col-sm-6 col-lg-4' : 'col-md-6' ?>">
      <label class="form-label" for="fecha">Fecha<?= $admin ? '' : ' preferida' ?></label>
      <input class="form-control<?= claseError($errores, 'fecha') ?>" id="fecha" name="fecha" type="date" required value="<?= valor($datos, 'fecha') ?>" />
      <?= errorCampo($errores, 'fecha') ?>
    </div>
    <div class="<?= $admin ? 'col-sm-6 col-lg-4' : 'col-md-6' ?>">
      <label class="form-label" for="horario">Horario</label>
      <select class="form-select<?= claseError($errores, 'horario') ?>" id="horario" name="horario" required>
        <option value="">Elegí</option>
        <?php foreach ($horarios as $h): ?>
          <option value="<?= e($h) ?>"<?= seleccionado($datos, 'horario', $h) ?>><?= e($h) ?></option>
        <?php endforeach; ?>
      </select>
      <?= errorCampo($errores, 'horario') ?>
    </div>
    <?php if ($admin): ?>
      <div class="col-sm-6 col-lg-4">
        <label class="form-label" for="estado_id">Estado</label>
        <select class="form-select<?= claseError($errores, 'estado_id') ?>" id="estado_id" name="estado_id" required>
          <?php foreach ($catalogos['estados'] as $idEstado => $estado): ?>
            <option value="<?= e($idEstado) ?>"<?= seleccionado($datos, 'estado_id', (string) $idEstado) ?>><?= e($estado) ?></option>
          <?php endforeach; ?>
        </select>
        <?= errorCampo($errores, 'estado_id') ?>
      </div>
    <?php endif; ?>
    <div class="col-12">
      <label class="form-label" for="sintoma"><?= $admin ? 'Síntoma / trabajo a realizar' : '¿Qué le pasa al auto?' ?></label>
      <textarea class="form-control<?= claseError($errores, 'sintoma') ?>" id="sintoma" name="sintoma" rows="4" required placeholder="Describí el ruido, luz en tablero o síntoma..."><?= valor($datos, 'sintoma') ?></textarea>
      <?= errorCampo($errores, 'sintoma') ?>
    </div>
  </div>
</fieldset>
