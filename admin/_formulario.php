<?php
// _formulario.php — Formulario de turno compartido por crear.php y editar.php
// Variables esperadas: $datos, $errores, $accion, $textoBoton, $csrf, ($id opcional)
?>
<form class="bx-form bx-card p-4" method="post" action="<?= e($accion) ?>" novalidate>
  <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
  <?php if (isset($id)): ?>
    <input type="hidden" name="id" value="<?= e($id) ?>" />
  <?php endif; ?>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label" for="nombre">Nombre y apellido</label>
      <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" maxlength="120" required value="<?= valor($datos, 'nombre') ?>" />
      <?= errorCampo($errores, 'nombre') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
      <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" maxlength="20" required value="<?= valor($datos, 'telefono') ?>" />
      <?= errorCampo($errores, 'telefono') ?>
    </div>
    <div class="col-12">
      <label class="form-label" for="vehiculo">Vehículo (marca, modelo, año)</label>
      <input class="form-control<?= claseError($errores, 'vehiculo') ?>" id="vehiculo" name="vehiculo" maxlength="150" required value="<?= valor($datos, 'vehiculo') ?>" />
      <?= errorCampo($errores, 'vehiculo') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="servicio">Servicio</label>
      <select class="form-select<?= claseError($errores, 'servicio') ?>" id="servicio" name="servicio" required>
        <option value="">Elegí una opción</option>
        <?php foreach (SERVICIOS_TURNO as $s): ?>
          <option value="<?= e($s) ?>"<?= seleccionado($datos, 'servicio', $s) ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
      <?= errorCampo($errores, 'servicio') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="fecha">Fecha</label>
      <input class="form-control<?= claseError($errores, 'fecha') ?>" id="fecha" name="fecha" type="date" required value="<?= valor($datos, 'fecha') ?>" />
      <?= errorCampo($errores, 'fecha') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="horario">Horario</label>
      <select class="form-select<?= claseError($errores, 'horario') ?>" id="horario" name="horario" required>
        <option value="">Elegí</option>
        <?php foreach (HORARIOS_TURNO as $h): ?>
          <option value="<?= e($h) ?>"<?= seleccionado($datos, 'horario', $h) ?>><?= e($h) ?></option>
        <?php endforeach; ?>
      </select>
      <?= errorCampo($errores, 'horario') ?>
    </div>
    <div class="col-sm-6 col-lg-3">
      <label class="form-label" for="estado">Estado</label>
      <select class="form-select<?= claseError($errores, 'estado') ?>" id="estado" name="estado" required>
        <?php foreach (ESTADOS_TURNO as $est): ?>
          <option value="<?= e($est) ?>"<?= seleccionado($datos, 'estado', $est) ?>><?= e($est) ?></option>
        <?php endforeach; ?>
      </select>
      <?= errorCampo($errores, 'estado') ?>
    </div>
    <div class="col-12">
      <label class="form-label" for="sintoma">Síntoma / trabajo a realizar</label>
      <textarea class="form-control<?= claseError($errores, 'sintoma') ?>" id="sintoma" name="sintoma" rows="4" required><?= valor($datos, 'sintoma') ?></textarea>
      <?= errorCampo($errores, 'sintoma') ?>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2 mt-4 bx-form-actions">
    <button class="btn btn-bx-primary" type="submit"><?= e($textoBoton) ?></button>
    <a class="btn btn-bx-outline-dark" href="index.php">Cancelar</a>
  </div>
</form>
