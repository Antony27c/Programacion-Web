<?php
$titulo      = 'BOXLY | Frenos y suspensión';
$descripcion = 'Detalle del servicio de frenos y suspensión en BOXLY Salta.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow"><a href="servicios.php">Servicios</a> <span aria-hidden="true">/</span> Detalle</p>
          <h1>Frenos y suspensión</h1>
          <p>Seguridad primero: revisamos, medimos y te mostramos el estado real antes de cambiar nada.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row g-5">
            <div class="col-lg-8">
              <img class="bx-img mb-5" src="img/frenos-suspension.svg" width="800" height="500" alt="Ilustración de un disco de freno ventilado con caliper y amortiguador" />

              <h2 class="mb-3">Qué incluye</h2>
              <div class="row g-3 mb-5">
                <div class="col-sm-6">
                  <div class="bx-feature">
                    <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--frenos"></span></span>
                    <div><h3>Inspección de frenos</h3><p>Pastillas, discos y líquido de frenos.</p></div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bx-feature">
                    <span class="bx-card-icon" aria-hidden="true"><i class="bi bi-rulers"></i></span>
                    <div><h3>Medición y reporte</h3><p>Medimos el desgaste y te mandamos fotos.</p></div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bx-feature">
                    <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--suspension"></span></span>
                    <div><h3>Cambio de componentes</h3><p>Con repuestos de calidad y a tu elección.</p></div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bx-feature">
                    <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--alineacion"></span></span>
                    <div><h3>Prueba en ruta</h3><p>Probamos el auto y reajustamos si hace falta.</p></div>
                  </div>
                </div>
              </div>

              <h2 class="h3 mb-3">¿Cuándo conviene revisarlos?</h2>
              <ul class="bx-list-check">
                <li>Sentís ruido o chirrido al frenar</li>
                <li>El pedal se siente esponjoso o baja más de lo normal</li>
                <li>El auto vibra o tira hacia un costado al frenar</li>
                <li>Rebota mucho en pozos o lomos de burro</li>
              </ul>
            </div>
            <div class="col-lg-4">
              <aside class="bx-side-panel bx-sticky" aria-labelledby="precio-title">
                <h2 id="precio-title" class="h5 mb-1">Precio</h2>
                <p class="bx-price mb-2">Según diagnóstico</p>
                <p class="small text-secondary">Revisamos el auto y te pasamos el presupuesto por escrito antes de empezar. Sin tu aprobación no se hace ningún trabajo.</p>
                <div class="d-grid gap-2">
                  <a class="btn btn-bx-primary" href="presupuesto.php"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Pedir presupuesto</a>
                  <a class="btn btn-bx-outline-dark" href="turnos.php"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno</a>
                </div>
                <p class="bx-badge-ok mt-3 mb-0"><i class="bi bi-shield-check" aria-hidden="true"></i> Garantía en mano de obra</p>
              </aside>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
