<?php
// ============================================================
// servicio-detalle.php — Detalle de un servicio del catálogo
// GET ?id=N → servicio de la tabla servicios (modelo Servicio).
// Sin id muestra Frenos (enlace histórico del footer); id inexistente → 404.
// ============================================================
use App\Models\Servicio;

require __DIR__ . '/app/bootstrap.php';

$modelo   = new Servicio();
$idTexto  = limpiar(filter_input(INPUT_GET, 'id', FILTER_UNSAFE_RAW));
$servicio = null;
try {
    if ($idTexto === '') {
        $servicio = $modelo->buscar('Frenos')[0] ?? null;
    } elseif (ctype_digit($idTexto) && (int) $idTexto > 0) {
        $servicio = $modelo->buscarPorId((int) $idTexto);
    }
} catch (PDOException $ex) {
    error_log('servicio-detalle: ' . $ex->getMessage());
}

// Contenido de presentación según el tipo de servicio (mismo ícono que el catálogo)
$contenidos = [
    'frenos' => [
        'bajada'  => 'Seguridad primero: revisamos, medimos y te mostramos el estado real antes de cambiar nada.',
        'incluye' => [['frenos', 'Inspección de frenos', 'Pastillas, discos y líquido de frenos.'], ['', 'Medición y reporte', 'Medimos el desgaste y te mandamos fotos.'], ['suspension', 'Cambio de componentes', 'Con repuestos de calidad y a tu elección.'], ['alineacion', 'Prueba en ruta', 'Probamos el auto y reajustamos si hace falta.']],
        'cuando'  => ['Sentís ruido o chirrido al frenar', 'El pedal se siente esponjoso o baja más de lo normal', 'El auto vibra o tira hacia un costado al frenar'],
        'imagen'  => ['img/frenos-suspension.svg', 'Ilustración de un disco de freno ventilado con caliper y amortiguador'],
    ],
    'aceite' => [
        'bajada'  => 'El mantenimiento que más alarga la vida del motor, con el checklist que pide tu fabricante.',
        'incluye' => [['aceite', 'Aceite y filtros', 'Aceite según especificación, filtro de aceite, aire y habitáculo.'], ['', 'Control de fluidos', 'Refrigerante, frenos, dirección y limpiaparabrisas.'], ['diagnostico', 'Checklist general', 'Correas, luces, batería y testigos del tablero.'], ['garantia', 'Registro del service', 'Te dejamos anotado el kilometraje del próximo.']],
        'cuando'  => ['Llegaste al kilometraje indicado en el manual', 'Pasó un año desde el último service', 'Se encendió el testigo de aceite o de mantenimiento'],
        'imagen'  => ['img/taller.svg', 'Ilustración de un auto en el elevador dentro del taller BOXLY'],
    ],
    'diagnostico' => [
        'bajada'  => 'Leemos lo que dice la computadora del auto y lo traducimos a lenguaje claro.',
        'incluye' => [['diagnostico', 'Escaneo OBD-II', 'Lectura de códigos de falla y datos en vivo.'], ['bateria', 'Revisión eléctrica', 'Sensores, batería y sistema de carga.'], ['', 'Informe claro', 'Te explicamos qué falla y qué es urgente.'], ['presupuesto', 'Presupuesto por escrito', 'Antes de cualquier reparación.']],
        'cuando'  => ['Se encendió el check engine u otro testigo', 'El auto pierde potencia o consume más', 'Tironea o se apaga en ralentí'],
        'imagen'  => ['img/taller.svg', 'Ilustración de un auto en el elevador dentro del taller BOXLY'],
    ],
];
$generico = [
    'bajada'  => 'Revisamos el auto, te explicamos lo que encontramos y cotizamos el trabajo antes de empezar.',
    'incluye' => [['diagnostico', 'Revisión inicial', 'Inspección del problema que nos contás.'], ['presupuesto', 'Presupuesto por escrito', 'Con el detalle de repuestos y mano de obra.'], ['', 'Trabajo acordado', 'Solo hacemos lo que aprobaste.'], ['garantia', 'Garantía', 'En mano de obra y repuestos según fabricante.']],
    'cuando'  => ['Notás un ruido, olor o vibración nueva', 'Querés una segunda opinión sobre un presupuesto', 'Tenés un trabajo que no está en el catálogo'],
    'imagen'  => ['img/taller.svg', 'Ilustración de un auto en el elevador dentro del taller BOXLY'],
];

