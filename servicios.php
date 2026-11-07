<?php
// ============================================================
// servicios.php — Formulario GET: buscador/filtro de servicios
// TP5: el catálogo se filtra en servidor por el parámetro ?q=
// TP9: el catálogo sale de la tabla servicios y, con JS, la búsqueda
//      es en vivo vía fetch a api/buscar_servicios.php (js/servicios.js).
//      Sin JS el formulario GET sigue funcionando igual (mejora progresiva).
// TP10: las consultas viven en el modelo App\Models\Servicio.
// ============================================================
use App\Models\Servicio;

require __DIR__ . '/app/bootstrap.php';

// Entrada GET sanitizada (filter_input + trim) y validada con la misma regla que la API
$busqueda   = limpiar(filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW));
$error      = isset($_GET['q']) && !is_string($_GET['q']) ? 'El parámetro q debe ser un texto.' : Servicio::errorBusqueda($busqueda);
$resultados = $error === null ? (new Servicio())->buscar($busqueda) : [];
if (!mb_check_encoding($busqueda, 'UTF-8')) {
    $busqueda = '';
}

$titulo      = 'BOXLY | Servicios';
$descripcion = 'Servicios del taller BOXLY en Salta: mecánica, frenos, diagnóstico y service.';
$scripts     = ['js/servicios.js'];
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Servicios</p>
          <h1>Catálogo de servicios</h1>
          <p>Elegí el servicio y pedí turno o presupuesto online.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">

          <form class="bx-form mb-4" id="form-busqueda" method="get" action="servicios.php" role="search"
                data-api="api/buscar_servicios.php" data-turnos="turnos.php">
            <div class="row g-2 align-items-end">
              <div class="col-md-9">
                <label class="form-label" for="q">Buscar servicio</label>
                <input class="form-control" id="q" name="q" type="search" maxlength="<?= Servicio::BUSQUEDA_MAX ?>" autocomplete="off" placeholder="Ej: frenos, aceite, escáner..." value="<?= e($busqueda) ?>" aria-controls="resultados-servicios" />
              </div>
              <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-bx-primary w-100" type="submit">Buscar</button>
                <a class="btn btn-bx-outline-dark" id="limpiar-busqueda" href="servicios.php"<?= $busqueda === '' ? ' hidden' : '' ?>>Limpiar</a>
              </div>
            </div>
          </form>

          <div id="estado-busqueda" aria-live="polite">
            <?php if ($error !== null): ?>
              <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
            <?php elseif ($busqueda !== ''): ?>
              <?php if ($resultados !== []): ?>
                <div class="alert alert-success" role="status">
                  <?= count($resultados) ?> resultado(s) para «<?= e($busqueda) ?>».
                </div>
              <?php else: ?>
                <div class="alert alert-danger" role="alert">
                  No encontramos servicios para «<?= e($busqueda) ?>». Probá con otra palabra.
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <div class="d-flex flex-column gap-3" id="resultados-servicios">
            <?php foreach ($resultados as $s): ?>
              <article class="bx-service-row d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                  <h2 class="h4 mb-1"><?= e($s['nombre']) ?></h2>
                  <p class="mb-1 text-secondary"><?= e($s['descripcion']) ?></p>
                  <p class="mb-0 fw-semibold"><?= e(Servicio::precio($s['precio_base'])) ?></p>
                </div>
                <a class="btn btn-bx-primary flex-shrink-0" href="turnos.php">Pedir turno</a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
