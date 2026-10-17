    <footer class="bx-footer">
      <div class="container">
        <div class="row g-4">
          <div class="col-12 col-lg-3">
            <p class="brand">BOXLY</p>
            <p>Taller de servicio automotor en Salta. Diagnóstico honesto. Trabajo garantizado.</p>
          </div>
          <div class="col-6 col-md-4 col-lg-3">
            <h3>Servicios</h3>
            <ul>
              <li><a href="<?= e($base) ?>servicios.php">Mecánica general</a></li>
              <li><a href="<?= e($base) ?>servicio-detalle.php">Frenos y suspensión</a></li>
              <li><a href="<?= e($base) ?>servicios.php">Diagnóstico computarizado</a></li>
              <li><a href="<?= e($base) ?>servicios.php">Service programado</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-4 col-lg-3">
            <h3>El taller</h3>
            <ul>
              <li><a href="<?= e($base) ?>nosotros.php">Nosotros</a></li>
              <li><a href="<?= e($base) ?>presupuesto.php">Pedir presupuesto</a></li>
              <li><a href="<?= e($base) ?>testimonios.php">Testimonios</a></li>
              <li><a href="<?= e($base) ?>faq.php">Preguntas frecuentes</a></li>
            </ul>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <h3>Contacto</h3>
            <ul>
              <li>Av. Bolivia 2450, Salta</li>
              <li>Lun–Vie 8:00–18:00</li>
              <li><a href="tel:+543875550199">+54 387 555-0199</a></li>
              <li><a href="mailto:turnos@boxly.com.ar">turnos@boxly.com.ar</a></li>
            </ul>
          </div>
        </div>
        <p class="bx-footer-copy mb-0">© <?= date('Y') ?> BOXLY · Salta, Argentina</p>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= e($base) ?>js/main.js"></script>
  </body>
</html>
