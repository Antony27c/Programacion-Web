// ============================================================
// admin-turnos.js — Cambio de estado de turnos con Fetch API (TP9)
// Evento change del <select> → fetch POST (JSON + X-CSRF-Token) a
// api/turno_estado.php → se actualiza solo esa fila, sin recargar.
// Sin JS queda el badge de solo lectura y se edita desde "Editar".
// ============================================================
document.addEventListener("DOMContentLoaded", () => {
  const tabla = document.getElementById("tabla-turnos");
  if (!tabla || !window.fetch) {
    return;
  }

  const urlApi = new URL(tabla.dataset.api, window.location.href);
  const csrf = tabla.dataset.csrf;
  const colores = JSON.parse(tabla.dataset.colores || "{}");
  const todasLasClases = [...new Set([...Object.values(colores), "text-bg-light"])];
  const avisos = document.getElementById("avisos-turnos");

  function colorDe(estado) {
    return colores[estado] || "text-bg-light";
  }

  function pintar(select, estado) {
    select.classList.remove(...todasLasClases);
    select.classList.add(colorDe(estado));
  }

  // Aviso tipo toast de Bootstrap. El texto va con textContent (anti XSS).
  function avisar(tipo, texto) {
    if (!avisos) {
      return;
    }
    const toast = document.createElement("div");
    toast.className = `toast align-items-center border-0 text-bg-${tipo}`;
    toast.setAttribute("role", tipo === "danger" ? "alert" : "status");

    const fila = document.createElement("div");
    fila.className = "d-flex";
    const cuerpo = document.createElement("div");
    cuerpo.className = "toast-body";
    cuerpo.textContent = texto;
    const cerrar = document.createElement("button");
    cerrar.type = "button";
    cerrar.className = "btn-close btn-close-white me-2 m-auto";
    cerrar.setAttribute("data-bs-dismiss", "toast");
    cerrar.setAttribute("aria-label", "Cerrar");
    fila.append(cuerpo, cerrar);
    toast.append(fila);
    avisos.append(toast);

    if (window.bootstrap?.Toast) {
      toast.addEventListener("hidden.bs.toast", () => toast.remove());
      new bootstrap.Toast(toast, { delay: 4000 }).show();
    } else {
      toast.classList.add("show");
      setTimeout(() => toast.remove(), 4000);
    }
  }

  async function cambiarEstado(select) {
    const anterior = select.dataset.actual;
    const nombreNuevo = select.selectedOptions[0]?.textContent ?? "";
    select.disabled = true;

    try {
      const respuesta = await fetch(urlApi, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-Token": csrf,
        },
        body: JSON.stringify({ id: Number(select.dataset.id), estado_id: Number(select.value) }),
      });
      const json = await respuesta.json().catch(() => null);

      if (!respuesta.ok || !json || json.status !== "success") {
        throw new Error(json?.message || `Error ${respuesta.status} del servidor.`);
      }

      select.dataset.actual = String(json.data.estado_id);
      pintar(select, json.data.estado);
      avisar("success", `Turno #${json.data.id}: estado cambiado a «${json.data.estado}».`);
    } catch (error) {
      // Se vuelve al valor anterior para que la tabla refleje lo que hay en la base
      select.value = anterior;
      pintar(select, select.selectedOptions[0]?.textContent ?? "");
      const mensaje =
        error instanceof TypeError
          ? "No pudimos conectar con el servidor. Revisá tu conexión e intentá de nuevo."
          : error.message;
      avisar("danger", `No se pudo cambiar a «${nombreNuevo}». ${mensaje}`);
    } finally {
      select.disabled = false;
    }
  }

  // Mejora progresiva: con JS se oculta el badge y se muestra el select
  tabla.querySelectorAll(".bx-estado-select").forEach((select) => {
    const badge = select.parentElement.querySelector(".bx-estado-badge");
    if (badge) {
      badge.hidden = true;
    }
    select.hidden = false;
    select.dataset.actual = select.value;
    select.addEventListener("change", () => cambiarEstado(select));
  });
});
