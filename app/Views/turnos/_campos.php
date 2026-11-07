<?php
// Vista parcial: campos del formulario de turno (solicitar.php y form.php)
// Variables: $datos, $errores, $catalogos, $horarios, $admin (bool) y $emailFijo (opcional)
// Presentación (opcionales): $serviciosDetalle [id => descripción/precio] y $fechaMinima (Y-m-d)
use App\Models\Servicio;

$serviciosDetalle = $serviciosDetalle ?? [];
$errorServicios   = isset($errores['servicios']);
?>
<fieldset class="bx-form-step mb-4">
  <legend><span class="bx-step-num" aria-hidden="true">1</span> <?= $admin ? 'Datos del cliente' : 'Tus datos' ?></legend>
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
      <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" maxlength="150" required autocomplete="email" placeholder="nombre@correo.com" value="<?= valor($datos, 'email') ?>"<?= !empty($emailFijo) ? ' readonly aria-describedby="ayudaEmailCuenta"' : '' ?> />
      <?= errorCampo($errores, 'email') ?>
      <?php if (!empty($emailFijo)): ?>
        <div class="form-text" id="ayudaEmailCuenta"><i class="bi bi-lock" aria-hidden="true"></i> Es el email de tu cuenta: el turno queda asociado a ella.</div>
      <?php endif; ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
      <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" maxlength="20" required autocomplete="tel" placeholder="+54 387 ..." aria-describedby="ayudaTelefono" value="<?= valor($datos, 'telefono') ?>" />
      <?= errorCampo($errores, 'telefono') ?>
      <div class="form-text" id="ayudaTelefono">Por acá te confirmamos el turno.</div>
    </div>
  </div>
</fieldset>

<fieldset class="bx-form-step mb-4">
  <legend><span class="bx-step-num" aria-hidden="true">2</span> Vehículo</legend>
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
      <input class="form-control text-uppercase<?= claseError($errores, 'patente') ?>" id="patente" name="patente" maxlength="10" required placeholder="AB123CD" aria-describedby="ayudaPatente" value="<?= valor($datos, 'patente') ?>" />
      <?= errorCampo($errores, 'patente') ?>
      <div class="form-text" id="ayudaPatente">ABC123 o AB123CD.</div>
    </div>
  </div>
</fieldset>

<fieldset class="bx-form-step">
  <legend><span class="bx-step-num" aria-hidden="true">3</span> Turno</legend>
  <div class="row g-3">
    <div class="col-12">
      <p class="form-label mb-1" id="servicios-label">Servicios <span class="bx-req" aria-hidden="true">*</span></p>
      <p class="form-text mt-0 mb-2" id="ayudaServicios">Podés elegir más de uno.</p>
      <div class="bx-choices<?= $errorServicios ? ' is-invalid' : '' ?>" role="group" aria-labelledby="servicios-label" aria-describedby="ayudaServicios<?= $errorServicios ? ' errorServicios' : '' ?>">
        <?php foreach ($catalogos['servicios'] as $idServicio => $servicio): ?>
          <?php $detalle = $serviciosDetalle[$idServicio] ?? null; ?>
          <div class="bx-choice">
            <input class="bx-choice-input" type="checkbox" id="servicio_<?= e($idServicio) ?>" name="servicios[]" value="<?= e($idServicio) ?>"<?= marcado($datos, 'servicios', (string) $idServicio) ?><?= $errorServicios ? ' aria-invalid="true"' : '' ?> />
            <label class="bx-choice-card" for="servicio_<?= e($idServicio) ?>">
              <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--<?= e(Servicio::icono($servicio)) ?>"></span></span>
              <span class="bx-choice-text">
                <strong><?= e($servicio) ?></strong>
                <?php if ($detalle): ?>
                  <small><?= e($detalle['descripcion']) ?></small>
                  <span class="bx-choice-precio"><?= $detalle['precio_base'] > 0 ? 'Desde ' : '' ?><?= e(Servicio::precio($detalle['precio_base'])) ?></span>
                <?php endif; ?>
              </span>
              <span class="bx-choice-check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
            </label>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if ($errorServicios): ?>
        <div class="invalid-feedback d-block" id="errorServicios"><?= e($errores['servicios']) ?></div>
      <?php endif; ?>
    </div>
    <div class="<?= $admin ? 'col-sm-6 col-lg-4' : 'col-md-6' ?>">
      <label class="form-label" for="fecha">Fecha<?= $admin ? '' : ' preferida' ?></label>
      <input class="form-control<?= claseError($errores, 'fecha') ?>" id="fecha" name="fecha" type="date" required<?= $admin ? '' : ' min="' . e($fechaMinima ?? date('Y-m-d')) . '" aria-describedby="ayudaFecha"' ?> value="<?= valor($datos, 'fecha') ?>" />
      <?= errorCampo($errores, 'fecha') ?>
      <?php if (!$admin): ?>
        <div class="form-text" id="ayudaFecha">Desde hoy en adelante. Lun a Vie 8:00–18:00 · Sáb 8:00–13:00.</div>
      <?php endif; ?>
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
      <textarea class="form-control<?= claseError($errores, 'sintoma') ?>" id="sintoma" name="sintoma" rows="4" required placeholder="Describí el ruido, luz en tablero o síntoma..."<?= $admin ? '' : ' aria-describedby="ayudaSintoma"' ?>><?= valor($datos, 'sintoma') ?></textarea>
      <?= errorCampo($errores, 'sintoma') ?>
      <?php if (!$admin): ?>
        <div class="form-text" id="ayudaSintoma">Contanos cuándo pasa, si hace ruido o si se encendió algún testigo.</div>
      <?php endif; ?>
    </div>
  </div>
</fieldset>
