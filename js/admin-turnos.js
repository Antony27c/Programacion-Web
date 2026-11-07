// ============================================================
// admin-turnos.js — Panel de turnos
// TP9: cambio de estado con Fetch API. Evento change del <select> →
// fetch POST (JSON + X-CSRF-Token) a api/turno_estado.php → se
// actualiza solo esa fila (y los contadores), sin recargar.
// Sin JS queda el badge de solo lectura y se edita desde "Editar".
// Rediseño: tooltips de las acciones, confirmación de baja en un
// modal de Bootstrap (el formulario sigue siendo POST + CSRF) y
// avisos (toasts) con el estilo del sitio.
// ============================================================
document.addEventListener("DOMContentLoaded", () => {
  const bs = window.bootstrap;

  // Tooltips de los botones de acción (solo se ven en escritorio, donde el botón es un ícono)
  if (bs?.Tooltip) {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bs.Tooltip(el));
  }

  iniciarBaja();

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

  // Suma (o resta) uno en las tarjetas resumen y en las píldoras del estado indicado
  function ajustarConteo(estadoId, delta) {
    document.querySelectorAll(`[data-conteo="${estadoId}"]`).forEach((el) => {
      el.textContent = String(Math.max(0, Number(el.textContent) + delta));
    });
  }

  // Aviso tipo toast de Bootstrap. El texto va con textContent (anti XSS).
  function avisar(tipo, texto) {
    if (!avisos) {
      return;
    }
    const toast = document.createElement("div");
    toast.className = `toast bx-toast bx-toast--${tipo}`;
    toast.setAttribute("role", tipo === "danger" ? "alert" : "status");

    const fila = document.createElement("div");
    fila.className = "d-flex align-items-start";
    const cuerpo = document.createElement("div");
    cuerpo.className = "toast-body flex-grow-1";
    const icono = document.createElement("i");
    icono.className = `bi ${tipo === "danger" ? "bi-exclamation-octagon" : "bi-check-circle"}`;
    icono.setAttribute("aria-hidden", "true");
    const mensaje = document.createElement("span");
    mensaje.textContent = texto;
    cuerpo.append(icono, mensaje);
    const cerrar = document.createElement("button");
    cerrar.type = "button";
    cerrar.className = "btn-close me-2 mt-2";
    cerrar.setAttribute("data-bs-dismiss", "toast");
    cerrar.setAttribute("aria-label", "Cerrar aviso");
    fila.append(cuerpo, cerrar);
    toast.append(fila);
    avisos.append(toast);

    if (bs?.Toast) {
      toast.addEventListener("hidden.bs.toast", () => toast.remove());
      new bs.Toast(toast, { delay: 4500 }).show();
    } else {
      toast.classList.add("show");
      setTimeout(() => toast.remove(), 4500);
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

      const nuevo = String(json.data.estado_id);
      if (nuevo !== anterior) {
        ajustarConteo(anterior, -1);
        ajustarConteo(nuevo, 1);
      }
      select.dataset.actual = nuevo;
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

  // Confirmación de baja: el submit de cada formulario abre el modal; al confirmar
  // se envía ese mismo formulario (POST con id + token CSRF). Sin Bootstrap cae a confirm().
  function iniciarBaja() {
    const modalEl = document.getElementById("modal-baja");
    const confirmar = document.getElementById("confirmar-baja");
    const modal = modalEl && bs?.Modal ? bs.Modal.getOrCreateInstance(modalEl) : null;
    let pendiente = null;

    document.querySelectorAll(".bx-form-baja").forEach((form) => {
      form.addEventListener("submit", (evento) => {
        if (form.dataset.confirmado === "1") {
          return;
        }
        evento.preventDefault();
        const { turno, cliente, detalle } = form.dataset;

        if (!modal) {
          if (window.confirm(`¿Eliminar el turno #${turno} de ${cliente}?`)) {
            form.dataset.confirmado = "1";
            form.submit();
          }
          return;
        }
        pendiente = form;
        modalEl.querySelector('[data-baja="turno"]').textContent = `#${turno}`;
        modalEl.querySelector('[data-baja="cliente"]').textContent = cliente;
        modalEl.querySelector('[data-baja="detalle"]').textContent = detalle;
        bs.Tooltip?.getInstance(form.querySelector('[data-bs-toggle="tooltip"]'))?.hide();
        modal.show();
      });
    });

    confirmar?.addEventListener("click", () => {
      if (!pendiente) {
        return;
      }
      confirmar.disabled = true;
      pendiente.dataset.confirmado = "1";
      pendiente.submit();
    });

    // Al cerrar sin confirmar el foco vuelve al botón que abrió el modal
    modalEl?.addEventListener("hidden.bs.modal", () => {
      pendiente?.querySelector("button[type=submit]")?.focus();
      pendiente = null;
    });
  }
});
