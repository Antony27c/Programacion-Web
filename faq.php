<?php
$titulo      = 'BOXLY | Preguntas frecuentes';
$descripcion = 'Preguntas frecuentes del taller BOXLY.';
require __DIR__ . '/includes/header.php';

$preguntas = [
    ['¿Necesito turno previo?', 'Sí, recomendamos sacar turno para asegurar banco y tiempo de diagnóstico. Podés reservarlo online en la sección Turnos.'],
    ['¿Dan presupuesto antes de trabajar?', 'Siempre. Te mostramos el diagnóstico y el costo por escrito antes de avanzar. Si no estás de acuerdo, no se toca nada.'],
    ['¿Qué formas de pago aceptan?', 'Efectivo, transferencia y tarjetas. Consultá las cuotas vigentes.'],
    ['¿Hay garantía?', 'Sí, en mano de obra y en repuestos según el fabricante.'],
    ['¿Atienden todas las marcas?', 'Sí. Trabajamos nafteros, diésel livianos y la mayoría de las marcas del mercado.'],
    ['¿Puedo esperar en el taller?', 'Tenemos espacio de espera. Para trabajos largos te avisamos por WhatsApp cuando el auto está listo.'],
    ['¿Cuáles son los horarios?', 'Lunes a viernes de 8:00 a 18:00 y sábados de 8:00 a 13:00. Domingos y feriados cerrado.'],
];
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Preguntas frecuentes</p>
          <h1>¿Tenés dudas?</h1>
          <p>Respuestas cortas a lo que más nos consultan. Tocá una pregunta para ver la respuesta.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row g-5">
            <div class="col-lg-8">
              <?php foreach ($preguntas as $i => [$pregunta, $respuesta]): ?>
                <details class="bx-faq-item"<?= $i === 0 ? ' open' : '' ?>>
                  <summary><h2><?= e($pregunta) ?></h2></summary>
                  <p><?= e($respuesta) ?></p>
                </details>
              <?php endforeach; ?>
            </div>
            <div class="col-lg-4">
              <aside class="bx-side-panel bx-sticky" aria-labelledby="ayuda-title">
                <div class="bx-card-icon" aria-hidden="true"><i class="bi bi-chat-dots"></i></div>
                <h2 id="ayuda-title">¿No encontraste tu respuesta?</h2>
                <p class="text-secondary">Escribinos y te respondemos en el día hábil.</p>
                <div class="d-grid gap-2">
                  <a class="btn btn-bx-primary" href="contacto.php"><i class="bi bi-envelope" aria-hidden="true"></i> Escribinos</a>
                  <a class="btn btn-bx-outline-dark" href="https://wa.me/5493875550199" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp</a>
                </div>
              </aside>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
