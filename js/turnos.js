// ============================================================
// turnos.js — Consulta del estado de un turno con Fetch API (TP9)
// Evento submit (preventDefault) → fetch a api/consultar_turno.php →
// JSON → se reemplaza solo el bloque de resultado, sin recargar.
// Sin JS, el formulario GET se resuelve en turnos.php.
// ============================================================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-consulta-turno");
  if (!form || !window.fetch) {
    return;
  }

  const inputId = form.querySelector("#consulta_id");
  const inputPatente = form.querySelector("#consulta_patente");
  const boton = form.querySelector("button[type=submit]");
  const resultado = document.getElementById("resultado-consulta");
  const urlApi = new URL(form.dataset.api, window.location.href);
  const PATENTE = /^([A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2})$/;

  // Crea un elemento con clases y texto. textContent nunca interpreta HTML (anti XSS).
  function crear(etiqueta, clases = "", texto = "") {
    const elemento = document.createElement(etiqueta);
    if (clases) {
      elemento.className = clases;
    }
    if (texto) {
      elemento.textContent = texto;
    }
    return elemento;
  }

  function mostrarAlerta(tipo, texto) {
    const alerta = crear("div", `alert alert-${tipo} mb-0`, texto);
    if (tipo === "danger") {
      alerta.setAttribute("role", "alert");
    }
    resultado.replaceChildren(alerta);
  }

  // "2026-11-20" → "20/11/2026" sin pasar por Date (evita corrimientos de zona horaria)
  function fecha(iso) {
    const [anio, mes, dia] = String(iso).split("-");
    return dia && mes && anio ? `${dia}/${mes}/${anio}` : String(iso);
  }

  function mostrarTurno(turno) {
    const tarjeta = crear("div", "alert alert-info mb-0");
    const titulo = crear("p", "fw-semibold mb-2", `Turno N° ${turno.id}: `);
    titulo.append(crear("span", "badge text-bg-dark", turno.estado));

    const lista = crear("dl", "row small mb-0");
    const filas = [
      ["Vehículo", `${turno.vehiculo} (${turno.patente})`],
      ["Fecha", `${fecha(turno.fecha)} (${turno.horario})`],
      ["Servicios", turno.servicios.length ? turno.servicios.join(", ") : "—"],
    ];
    filas.forEach(([etiqueta, valor], i) => {
      const ultima = i === filas.length - 1;
      lista.append(crear("dt", "col-5", etiqueta), crear("dd", ultima ? "col-7 mb-0" : "col-7", valor));
    });

    tarjeta.append(titulo, lista);
    resultado.replaceChildren(tarjeta);
  }

  // Validación en el cliente (la API vuelve a validar todo)
  function validar(id, patente) {
    inputId.classList.toggle("is-invalid", !/^[0-9]+$/.test(id) || Number(id) <= 0);
    inputPatente.classList.toggle("is-invalid", !PATENTE.test(patente));
    if (inputId.classList.contains("is-invalid")) {
      return "Ingresá un número de turno válido (entero positivo).";
    }
    if (inputPatente.classList.contains("is-invalid")) {
      return "Ingresá una patente válida (ABC123 o AB123CD).";
    }
    return null;
  }

  form.addEventListener("submit", async (evento) => {
    evento.preventDefault();

    const id = inputId.value.trim();
    const patente = inputPatente.value.replace(/[\s.-]+/g, "").toUpperCase();
    const error = validar(id, patente);
    if (error) {
      mostrarAlerta("danger", error);
      return;
    }

    boton.disabled = true;
    mostrarAlerta("secondary", "Consultando…");

    try {
      urlApi.searchParams.set("id", id);
      urlApi.searchParams.set("patente", patente);
      const respuesta = await fetch(urlApi, { headers: { Accept: "application/json" } });
      const json = await respuesta.json().catch(() => null);

      if (!respuesta.ok || !json || json.status !== "success") {
        throw new Error(json?.message || `Error ${respuesta.status} del servidor.`);
      }
      mostrarTurno(json.data);
    } catch (error) {
      // TypeError = sin conexión / servidor caído; el resto trae el mensaje de la API
      const mensaje =
        error instanceof TypeError
          ? "No pudimos conectar con el servidor. Revisá tu conexión e intentá de nuevo."
          : error.message;
      mostrarAlerta("danger", mensaje);
    } finally {
      boton.disabled = false;
    }
  });
});
