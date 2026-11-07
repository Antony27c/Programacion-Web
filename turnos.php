<?php
// ============================================================
// turnos.php — Formulario POST: solicitud de turno
// Sanitización + validación en servidor (TP5) + persistencia en MySQL (TP7)
// sobre el esquema normalizado del TP6
// TP9: panel "Consultá tu turno" (número + patente) que pide el estado
//      con fetch a api/consultar_turno.php (js/turnos.js). Sin JS, el
//      mismo formulario GET se resuelve acá en el servidor.
// ============================================================
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/turnos_db.php';
require_once __DIR__ . '/config/conexion.php';

$catalogos = catalogos($pdo);
$datos     = turnoVacio($catalogos);
$errores   = [];
$turnoId   = null;
$admin     = false;

// TP8: con sesión iniciada se precargan los datos y el turno queda a nombre de la cuenta
$cuenta    = ($u = usuarioActual()) ? obtenerUsuario($pdo, $u['id']) : null;
$emailFijo = $cuenta !== null;
if ($cuenta) {
    $datos = array_merge($datos, array_intersect_key($cuenta, array_flip(['nombre', 'apellido', 'email', 'telefono'])));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Sanitizar y validar en servidor (listas blancas contra los catálogos)
    $entrada = $cuenta ? ['email' => $cuenta['email']] + $_POST : $_POST;
    [$datos, $errores] = validarTurno($entrada, $catalogos);

    // 2) Guardar con consultas preparadas (anti SQL Injection)
    if ($errores === []) {
        $turnoId = guardarTurno($pdo, $datos, false);
    }
}
$exito = $turnoId !== null;

// TP9: consulta de estado sin JS (mismas validaciones que la API)
$consultaId      = limpiar(filter_input(INPUT_GET, 'consulta_id', FILTER_UNSAFE_RAW));
$consultaPatente = limpiar(filter_input(INPUT_GET, 'patente', FILTER_UNSAFE_RAW));
$consulta        = null;
$consultaError   = null;
if ($consultaId !== '' || $consultaPatente !== '') {
    $idValido      = ctype_digit($consultaId) && (int) $consultaId > 0 ? (int) $consultaId : null;
    $patenteValida = normalizarPatente($consultaPatente);
    if ($idValido === null) {
        $consultaError = 'Ingresá un número de turno válido (entero positivo).';
    } elseif ($patenteValida === null) {
        $consultaError = 'Ingresá una patente válida (ABC123 o AB123CD).';
    } else {
        $consulta = consultarEstadoTurno($pdo, $idValido, $patenteValida);
        if ($consulta === null) {
            $consultaError = "No encontramos el turno N° $idValido para la patente $patenteValida.";
        }
    }
}

