document.addEventListener("DOMContentLoaded", () => {
  // La navegación activa y la validación se resuelven en PHP.
  // Acá solo queda UX extra, sin dependencias.

  // 1. Foco automático en el primer mensaje de feedback (éxito o error)
  const alerta = document.querySelector(".alert:not(.d-none)");
  if (alerta) {
    alerta.setAttribute("tabindex", "-1");
    alerta.focus({ preventScroll: false });
  }

  // 2. El skip-link mueve el foco al <main> (no solo el scroll)
  const principal = document.getElementById("contenido");
  if (principal && !principal.hasAttribute("tabindex")) {
    principal.setAttribute("tabindex", "-1");
  }

  // 3. Sombra en la navbar al hacer scroll
  const navbar = document.querySelector(".bx-navbar");
  if (navbar) {
    const marcar = () => navbar.classList.toggle("is-scrolled", window.scrollY > 8);
    marcar();
    window.addEventListener("scroll", marcar, { passive: true });
  }

  // 4. Aparición suave de bloques al entrar en pantalla (respeta prefers-reduced-motion)
  const bloques = document.querySelectorAll(".bx-reveal");
  const sinMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (sinMovimiento || !("IntersectionObserver" in window)) {
    bloques.forEach((b) => b.classList.add("is-visible"));
  } else {
    const observador = new IntersectionObserver(
      (entradas) => {
        entradas.forEach((entrada) => {
          if (entrada.isIntersecting) {
            entrada.target.classList.add("is-visible");
            observador.unobserve(entrada.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
    );
    bloques.forEach((b) => observador.observe(b));
  }

  // 5. Contador de caracteres para textareas con maxlength y data-contador
  document.querySelectorAll("textarea[data-contador]").forEach((campo) => {
    const salida = document.getElementById(campo.dataset.contador);
    const max = Number(campo.getAttribute("maxlength")) || 0;
    if (!salida || !max) return;
    const actualizar = () => {
      salida.textContent = `${campo.value.length} / ${max} caracteres`;
    };
    actualizar();
    campo.addEventListener("input", actualizar);
  });
});
