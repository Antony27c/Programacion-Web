<?php
$titulo      = 'BOXLY | Inicio';
$descripcion = 'BOXLY — Taller de servicio mecánico automotor en Salta. Diagnóstico claro, turnos y presupuestos honestos.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-hero" aria-labelledby="hero-title">
        <div class="container">
          <p class="eyebrow">Taller en Salta</p>
          <h1 id="hero-title">Tu auto en manos<br />técnicas de verdad</h1>
          <p class="lead">
            Diagnóstico claro, presupuestos honestos y turnos sin vueltas. Formación
            técnica real al servicio de tu vehículo.
          </p>
          <div class="d-flex flex-wrap gap-3 mt-4">
            <a class="btn btn-bx-primary" href="turnos.php">Agendar turno</a>
            <a class="btn btn-bx-ghost" href="servicios.php">Ver servicios</a>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-labelledby="nosotros-title">
        <div class="container">
          <div class="row g-5 align-items-center">
            <div class="col-lg-6">
              <div class="bx-placeholder bx-placeholder--tall" role="img" aria-label="Foto del taller BOXLY">
                FOTO TALLER
              </div>
            </div>
            <div class="col-lg-6">
              <p class="eyebrow">Nosotros</p>
              <h2 id="nosotros-title">Mecánica con criterio técnico</h2>
              <p class="text-secondary">
                Somos un taller de Salta con formación de Técnico en Automotores.
                Explicamos cada falla en lenguaje claro, mostramos el diagnóstico y
                acordamos el trabajo antes de tocar una herramienta.
              </p>
              <ul class="bx-list-check">
                <li>Técnicos matriculados ETT N°3139</li>
                <li>Presupuesto previo por escrito</li>
                <li>Garantía en mano de obra</li>
              </ul>
              <a class="btn btn-bx-primary mt-3" href="nosotros.php">Conocenos</a>
            </div>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-muted" aria-labelledby="servicios-title">
        <div class="container">
          <div class="text-center mb-5">
            <p class="eyebrow">Servicios</p>
            <h2 id="servicios-title">Lo que resolvemos en el taller</h2>
          </div>
          <div class="row g-4">
            <div class="col-md-6">
              <article class="bx-card">
                <div class="bx-card-icon" aria-hidden="true"></div>
                <h3>Mecánica general</h3>
                <p>Motor, distribución, correas, lubricación y fallas de rendimiento.</p>
              </article>
            </div>
            <div class="col-md-6">
              <article class="bx-card">
                <div class="bx-card-icon" aria-hidden="true"></div>
                <h3>Frenos y suspensión</h3>
                <p>Pastillas, discos, amortiguadores y alineación preventiva.</p>
              </article>
            </div>
            <div class="col-md-6">
              <article class="bx-card">
                <div class="bx-card-icon" aria-hidden="true"></div>
                <h3>Diagnóstico computarizado</h3>
                <p>Escáner OBD, sensores y puesta a punto electrónica.</p>
              </article>
            </div>
            <div class="col-md-6">
              <article class="bx-card">
                <div class="bx-card-icon" aria-hidden="true"></div>
                <h3>Service programado</h3>
                <p>Aceite, filtros, fluidos y checklist según kilometraje.</p>
              </article>
            </div>
          </div>
          <div class="text-center mt-4">
            <a class="btn btn-bx-primary" href="servicios.php">Ver todos los servicios</a>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-labelledby="testimonios-title">
        <div class="container">
          <div class="text-center mb-5">
            <p class="eyebrow">Testimonios</p>
            <h2 id="testimonios-title">Lo que dicen nuestros clientes</h2>
          </div>
          <div class="row g-3">
            <div class="col-md-6 col-lg-3">
              <blockquote class="bx-quote">
                <p>“Me explicaron todo sin tecnicismos.”</p>
                <cite>Laura M.</cite>
              </blockquote>
            </div>
            <div class="col-md-6 col-lg-3">
              <blockquote class="bx-quote">
                <p>“Presupuesto claro y cumplieron el plazo.”</p>
                <cite>Diego R.</cite>
              </blockquote>
            </div>
            <div class="col-md-6 col-lg-3">
              <blockquote class="bx-quote">
                <p>“Llevé el auto con un ruido raro y lo encontraron al toque.”</p>
                <cite>Ana P.</cite>
              </blockquote>
            </div>
            <div class="col-md-6 col-lg-3">
              <blockquote class="bx-quote">
                <p>“Buen trato y precio justo en Salta.”</p>
                <cite>Carlos V.</cite>
              </blockquote>
            </div>
          </div>
          <div class="text-center mt-4">
            <a class="btn btn-bx-outline-dark" href="testimonios.php">Ver más opiniones</a>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
