// ============================================================
// auth.js — Ayudas de presentación en login y registro
// 1) Botón mostrar/ocultar contraseña (aria-pressed)
// 2) Requisitos de contraseña marcados en vivo (registro)
// La validación real sigue siendo la del servidor (validarRegistro()).
// ============================================================
document.addEventListener("DOMContentLoaded", () => {
  // 1. Mostrar / ocultar contraseña
  document.querySelectorAll("[data-toggle-password]").forEach((boton) => {
    const campo = document.getElementById(boton.getAttribute("aria-controls"));
    if (!campo) return;
    const icono = boton.querySelector(".bi");

    boton.addEventListener("click", () => {
      const mostrar = campo.type === "password";
      campo.type = mostrar ? "text" : "password";
      boton.setAttribute("aria-pressed", String(mostrar));
      boton.setAttribute("aria-label", mostrar ? "Ocultar contraseña" : "Mostrar contraseña");
      if (icono) {
        icono.classList.toggle("bi-eye", !mostrar);
        icono.classList.toggle("bi-eye-slash", mostrar);
      }
      campo.focus();
    });
  });

  // 2. Requisitos de contraseña (mismas reglas que el servidor)
  const lista = document.querySelector("[data-requisitos]");
  if (!lista) return;
  const password = document.getElementById(lista.dataset.requisitos);
  const repetir = document.getElementById(lista.dataset.repetir || "");
  if (!password) return;

  const reglas = {
    largo: (v) => [...v].length >= 8 && new TextEncoder().encode(v).length <= 72,
    letra: (v) => /[A-Za-z]/.test(v),
    numero: (v) => /\d/.test(v),
    coincide: (v) => v !== "" && repetir !== null && v === repetir.value,
  };

  const actualizar = () => {
    lista.querySelectorAll("[data-regla]").forEach((item) => {
      const regla = reglas[item.dataset.regla];
      const ok = regla ? regla(password.value) : false;
      item.classList.toggle("is-ok", ok);
      const estado = item.querySelector(".visually-hidden");
      if (estado) estado.textContent = ok ? " (cumplido)" : " (pendiente)";
    });
  };

  password.addEventListener("input", actualizar);
  if (repetir) repetir.addEventListener("input", actualizar);
  actualizar();
});
