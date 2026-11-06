<?php
// _formulario.php — Formulario de turno compartido por crear.php y editar.php
// Variables esperadas: $datos, $errores, $catalogos, $accion, $textoBoton, $csrf, ($id opcional)
$admin = true;
?>
<form class="bx-form bx-card p-4" method="post" action="<?= e($accion) ?>" novalidate>
  <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
  <?php if (isset($id)): ?>
    <input type="hidden" name="id" value="<?= e($id) ?>" />
  <?php endif; ?>
  <p class="text-muted small">El cliente se identifica por su <strong>email</strong> y el vehículo por su <strong>patente</strong>: si ya existen se reutilizan y se actualizan sus datos.</p>
  <?php require __DIR__ . '/../includes/campos_turno.php'; ?>
  <div class="d-flex flex-wrap gap-2 mt-4 bx-form-actions">
    <button class="btn btn-bx-primary" type="submit"><?= e($textoBoton) ?></button>
    <a class="btn btn-bx-outline-dark" href="index.php">Cancelar</a>
  </div>
</form>
