<?php
$titulo      = 'BOXLY | Galería';
$descripcion = 'Trabajos realizados en el taller BOXLY.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Galería</p>
          <h1>Trabajos en el taller</h1>
          <p>Algunos servicios realizados en BOXLY.</p>
        </div>
      </section>
      <section class="bx-section bx-section-muted">
        <div class="container">
          <div class="row g-4">
            <div class="col-md-6 col-lg-4">
              <article class="bx-gallery-card">
                <img src="img/galeria/trabajo-1.svg" width="800" height="500" loading="lazy" alt="Cambio de kit de distribución" />
                <div class="caption">
                  <h3>Cambio de kit de distribución</h3>
                  <p>Gol Trend</p>
                </div>
              </article>
            </div>
            <div class="col-md-6 col-lg-4">
              <article class="bx-gallery-card">
                <img src="img/galeria/trabajo-2.svg" width="800" height="500" loading="lazy" alt="Discos y pastillas delanteras" />
                <div class="caption">
                  <h3>Discos y pastillas delanteras</h3>
                  <p>Toyota Corolla</p>
                </div>
              </article>
            </div>
            <div class="col-md-6 col-lg-4">
              <article class="bx-gallery-card">
                <img src="img/galeria/trabajo-3.svg" width="800" height="500" loading="lazy" alt="Diagnóstico check engine" />
                <div class="caption">
                  <h3>Diagnóstico check engine</h3>
                  <p>Ford Focus</p>
                </div>
              </article>
            </div>
            <div class="col-md-6 col-lg-4">
              <article class="bx-gallery-card">
                <img src="img/galeria/trabajo-4.svg" width="800" height="500" loading="lazy" alt="Service 60000 km" />
                <div class="caption">
                  <h3>Service 60.000 km</h3>
                  <p>Chevrolet Onix</p>
                </div>
              </article>
            </div>
            <div class="col-md-6 col-lg-4">
              <article class="bx-gallery-card">
                <img src="img/galeria/trabajo-5.svg" width="800" height="500" loading="lazy" alt="Amortiguadores y alineación" />
                <div class="caption">
                  <h3>Amortiguadores + alineación</h3>
                  <p>Renault Sandero</p>
                </div>
              </article>
            </div>
            <div class="col-md-6 col-lg-4">
              <article class="bx-gallery-card">
                <img src="img/galeria/trabajo-6.svg" width="800" height="500" loading="lazy" alt="Embrague completo" />
                <div class="caption">
                  <h3>Embrague completo</h3>
                  <p>Fiat Palio</p>
                </div>
              </article>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
