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
          <p>Elegí el servicio y sacá turno o pedí presupuesto online. Los precios son orientativos: el final lo confirmamos por escrito después del diagnóstico.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">

          <form class="bx-form bx-search mb-4" id="form-busqueda" method="get" action="servicios.php" role="search"
                data-api="api/buscar_servicios.php" data-turnos="turnos.php" data-detalle="servicio-detalle.php">
            <label class="form-label" for="q">Buscar servicio</label>
            <div class="bx-search-row">
              <div class="bx-search-field">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input class="form-control" id="q" name="q" type="search" maxlength="<?= Servicio::BUSQUEDA_MAX ?>" autocomplete="off" placeholder="Ej: frenos, aceite, escáner..." value="<?= e($busqueda) ?>" aria-controls="resultados-servicios" aria-describedby="ayudaBusqueda" />
              </div>
              <button class="btn btn-bx-primary" type="submit">Buscar</button>
              <a class="btn btn-bx-outline-dark" id="limpiar-busqueda" href="servicios.php"<?= $busqueda === '' ? ' hidden' : '' ?>>Limpiar</a>
            </div>
            <div class="form-text" id="ayudaBusqueda">Los resultados se actualizan mientras escribís.</div>
          </form>

          <div id="estado-busqueda" aria-live="polite">
            <?php if ($error !== null): ?>
              <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
            <?php elseif ($busqueda !== ''): ?>
              <?php if ($resultados !== []): ?>
                <p class="bx-search-count" role="status"><?= count($resultados) ?> resultado(s) para «<?= e($busqueda) ?>».</p>
              <?php else: ?>
                <div class="bx-empty" role="status">
                  <img src="img/ilustraciones/estado-vacio.svg" width="480" height="320" alt="" />
                  <h2 class="h4">No encontramos servicios para «<?= e($busqueda) ?>»</h2>
                  <p>Probá con otra palabra o contanos el problema y te pasamos un presupuesto.</p>
                  <a class="btn btn-bx-primary" href="presupuesto.php"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Pedir presupuesto</a>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <div class="row g-4" id="resultados-servicios">
            <?php foreach ($resultados as $s): ?>
              <div class="col-md-6 col-lg-4">
                <article class="bx-card bx-servicio">
                  <div class="bx-servicio-top">
                    <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--<?= e(Servicio::icono($s['nombre'])) ?>"></span></span>
                    <p class="bx-servicio-precio"><?php if ($s['precio_base'] > 0): ?><small>Desde</small> <?php endif; ?><?= e(Servicio::precio($s['precio_base'])) ?></p>
                  </div>
                  <h2 class="h4"><?= e($s['nombre']) ?></h2>
                  <p><?= e($s['descripcion']) ?></p>
                  <div class="bx-servicio-acciones">
                    <a class="btn btn-bx-primary btn-sm" href="turnos.php?servicio=<?= e($s['id']) ?>"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno<span class="visually-hidden"> para <?= e($s['nombre']) ?></span></a>
                    <a class="btn btn-bx-outline-dark btn-sm" href="servicio-detalle.php?id=<?= e($s['id']) ?>">Ver detalle<span class="visually-hidden"> de <?= e($s['nombre']) ?></span></a>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
