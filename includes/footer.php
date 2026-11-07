<?php
// footer.php — banda de cierre con llamada a la acción + pie de página compartido
// Usa $base y $pagina (definidos en header.php)
$sinBandaCta = ['admin', 'login.php', 'registro.php', 'mi-cuenta.php', 'turnos.php', 'presupuesto.php', 'contacto.php'];
?>
    <?php if (!in_array($pagina ?? '', $sinBandaCta, true)): ?>
      <section class="bx-cta-section bx-section-white" aria-labelledby="cta-title">
        <div class="container">
          <div class="bx-cta-band">
            <div class="row g-4 align-items-center">
              <div class="col-lg-7">
                <h2 id="cta-title">¿Tu auto necesita una mano?</h2>
                <p>Sacá turno online en 2 minutos o contanos el problema y te pasamos un presupuesto sin compromiso.</p>
              </div>
              <div class="col-lg-5 d-flex flex-wrap gap-3 justify-content-lg-end">
                <a class="btn btn-bx-light btn-lg" href="<?= e($base) ?>turnos.php"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno</a>
                <a class="btn btn-bx-ghost btn-lg" href="<?= e($base) ?>presupuesto.php"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Pedir presupuesto</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    <?php endif; ?>
    <footer class="bx-footer">
      <div class="container">
        <div class="row g-4">
          <div class="col-12 col-lg-4">
            <p class="brand"><span class="bx-logo-mark" aria-hidden="true">BX</span> BOXLY</p>
            <p class="mb-3">Taller de servicio automotor en Salta. Diagnóstico honesto, presupuesto por escrito y trabajo garantizado.</p>
            <a class="btn btn-bx-ghost btn-sm" href="https://wa.me/5493875550199" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Escribinos por WhatsApp</a>
          </div>
          <div class="col-6 col-md-4 col-lg-2">
            <h3>Servicios</h3>
            <ul>
              <li><a href="<?= e($base) ?>servicios.php">Mecánica general</a></li>
              <li><a href="<?= e($base) ?>servicio-detalle.php">Frenos y suspensión</a></li>
              <li><a href="<?= e($base) ?>servicios.php">Diagnóstico computarizado</a></li>
              <li><a href="<?= e($base) ?>servicios.php">Service programado</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-4 col-lg-2">
            <h3>El taller</h3>
            <ul>
              <li><a href="<?= e($base) ?>nosotros.php">Nosotros</a></li>
              <li><a href="<?= e($base) ?>presupuesto.php">Pedir presupuesto</a></li>
              <li><a href="<?= e($base) ?>testimonios.php">Testimonios</a></li>
              <li><a href="<?= e($base) ?>faq.php">Preguntas frecuentes</a></li>
            </ul>
          </div>
          <div class="col-12 col-md-4 col-lg-4">
            <h3>Contacto y horarios</h3>
            <ul>
              <li><i class="bi bi-geo-alt" aria-hidden="true"></i>Av. Bolivia 2450, Salta Capital</li>
              <li><i class="bi bi-clock" aria-hidden="true"></i>Lun a Vie 8:00–18:00 · Sáb 8:00–13:00</li>
              <li><i class="bi bi-telephone" aria-hidden="true"></i><a href="tel:+543875550199">+54 387 555-0199</a></li>
              <li><i class="bi bi-envelope" aria-hidden="true"></i><a href="mailto:turnos@boxly.com.ar">turnos@boxly.com.ar</a></li>
            </ul>
          </div>
        </div>
        <div class="bx-footer-copy">
          <p class="mb-0">© <?= date('Y') ?> BOXLY · Salta, Argentina</p>
          <p class="mb-0">Técnicos matriculados ETT N°3139</p>
        </div>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= e($base) ?>js/main.js"></script>
  </body>
</html>
