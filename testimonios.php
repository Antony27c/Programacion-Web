<?php
$titulo      = 'BOXLY | Testimonios';
$descripcion = 'Opiniones de clientes del taller BOXLY en Salta.';
require __DIR__ . '/includes/header.php';

$opiniones = [
    ['Laura M.', 'Service + frenos', 'Me explicaron todo sin tecnicismos y el auto quedó impecable.', ''],
    ['Diego R.', 'Diagnóstico', 'Presupuesto claro y cumplieron el plazo. Volvería sin dudar.', 'bx-avatar--amber'],
    ['Ana P.', 'Ruido en el motor', 'Encontraron la falla rápido y me mostraron la pieza.', 'bx-avatar--green'],
    ['Carlos V.', 'Service', 'Buen trato, precio justo y taller ordenado.', ''],
    ['Sofía L.', 'Suspensión', 'Me dieron opciones de repuesto según mi presupuesto.', 'bx-avatar--amber'],
    ['Martín G.', 'Embrague', 'Trabajo prolijo y entrega cuando dijeron.', 'bx-avatar--green'],
];
// Iniciales para el avatar: "Laura M." -> "LM"
$iniciales = fn (string $nombre): string => implode('', array_map(fn ($p) => mb_substr($p, 0, 1), explode(' ', $nombre)));
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Testimonios</p>
          <h1>Opiniones de clientes</h1>
          <p>Clientes de Salta que ya pasaron por el taller y nos dejaron su comentario.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">
          <div class="row g-4">
            <?php foreach ($opiniones as [$nombre, $servicio, $texto, $color]): ?>
              <div class="col-md-6 col-lg-4 bx-reveal">
                <blockquote class="bx-quote">
                  <p class="bx-stars" role="img" aria-label="5 de 5 estrellas">★★★★★</p>
                  <p>“<?= e($texto) ?>”</p>
                  <footer>
                    <span class="bx-avatar <?= e($color) ?>" aria-hidden="true"><?= e($iniciales($nombre)) ?></span>
                    <cite><?= e($nombre) ?><small><?= e($servicio) ?></small></cite>
                  </footer>
                </blockquote>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
