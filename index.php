<?php
$titulo      = 'BOXLY | Inicio';
$descripcion = 'BOXLY — Taller de servicio mecánico automotor en Salta. Diagnóstico claro, turnos y presupuestos honestos.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-hero" aria-labelledby="hero-title">
        <div class="container">
          <div class="row g-5 align-items-center">
            <div class="col-lg-6">
              <p class="eyebrow"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Taller mecánico en Salta</p>
              <h1 id="hero-title">Tu auto en buenas manos, <span class="bx-highlight">sin vueltas</span></h1>
              <p class="lead">
                Te explicamos la falla en palabras simples, te pasamos el presupuesto por escrito
                y recién ahí arrancamos. Técnicos matriculados, trato directo y trabajo garantizado.
              </p>
              <div class="d-flex flex-wrap gap-3 mt-4">
                <a class="btn btn-bx-primary btn-lg" href="turnos.php"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno</a>
                <a class="btn btn-bx-ghost btn-lg" href="presupuesto.php"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Pedir presupuesto</a>
              </div>
              <ul class="bx-hero-trust">
                <li><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Técnicos matriculados ETT N°3139</li>
                <li><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Presupuesto previo por escrito</li>
                <li><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Garantía en mano de obra</li>
              </ul>
            </div>
            <div class="col-lg-6 bx-hero-media">
              <img src="img/hero-taller.svg" width="640" height="520" fetchpriority="high" alt="Ilustración de un auto azul recién revisado en el taller, con avisos de turno confirmado y presupuesto aprobado" />
            </div>
          </div>
        </div>
      </section>

      <section class="bx-section-white" aria-label="Por qué elegirnos">
        <div class="container">
          <div class="bx-trust-strip">
            <div class="row g-4">
              <div class="col-md-6 col-lg-3">
                <div class="bx-feature">
                  <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--diagnostico"></span></span>
                  <div>
                    <h3>Diagnóstico claro</h3>
                    <p>Te mostramos qué falla y por qué, con el escáner o la pieza en la mano.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="bx-feature">
                  <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--presupuesto"></span></span>
                  <div>
                    <h3>Precio acordado</h3>
                    <p>Nada se hace sin tu aprobación. Sin sorpresas al retirar el auto.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="bx-feature">
                  <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--turno"></span></span>
                  <div>
                    <h3>Turnos puntuales</h3>
                    <p>Reservás online y te confirmamos por WhatsApp el mismo día.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="bx-feature">
                  <span class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--garantia"></span></span>
                  <div>
                    <h3>Trabajo garantizado</h3>
                    <p>Garantía en mano de obra y repuestos según fabricante.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-labelledby="servicios-title">
        <div class="container">
          <div class="bx-section-head">
            <p class="eyebrow">Servicios</p>
            <h2 id="servicios-title">Lo que resolvemos en el taller</h2>
            <p>Desde el service de rutina hasta esa luz del tablero que no se apaga.</p>
          </div>
          <div class="row g-4">
            <div class="col-md-6 col-lg-3 bx-reveal">
              <article class="bx-card bx-card-link">
                <div class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--aceite"></span></div>
                <h3>Service programado</h3>
                <p>Aceite, filtros, fluidos y checklist según el kilometraje de tu auto.</p>
                <a class="bx-card-cta" href="turnos.php">Sacar turno <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
              </article>
            </div>
            <div class="col-md-6 col-lg-3 bx-reveal">
              <article class="bx-card bx-card-link">
                <div class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--frenos"></span></div>
                <h3>Frenos y suspensión</h3>
                <p>Pastillas, discos, amortiguadores y alineación preventiva.</p>
                <a class="bx-card-cta" href="servicio-detalle.php">Ver detalle <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
              </article>
            </div>
            <div class="col-md-6 col-lg-3 bx-reveal">
              <article class="bx-card bx-card-link">
                <div class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--diagnostico"></span></div>
                <h3>Diagnóstico computarizado</h3>
                <p>Escáner OBD, sensores y puesta a punto electrónica.</p>
                <a class="bx-card-cta" href="presupuesto.php">Pedir presupuesto <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
              </article>
            </div>
            <div class="col-md-6 col-lg-3 bx-reveal">
              <article class="bx-card bx-card-link">
                <div class="bx-card-icon" aria-hidden="true"><i class="bi bi-gear-wide-connected"></i></div>
                <h3>Mecánica general</h3>
                <p>Motor, distribución, correas, lubricación y fallas de rendimiento.</p>
                <a class="bx-card-cta" href="presupuesto.php">Pedir presupuesto <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
              </article>
            </div>
          </div>
          <div class="text-center mt-5">
            <a class="btn btn-bx-outline-dark" href="servicios.php">Ver todos los servicios</a>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-sand" aria-labelledby="pasos-title">
        <div class="container">
          <div class="bx-section-head">
            <p class="eyebrow">Cómo trabajamos</p>
            <h2 id="pasos-title">Cuatro pasos, cero sorpresas</h2>
            <p>Así es una visita a BOXLY, desde que reservás hasta que te llevás el auto.</p>
          </div>
          <ol class="bx-steps row g-4">
            <li class="col-sm-6 col-lg-3 bx-reveal">
              <div class="bx-step">
                <h3>Sacás turno</h3>
                <p>Elegís día y horario online. Te confirmamos por WhatsApp.</p>
              </div>
            </li>
            <li class="col-sm-6 col-lg-3 bx-reveal">
              <div class="bx-step">
                <h3>Diagnosticamos</h3>
                <p>Revisamos el auto y te explicamos la falla sin tecnicismos.</p>
              </div>
            </li>
            <li class="col-sm-6 col-lg-3 bx-reveal">
              <div class="bx-step">
                <h3>Aprobás el presupuesto</h3>
                <p>Te lo pasamos por escrito. Si no estás de acuerdo, no se toca nada.</p>
              </div>
            </li>
            <li class="col-sm-6 col-lg-3 bx-reveal">
              <div class="bx-step">
                <h3>Retirás con garantía</h3>
                <p>Prueba en ruta, entrega puntual y garantía en mano de obra.</p>
              </div>
            </li>
          </ol>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-labelledby="nosotros-title">
        <div class="container">
          <div class="row g-5 align-items-center">
            <div class="col-lg-6">
              <img class="bx-img" src="img/taller.svg" width="800" height="500" loading="lazy" decoding="async" alt="Ilustración de un auto en el elevador dentro del taller BOXLY" />
            </div>
            <div class="col-lg-6">
              <p class="eyebrow">Nosotros</p>
              <h2 id="nosotros-title" class="h1 mb-3">Mecánica con criterio técnico y trato de barrio</h2>
              <p class="bx-lead">
                Somos un taller de Salta con formación de Técnico en Automotores.
                Explicamos cada falla en lenguaje claro, mostramos el diagnóstico y
                acordamos el trabajo antes de tocar una herramienta.
              </p>
              <ul class="bx-list-check">
                <li>Técnicos matriculados ETT N°3139</li>
                <li>Presupuesto previo por escrito</li>
                <li>Garantía en mano de obra</li>
              </ul>
              <a class="btn btn-bx-outline-dark mt-3" href="nosotros.php">Conocé el taller</a>
            </div>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-muted" aria-labelledby="testimonios-title">
        <div class="container">
          <div class="bx-section-head">
            <p class="eyebrow">Testimonios</p>
            <h2 id="testimonios-title">Lo que dicen nuestros clientes</h2>
            <p>Opiniones de clientes de Salta que ya pasaron por el taller.</p>
          </div>
          <div class="row g-4">
            <div class="col-md-6 col-lg-4 bx-reveal">
              <blockquote class="bx-quote">
                <p class="bx-stars" role="img" aria-label="5 de 5 estrellas">★★★★★</p>
                <p>“Me explicaron todo sin tecnicismos y el auto quedó impecable.”</p>
                <footer><span class="bx-avatar" aria-hidden="true">LM</span><cite>Laura M.<small>Service + frenos</small></cite></footer>
              </blockquote>
            </div>
            <div class="col-md-6 col-lg-4 bx-reveal">
              <blockquote class="bx-quote">
                <p class="bx-stars" role="img" aria-label="5 de 5 estrellas">★★★★★</p>
                <p>“Presupuesto claro y cumplieron el plazo. Volvería sin dudar.”</p>
                <footer><span class="bx-avatar bx-avatar--amber" aria-hidden="true">DR</span><cite>Diego R.<small>Diagnóstico</small></cite></footer>
              </blockquote>
            </div>
            <div class="col-md-6 col-lg-4 bx-reveal">
              <blockquote class="bx-quote">
                <p class="bx-stars" role="img" aria-label="5 de 5 estrellas">★★★★★</p>
                <p>“Llevé el auto con un ruido raro y lo encontraron al toque. Me mostraron la pieza.”</p>
                <footer><span class="bx-avatar bx-avatar--green" aria-hidden="true">AP</span><cite>Ana P.<small>Ruido en el motor</small></cite></footer>
              </blockquote>
            </div>
          </div>
          <div class="text-center mt-5">
            <a class="btn btn-bx-outline-dark" href="testimonios.php">Ver más opiniones</a>
          </div>
        </div>
      </section>

      <section class="bx-section bx-section-white" aria-label="Garantía, horarios y ubicación">
        <div class="container">
          <div class="row g-4 align-items-stretch">
            <div class="col-lg-6">
              <div class="bx-card bx-card-static">
                <div class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--garantia"></span></div>
                <h2 class="h3">Garantía BOXLY</h2>
                <p class="mb-3">Todo trabajo tiene garantía en mano de obra, y los repuestos la del fabricante. Si algo no quedó bien, volvés y lo resolvemos.</p>
                <ul class="bx-list-check mt-0">
                  <li>Reporte de lo que se cambió y por qué</li>
                  <li>Te mostramos las piezas reemplazadas</li>
                  <li>Prueba en ruta antes de la entrega</li>
                </ul>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="bx-card bx-card-static">
                <div class="bx-card-icon" aria-hidden="true"><span class="bx-icon bx-icon--ubicacion"></span></div>
                <h2 class="h3">Horarios y ubicación</h2>
                <p class="mb-3">Av. Bolivia 2450, Salta Capital.</p>
                <ul class="bx-hours mb-4">
                  <li><span>Lunes a viernes</span> <strong>8:00 – 18:00</strong></li>
                  <li><span>Sábados</span> <strong>8:00 – 13:00</strong></li>
                  <li><span>Domingos y feriados</span> <strong>Cerrado</strong></li>
                </ul>
                <div class="d-flex flex-wrap gap-2">
                  <a class="btn btn-bx-primary" href="contacto.php"><i class="bi bi-signpost-split" aria-hidden="true"></i> Cómo llegar</a>
                  <a class="btn btn-bx-outline-dark" href="https://wa.me/5493875550199" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
