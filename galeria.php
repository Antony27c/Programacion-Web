<?php
$titulo      = 'BOXLY | Galería';
$descripcion = 'Así trabajamos en el taller BOXLY: tipos de trabajos que hacemos todos los días.';
require __DIR__ . '/includes/header.php';

// Tipos de trabajo que se hacen en el taller (ilustraciones propias, no fotos)
$trabajos = [
    ['img' => 'trabajo-1.svg', 'titulo' => 'Cambio de kit de distribución', 'tipo' => 'Mecánica general', 'desc' => 'Correa, tensor y bomba de agua en un mismo trabajo.'],
    ['img' => 'trabajo-2.svg', 'titulo' => 'Discos y pastillas delanteras', 'tipo' => 'Frenos', 'desc' => 'Medición de desgaste y reemplazo con repuestos de calidad.'],
    ['img' => 'trabajo-3.svg', 'titulo' => 'Diagnóstico de check engine', 'tipo' => 'Diagnóstico', 'desc' => 'Lectura de códigos con escáner OBD-II y verificación de sensores.'],
    ['img' => 'trabajo-4.svg', 'titulo' => 'Service de 60.000 km', 'tipo' => 'Service', 'desc' => 'Aceite, filtros, bujías y checklist completo del fabricante.'],
    ['img' => 'trabajo-5.svg', 'titulo' => 'Amortiguadores y alineación', 'tipo' => 'Suspensión', 'desc' => 'Cambio de amortiguadores y alineado para que no tire.'],
    ['img' => 'trabajo-6.svg', 'titulo' => 'Embrague completo', 'tipo' => 'Transmisión', 'desc' => 'Disco, placa y crapodina con prueba en ruta.'],
];
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Galería</p>
          <h1>Así trabajamos</h1>
          <p>Los trabajos que más hacemos en el taller, contados con ilustraciones propias del taller. Pronto vamos a sumar fotos reales de cada auto.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">
          <div class="row g-4">
            <?php foreach ($trabajos as $t): ?>
              <div class="col-md-6 col-lg-4 bx-reveal">
                <article class="bx-gallery-card">
                  <div class="bx-gallery-media">
                    <img src="img/galeria/<?= e($t['img']) ?>" width="800" height="500" loading="lazy" decoding="async" alt="Ilustración: <?= e(mb_strtolower($t['titulo'])) ?>" />
                  </div>
                  <div class="caption">
                    <span class="bx-tag"><?= e($t['tipo']) ?></span>
                    <h3><?= e($t['titulo']) ?></h3>
                    <p><?= e($t['desc']) ?></p>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
