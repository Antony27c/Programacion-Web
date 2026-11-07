// ============================================================
// servicios.js — Búsqueda en vivo de servicios con Fetch API (TP9)
// Evento input (debounce) → fetch a api/buscar_servicios.php → JSON →
// se reemplazan solo el contador/alerta y el contenedor de resultados.
// Sin JS, el formulario GET de servicios.php sigue funcionando.
// ============================================================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-busqueda");
  if (!form || !window.fetch) {
    return;
  }

  const input = form.querySelector("#q");
  const limpiar = document.getElementById("limpiar-busqueda");
  const estado = document.getElementById("estado-busqueda");
  const contenedor = document.getElementById("resultados-servicios");
  const urlApi = new URL(form.dataset.api, window.location.href);
  const urlTurnos = form.dataset.turnos;
  const formatoPrecio = new Intl.NumberFormat("es-AR", { maximumFractionDigits: 0 });
  const ESPERA_MS = 250;

  let temporizador = null;
  let controlador = null;

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

  function precio(valor) {
    return valor > 0 ? `$ ${formatoPrecio.format(valor)}` : "A cotizar";
  }

  // Reemplaza el contenido del área aria-live por una alerta (o la vacía).
  function mostrarEstado(tipo, texto) {
    if (!tipo) {
      estado.replaceChildren();
      return;
    }
    estado.replaceChildren(crear("div", `alert alert-${tipo}`, texto));
  }

  function crearTarjeta(servicio) {
    const tarjeta = crear(
      "article",
      "bx-service-row d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
    );
    const cuerpo = crear("div");
    cuerpo.append(
      crear("h2", "h4 mb-1", servicio.nombre),
      crear("p", "mb-1 text-secondary", servicio.descripcion),
      crear("p", "mb-0 fw-semibold", precio(Number(servicio.precio_base)))
    );
    const boton = crear("a", "btn btn-bx-primary flex-shrink-0", "Pedir turno");
    boton.href = urlTurnos;
    tarjeta.append(cuerpo, boton);
    return tarjeta;
  }

  function mostrarResultados(termino, servicios) {
    contenedor.replaceChildren(...servicios.map(crearTarjeta));
    if (termino === "") {
      mostrarEstado(null);
    } else if (servicios.length > 0) {
      mostrarEstado("success", `${servicios.length} resultado(s) para «${termino}».`);
    } else {
      mostrarEstado("danger", `No encontramos servicios para «${termino}». Probá con otra palabra.`);
    }
  }

  // Mantiene la URL compartible (?q=) sin recargar ni sumar entradas al historial.
  function actualizarUrl(termino) {
    const url = new URL(window.location.href);
    if (termino) {
      url.searchParams.set("q", termino);
    } else {
      url.searchParams.delete("q");
    }
    window.history.replaceState(null, "", url);
    limpiar.hidden = termino === "";
  }

  async function buscar(termino) {
    // Cancela la petición anterior: solo cuenta la respuesta de la última tecla
    if (controlador) {
      controlador.abort();
    }
    controlador = new AbortController();
    const { signal } = controlador;

    mostrarEstado("info", "Buscando…");
    contenedor.setAttribute("aria-busy", "true");

    try {
      urlApi.searchParams.set("q", termino);
      const respuesta = await fetch(urlApi, {
        headers: { Accept: "application/json" },
        signal,
      });
      const json = await respuesta.json().catch(() => null);

      if (!respuesta.ok || !json || json.status !== "success") {
        throw new Error(json?.message || `Error ${respuesta.status} del servidor.`);
      }

      mostrarResultados(termino, json.data);
      actualizarUrl(termino);
    } catch (error) {
      if (error.name === "AbortError") {
        return;
      }
      // TypeError = sin conexión / servidor caído; el resto trae el mensaje de la API
      const mensaje =
        error instanceof TypeError
          ? "No pudimos conectar con el servidor. Revisá tu conexión e intentá de nuevo."
          : error.message;
      mostrarEstado("danger", mensaje);
    } finally {
      if (!signal.aborted) {
        contenedor.removeAttribute("aria-busy");
      }
    }
  }

  input.addEventListener("input", () => {
    clearTimeout(temporizador);
    temporizador = setTimeout(() => buscar(input.value.trim()), ESPERA_MS);
  });

  form.addEventListener("submit", (evento) => {
    evento.preventDefault();
    clearTimeout(temporizador);
    buscar(input.value.trim());
  });

  limpiar.addEventListener("click", (evento) => {
    evento.preventDefault();
    input.value = "";
    clearTimeout(temporizador);
    buscar("");
    input.focus();
  });
});
