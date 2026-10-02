document.addEventListener("DOMContentLoaded", () => {
  // En clase5 la navegación activa y la validación se resuelven en PHP.
  // Acá solo queda UX extra: foco automático en el primer mensaje de feedback.
  const alerta = document.querySelector(".alert:not(.d-none)");
  if (alerta) {
    alerta.setAttribute("tabindex", "-1");
    alerta.focus({ preventScroll: false });
  }
});
