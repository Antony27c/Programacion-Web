<?php
// ============================================================
// servicios.php — Formulario GET: buscador/filtro de servicios
// El catálogo se filtra en servidor por el parámetro ?q= (TP5)
// ============================================================
require_once __DIR__ . '/includes/funciones.php';

// Catálogo de servicios (en una versión con DB vendría de una tabla)
$servicios = [
    ['nombre' => 'Mecánica general',          'desc' => 'Motor, distribución, lubricación, refrigeración y fallas de rendimiento.', 'url' => 'servicios.php'],
    ['nombre' => 'Frenos y suspensión',       'desc' => 'Pastillas, discos, tambores, amortiguadores y dirección.',                 'url' => 'servicio-detalle.php'],
    ['nombre' => 'Diagnóstico computarizado', 'desc' => 'Escáner OBD-II, sensores, inyección y puesta a punto.',                    'url' => 'servicios.php'],
    ['nombre' => 'Service programado',        'desc' => 'Aceite, filtros, bujías, fluidos y checklist por kilometraje.',            'url' => 'servicios.php'],
    ['nombre' => 'Embrague y transmisión',    'desc' => 'Embragues, cajas, ruidos y vibraciones.',                                  'url' => 'servicios.php'],
    ['nombre' => 'Aire acondicionado',        'desc' => 'Carga de gas, fugas, compresor y clima.',                                  'url' => 'servicios.php'],
];

// Entrada GET sanitizada (filter_input + trim)
$busqueda = limpiar(filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW));

// Filtrado case-insensitive sobre nombre y descripción
$resultados = $servicios;
if ($busqueda !== '') {
    $resultados = array_values(array_filter($servicios, function ($s) use ($busqueda) {
        return mb_stripos($s['nombre'] . ' ' . $s['desc'], $busqueda) !== false;
    }));
}

$titulo      = 'BOXLY | Servicios';
$descripcion = 'Servicios del taller BOXLY en Salta: mecánica, frenos, diagnóstico y service.';
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

          <form class="bx-form mb-4" method="get" action="servicios.php" role="search">
            <div class="row g-2 align-items-end">
              <div class="col-md-9">
                <label class="form-label" for="q">Buscar servicio</label>
                <input class="form-control" id="q" name="q" type="search" placeholder="Ej: frenos, aceite, escáner..." value="<?= e($busqueda) ?>" />
              </div>
              <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-bx-primary w-100" type="submit">Buscar</button>
                <?php if ($busqueda !== ''): ?>
                  <a class="btn btn-bx-outline-dark" href="servicios.php">Limpiar</a>
                <?php endif; ?>
              </div>
            </div>
          </form>

          <?php if ($busqueda !== ''): ?>
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

          <div class="d-flex flex-column gap-3">
            <?php foreach ($resultados as $s): ?>
              <article class="bx-service-row d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                  <h2 class="h4 mb-1"><?= e($s['nombre']) ?></h2>
                  <p class="mb-0 text-secondary"><?= e($s['desc']) ?></p>
                </div>
                <a class="btn btn-bx-primary flex-shrink-0" href="<?= e($s['url']) ?>">Ver detalle</a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