if ($servicio === null) {
    http_response_code(404);
    $titulo      = 'BOXLY | Servicio no encontrado';
    $descripcion = 'El servicio solicitado no existe en el catálogo de BOXLY.';
} else {
    $icono       = Servicio::icono($servicio['nombre']);
    $contenido   = $contenidos[$icono] ?? $generico;
    $titulo      = 'BOXLY | ' . $servicio['nombre'];
    $descripcion = $servicio['nombre'] . ' en BOXLY Salta: ' . $servicio['descripcion'];
}
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <?php if ($servicio === null): ?>
        <section class="bx-section bx-section-white">
          <div class="container">
            <div class="bx-empty">
              <img src="img/ilustraciones/estado-vacio.svg" width="480" height="320" alt="" />
              <p class="eyebrow">Error 404</p>
              <h1 class="h2">No encontramos ese servicio</h1>
              <p>Puede que el enlace esté mal escrito o que el servicio ya no esté en el catálogo.</p>
              <div class="d-flex flex-wrap justify-content-center gap-2">
                <a class="btn btn-bx-primary" href="servicios.php"><i class="bi bi-grid" aria-hidden="true"></i> Ver el catálogo</a>
                <a class="btn btn-bx-outline-dark" href="presupuesto.php">Pedir presupuesto</a>
              </div>
            </div>
          </div>
        </section>
      <?php else: ?>
        <section class="bx-page-hero">
          <div class="container">
            <nav aria-label="Ruta de navegación">
              <ol class="bx-breadcrumb">
                <li><a href="servicios.php">Servicios</a></li>
                <li aria-current="page"><?= e($servicio['nombre']) ?></li>
              </ol>
            </nav>
            <div class="d-flex align-items-center gap-3 mb-2">
              <span class="bx-card-icon mb-0" aria-hidden="true"><span class="bx-icon bx-icon--<?= e($icono) ?>"></span></span>
              <h1 class="mb-0"><?= e($servicio['nombre']) ?></h1>
            </div>
            <p><?= e($servicio['descripcion']) ?> <?= e($contenido['bajada']) ?></p>
          </div>
        </section>
        <section class="bx-section bx-section-white">
          <div class="container">
            <div class="row g-5">
              <div class="col-lg-8">
                <img class="bx-img mb-5" src="<?= e($contenido['imagen'][0]) ?>" width="800" height="500" alt="<?= e($contenido['imagen'][1]) ?>" />

                <h2 class="mb-3">Qué incluye</h2>
                <div class="row g-3 mb-5">
                  <?php foreach ($contenido['incluye'] as [$ic, $tit, $txt]): ?>
                    <div class="col-sm-6">
                      <div class="bx-feature">
                        <span class="bx-card-icon" aria-hidden="true"><?php if ($ic !== ''): ?><span class="bx-icon bx-icon--<?= e($ic) ?>"></span><?php else: ?><i class="bi bi-clipboard-check"></i><?php endif; ?></span>
                        <div><h3><?= e($tit) ?></h3><p><?= e($txt) ?></p></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <h2 class="h3 mb-3">¿Cuándo conviene traerlo?</h2>
                <ul class="bx-list-check">
                  <?php foreach ($contenido['cuando'] as $item): ?>
                    <li><?= e($item) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="col-lg-4">
                <aside class="bx-side-panel bx-sticky" aria-labelledby="precio-title">
                  <h2 id="precio-title" class="h5 mb-1">Precio orientativo</h2>
                  <p class="bx-price mb-2"><?php if ($servicio['precio_base'] > 0): ?><small class="bx-price-desde">Desde</small> <?php endif; ?><?= e(Servicio::precio($servicio['precio_base'])) ?></p>
                  <p class="small text-secondary">El precio final lo confirmamos por escrito después del diagnóstico. Sin tu aprobación no se hace ningún trabajo.</p>
                  <div class="d-grid gap-2">
                    <a class="btn btn-bx-primary" href="turnos.php?servicio=<?= e($servicio['id']) ?>"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno</a>
                    <a class="btn btn-bx-outline-dark" href="presupuesto.php"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Pedir presupuesto</a>
                  </div>
                  <p class="bx-badge-ok mt-3 mb-0"><i class="bi bi-shield-check" aria-hidden="true"></i> Garantía en mano de obra</p>
                </aside>
              </div>
            </div>
          </div>
        </section>
      <?php endif; ?>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
