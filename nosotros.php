<?php
$titulo      = 'BOXLY | Nosotros';
$descripcion = 'Conocé BOXLY: taller de servicio automotor en Salta con criterio técnico.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Nosotros</p>
          <h1>Quiénes somos</h1>
          <p>Técnicos formados, taller local y trato directo con el dueño del auto.</p>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-labelledby="historia-title">
        <div class="container">
          <div class="row g-5 align-items-center">
            <div class="col-lg-6">
              <img class="bx-img" src="img/equipo.svg" width="800" height="500" loading="lazy" decoding="async" alt="Ilustración del equipo de técnicos automotores del taller BOXLY" />
            </div>
            <div class="col-lg-6">
              <h2 id="historia-title" class="h1 mb-3">De la formación técnica al banco de trabajo</h2>
              <p class="bx-lead">BOXLY nace de la experiencia como Técnico en Automotores (ETT N°3139) y de la necesidad de talleres en Salta con presencia digital clara: turnos, servicios y confianza sin letra chica.</p>
              <ul class="bx-list-check">
                <li>Te atiende quien trabaja en tu auto</li>
                <li>Diagnóstico verificable antes de cualquier arreglo</li>
                <li>Presupuesto por escrito y garantía en mano de obra</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-sand" aria-labelledby="valores-title">
        <div class="container">
          <div class="bx-section-head">
            <p class="eyebrow">Lo que nos mueve</p>
            <h2 id="valores-title">Misión, visión y valores</h2>
          </div>
          <div class="row g-4">
            <div class="col-md-4 bx-reveal">
              <article class="bx-card bx-card-hover">
                <div class="bx-card-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></div>
                <h3>Misión</h3>
                <p>Resolver fallas con diagnóstico verificable y explicación comprensible.</p>
              </article>
            </div>
            <div class="col-md-4 bx-reveal">
              <article class="bx-card bx-card-hover">
                <div class="bx-card-icon" aria-hidden="true"><i class="bi bi-binoculars"></i></div>
                <h3>Visión</h3>
                <p>Ser el taller de referencia en Salta por transparencia y calidad técnica.</p>
              </article>
            </div>
            <div class="col-md-4 bx-reveal">
              <article class="bx-card bx-card-hover">
                <div class="bx-card-icon" aria-hidden="true"><i class="bi bi-heart"></i></div>
                <h3>Valores</h3>
                <p>Honestidad, puntualidad, seguridad y respeto por el vehículo del cliente.</p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-labelledby="pasos-title">
        <div class="container">
          <div class="bx-section-head">
            <p class="eyebrow">Cómo trabajamos</p>
            <h2 id="pasos-title">Así es una visita al taller</h2>
          </div>
          <ol class="bx-steps row g-4">
            <li class="col-sm-6 col-lg-3"><div class="bx-step"><h3>Sacás turno</h3><p>Online o por WhatsApp, en el horario que te quede cómodo.</p></div></li>
            <li class="col-sm-6 col-lg-3"><div class="bx-step"><h3>Diagnosticamos</h3><p>Revisamos el auto y te explicamos qué encontramos.</p></div></li>
            <li class="col-sm-6 col-lg-3"><div class="bx-step"><h3>Aprobás</h3><p>Presupuesto por escrito: sin tu OK no se toca nada.</p></div></li>
            <li class="col-sm-6 col-lg-3"><div class="bx-step"><h3>Retirás</h3><p>Prueba en ruta, entrega puntual y garantía.</p></div></li>
          </ol>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