$titulo      = 'BOXLY | Agendar turno';
$descripcion = 'Agendá tu turno en el taller BOXLY de Salta.';
$scripts     = ['js/turnos.js'];
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Turnos</p>
          <h1>Agendá tu visita</h1>
          <p>Elegí día, horario y servicio. Te confirmamos por WhatsApp.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row g-4">
            <div class="col-lg-8">
              <h2 class="h3 mb-3">Datos del turno</h2>

              <?php if ($exito): ?>
                <div class="alert alert-success" role="status">
                  <strong>Solicitud N° <?= e($turnoId) ?> registrada correctamente.</strong> Te contactaremos para confirmar el turno.
                  Con este número y tu patente podés consultar el estado en el panel «Consultá tu turno».
                </div>
                <div class="bx-card p-4 mb-4">
                  <h3 class="h5">Resumen de tu solicitud</h3>
                  <dl class="row mb-0">
                    <dt class="col-sm-4">Cliente</dt>
                    <dd class="col-sm-8"><?= e($datos['nombre'] . ' ' . $datos['apellido']) ?></dd>
                    <dt class="col-sm-4">Contacto</dt>
                    <dd class="col-sm-8"><?= e($datos['email']) ?> · <?= e($datos['telefono']) ?></dd>
                    <dt class="col-sm-4">Vehículo</dt>
                    <dd class="col-sm-8"><?= e($catalogos['marcas'][(int) $datos['marca_id']] . ' ' . $datos['modelo'] . ' ' . $datos['anio']) ?> (<?= e($datos['patente']) ?>)</dd>
                    <dt class="col-sm-4">Servicios</dt>
                    <dd class="col-sm-8"><?= e(implode(', ', array_map(fn ($s) => $catalogos['servicios'][(int) $s], $datos['servicios']))) ?></dd>
                    <dt class="col-sm-4">Fecha preferida</dt>
                    <dd class="col-sm-8"><?= e($datos['fecha']) ?> (<?= e($datos['horario']) ?>)</dd>
                    <dt class="col-sm-4">Síntoma</dt>
                    <dd class="col-sm-8"><?= e($datos['sintoma']) ?></dd>
                  </dl>
                </div>
              <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Revisá el formulario:</strong> encontramos <?= count($errores) ?> error(es) de validación.
                </div>
              <?php endif; ?>

              <form class="bx-form" method="post" action="turnos.php" novalidate>
                <?php require __DIR__ . '/includes/campos_turno.php'; ?>
                <button class="btn btn-bx-primary mt-4" type="submit">Confirmar turno</button>
              </form>
            </div>
            <div class="col-lg-4">
              <aside class="bx-side-panel">
                <h2 class="h4">Horarios</h2>
                <ul class="mb-0 ps-3">
                  <li>Lun a Vie: 8:00 – 18:00</li>
                  <li>Sábados: 8:00 – 13:00</li>
                  <li>Domingos: cerrado</li>
                  <li>Confirmación en menos de 2 hs hábiles</li>
                </ul>
              </aside>

              <aside class="bx-side-panel mt-4" aria-labelledby="titulo-consulta">
                <h2 class="h4" id="titulo-consulta">Consultá tu turno</h2>
                <p class="small text-secondary">Ingresá el número de solicitud y la patente del vehículo.</p>
                <form id="form-consulta-turno" method="get" action="turnos.php#titulo-consulta"
                      data-api="api/consultar_turno.php" novalidate>
                  <div class="mb-2">
                    <label class="form-label" for="consulta_id">N° de turno</label>
                    <input class="form-control" id="consulta_id" name="consulta_id" type="text" inputmode="numeric"
                           pattern="[0-9]+" maxlength="10" autocomplete="off" required value="<?= e($consultaId) ?>" />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="consulta_patente">Patente</label>
                    <input class="form-control text-uppercase" id="consulta_patente" name="patente" type="text"
                           maxlength="10" autocomplete="off" placeholder="ABC123 o AB123CD" required value="<?= e($consultaPatente) ?>" />
                  </div>
                  <button class="btn btn-bx-primary w-100" type="submit">Consultar estado</button>
                </form>
                <div class="mt-3" id="resultado-consulta" aria-live="polite">
                  <?php if ($consultaError !== null): ?>
                    <div class="alert alert-danger mb-0" role="alert"><?= e($consultaError) ?></div>
                  <?php elseif ($consulta !== null): ?>
                    <div class="alert alert-info mb-0">
                      <p class="fw-semibold mb-2">Turno N° <?= e($consulta['id']) ?>: <span class="badge text-bg-dark"><?= e($consulta['estado']) ?></span></p>
                      <dl class="row small mb-0">
                        <dt class="col-5">Vehículo</dt>
                        <dd class="col-7"><?= e($consulta['vehiculo']) ?> (<?= e($consulta['patente']) ?>)</dd>
                        <dt class="col-5">Fecha</dt>
                        <dd class="col-7"><?= e(date('d/m/Y', strtotime($consulta['fecha']))) ?> (<?= e($consulta['horario']) ?>)</dd>
                        <dt class="col-5">Servicios</dt>
                        <dd class="col-7 mb-0"><?= e(implode(', ', $consulta['servicios']) ?: '—') ?></dd>
                      </dl>
                    </div>
                  <?php endif; ?>
                </div>
              </aside>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
