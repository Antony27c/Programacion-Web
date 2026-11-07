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
  const urlDetalle = form.dataset.detalle;
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

  // Ícono según el nombre del servicio (misma tabla que Servicio::icono() en PHP)
  const ICONOS = [
    ["fren", "frenos"], ["suspens", "suspension"], ["amortig", "suspension"],
    ["alinea", "alineacion"], ["diagn", "diagnostico"], ["escan", "diagnostico"],
    ["bater", "bateria"], ["electr", "bateria"], ["aire", "aire"],
    ["neum", "neumaticos"], ["cubiert", "neumaticos"], ["service", "aceite"], ["aceite", "aceite"],
  ];
  function icono(nombre) {
    const texto = String(nombre).toLowerCase();
    const par = ICONOS.find(([clave]) => texto.includes(clave));
    return par ? par[1] : "presupuesto";
  }

  // Reemplaza el contenido del área aria-live por una alerta (o la vacía).
  function mostrarEstado(tipo, texto) {
    if (!tipo) {
      estado.replaceChildren();
      return;
    }
    estado.replaceChildren(crear("div", `alert alert-${tipo}`, texto));
  }

  // Link con texto oculto para lectores de pantalla ("Ver detalle de Frenos")
  function crearBoton(clases, href, texto, oculto, claseIcono) {
    const boton = crear("a", clases);
    boton.href = href;
    if (claseIcono) {
      const i = crear("i", `bi ${claseIcono}`);
      i.setAttribute("aria-hidden", "true");
      boton.append(i, " ");
    }
    boton.append(texto, crear("span", "visually-hidden", oculto));
    return boton;
  }

  function crearTarjeta(servicio) {
    const columna = crear("div", "col-md-6 col-lg-4");
    const tarjeta = crear("article", "bx-card bx-servicio");
    const valor = Number(servicio.precio_base);

    const arriba = crear("div", "bx-servicio-top");
    const caja = crear("span", "bx-card-icon");
    caja.setAttribute("aria-hidden", "true");
    caja.append(crear("span", `bx-icon bx-icon--${icono(servicio.nombre)}`));
    const etiquetaPrecio = crear("p", "bx-servicio-precio");
    if (valor > 0) {
      etiquetaPrecio.append(crear("small", "", "Desde"), " ");
    }
    etiquetaPrecio.append(precio(valor));
    arriba.append(caja, etiquetaPrecio);

    const acciones = crear("div", "bx-servicio-acciones");
    acciones.append(
      crearBoton("btn btn-bx-primary btn-sm", `${urlTurnos}?servicio=${encodeURIComponent(servicio.id)}`, "Sacar turno", ` para ${servicio.nombre}`, "bi-calendar-check"),
      crearBoton("btn btn-bx-outline-dark btn-sm", `${urlDetalle}?id=${encodeURIComponent(servicio.id)}`, "Ver detalle", ` de ${servicio.nombre}`)
    );

    tarjeta.append(arriba, crear("h2", "h4", servicio.nombre), crear("p", "", servicio.descripcion), acciones);
    columna.append(tarjeta);
    return columna;
  }

  // Estado vacío con ilustración y salida hacia el presupuesto
  function crearVacio(termino) {
    const caja = crear("div", "bx-empty");
    caja.setAttribute("role", "status");
    const img = crear("img");
    img.src = "img/ilustraciones/estado-vacio.svg";
    img.width = 480;
    img.height = 320;
    img.alt = "";
    const boton = crearBoton("btn btn-bx-primary", "presupuesto.php", "Pedir presupuesto", "", "bi-file-earmark-text");
    caja.append(
      img,
      crear("h2", "h4", `No encontramos servicios para «${termino}»`),
      crear("p", "", "Probá con otra palabra o contanos el problema y te pasamos un presupuesto."),
      boton
    );
    return caja;
  }

  function mostrarResultados(termino, servicios) {
    contenedor.replaceChildren(...servicios.map(crearTarjeta));
    if (termino === "") {
      mostrarEstado(null);
    } else if (servicios.length > 0) {
      const contador = crear("p", "bx-search-count", `${servicios.length} resultado(s) para «${termino}».`);
      contador.setAttribute("role", "status");
      estado.replaceChildren(contador);
    } else {
      estado.replaceChildren(crearVacio(termino));
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
