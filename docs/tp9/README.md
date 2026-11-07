# TP9 — Comunicación asincrónica y APIs (AJAX & JSON)

## Objetivo

Según el enunciado, el objetivo del TP9 es implementar comunicación asincrónica cliente-servidor
(AJAX) con **Fetch API** y **JSON**, para consultar y actualizar datos de la base MySQL sin recargar
la página. Lo integré al sitio del taller BOXLY en tres lugares:

| Página | Interacción | Endpoint |
|--------|-------------|----------|
| `servicios.php` | Buscador en vivo de servicios (evento `input`) | `GET api/buscar_servicios.php?q=` |
| `turnos.php` | Panel «Consultá tu turno» (evento `submit` con `preventDefault()`) | `GET api/consultar_turno.php?id=&patente=` |
| `admin/index.php` | Cambio de estado de un turno desde la tabla (evento `change`) | `POST api/turno_estado.php` |

En los tres casos apliqué **mejora progresiva**: sin JavaScript el sitio sigue funcionando como en
los TPs anteriores (formulario GET del buscador, consulta de turno resuelta en el servidor y edición
del estado desde «Editar»).

## Archivos principales

```
├── api/
│   ├── buscar_servicios.php     ← GET público: catálogo filtrado por nombre/descripción
│   ├── consultar_turno.php      ← GET público: estado de un turno (número + patente)
│   └── turno_estado.php         ← POST JSON: cambia el estado (solo Administrador + CSRF)
├── app/
│   ├── Models/Servicio.php      ← buscar() con LIKE preparado, errorBusqueda(), precio(), BUSQUEDA_MAX
│   ├── Models/Turno.php         ← (TP9) consultarEstado(), cambiarEstado(), normalizarPatente()
│   ├── Models/Catalogo.php      ← (TP9) nombreEstado()
│   ├── Controllers/             ← (TP9) SolicitudTurnoController: consulta sin JS; TurnoController::index: JS del panel
│   └── Views/turnos/            ← (TP9) panel «Consultá tu turno» en solicitar.php; select de estado en index.php
├── includes/
│   ├── api.php                  ← helpers comunes: responderJson(), errorJson(), exigirMetodo(),
│   │                              exigirRolApi(), leerCuerpoJson(), enteroPositivo(); carga app/bootstrap.php
│   └── footer.php               ← (TP9) carga el JS propio de cada página ($scripts)
├── js/
│   ├── servicios.js             ← búsqueda en vivo (input + debounce + AbortController)
│   ├── turnos.js                ← consulta de estado (submit + preventDefault)
│   └── admin-turnos.js          ← cambio de estado en la tabla del panel (change + POST JSON)
├── servicios.php             ← página del buscador (usa el modelo Servicio)
├── turnos.php  admin/index.php  ← front controllers MVC (vistas en app/Views/turnos/)
└── docs/tp9/
    ├── README.md                ← este documento
    └── probar_api.sh            ← pruebas automáticas con curl
```

## Backend: endpoints JSON

Cada endpoint es un archivo PHP independiente dentro de `api/`. Todos responden **siempre JSON**
(nunca HTML ni redirecciones) a través de `responderJson()` en `includes/api.php`, que:

- envía `Content-Type: application/json; charset=utf-8` (más `X-Content-Type-Options: nosniff` y
  `Cache-Control: no-store`),
- fija el código HTTP con `http_response_code()`,
- convierte el arreglo PHP con `json_encode()` (`JSON_UNESCAPED_UNICODE` para que las tildes
  viajen legibles).

Formato uniforme de las respuestas:

```json
{ "status": "success", "total": 1, "data": [ ... ] }
{ "status": "error", "message": "Texto para mostrar al usuario." }
```

Todas las consultas usan **PDO con `prepare()` + `execute()`** y viven en los modelos de `app/Models/`
(arquitectura MVC del TP10): los endpoints solo validan la entrada, llaman al modelo y arman la respuesta. Los errores de base se capturan con
`try/catch (PDOException)`: el detalle técnico va a `error_log()` y al cliente solo le llega un 500
con un mensaje genérico.

### `GET api/buscar_servicios.php?q=texto`

Busca en `servicios.nombre` y `servicios.descripcion` con `LIKE ... ESCAPE '!'`. Los comodines `%` y
`_` que escribe el usuario se escapan, así que se buscan como texto literal. Sin `q` (o vacío)
devuelve todo el catálogo.

| Código | Cuándo |
|--------|--------|
| 200 | Búsqueda correcta (con resultados o con `data: []`) |
| 400 | `q` no es texto (`?q[]=x`), supera 60 caracteres o no es UTF-8 válido |
| 405 | Método distinto de GET (con cabecera `Allow: GET`) |
| 500 | Error de base de datos |

### `GET api/consultar_turno.php?id=N&patente=ABC123`

Pensado para el cliente que pidió un turno: con el número de solicitud y la patente ve el estado,
la fecha, el vehículo y los servicios. La patente se normaliza (`ac-123-bd` → `AC123BD`) y se valida
con el mismo formato que el resto del sitio. Pedir las dos cosas evita que alguien recorra turnos
ajenos solo cambiando el número, y el 404 usa el mismo mensaje si el turno no existe o si la patente
no coincide, para no revelar cuál de los dos falló.

| Código | Cuándo |
|--------|--------|
| 200 | Turno encontrado |
| 400 | `id` no es entero positivo o patente con formato inválido |
| 404 | No hay un turno con ese número y esa patente |
| 405 | Método distinto de GET |
| 500 | Error de base de datos |

### `POST api/turno_estado.php`

Cuerpo JSON `{"id": 1, "estado_id": 2}` y cabecera `X-CSRF-Token` con el token de la sesión (el panel
lo expone en `data-csrf` de la tabla). Reutiliza la sesión del TP8: solo el rol *Administrador*.

| Código | Cuándo |
|--------|--------|
| 200 | Estado actualizado: `{"status":"success","data":{"id":1,"estado_id":2,"estado":"Confirmado"}}` |
| 400 | JSON mal formado, no es un objeto, `id`/`estado_id` no son enteros positivos, o el estado no existe |
| 401 | Sin sesión iniciada |
| 403 | Usuario sin rol Administrador, o token CSRF ausente/incorrecto |
| 404 | El turno no existe |
| 405 | Método distinto de POST (con `Allow: POST`) |
| 415 | El cuerpo no viene como `application/json` |
| 500 | Error de base de datos |

## Frontend: Fetch API

Los tres scripts siguen el mismo esquema:

1. **Captura del evento** dentro de `DOMContentLoaded`.
2. **Petición con `fetch` y `async/await`**, pidiendo `Accept: application/json`.
3. **Manejo de errores con `try...catch`**: si `response.ok` es falso o el JSON no trae
   `status: "success"`, lanzo un `Error` con el `message` de la API. Un `TypeError` lo interpreto
   como falla de red y muestro «No pudimos conectar con el servidor».
4. **Actualización del DOM** solo en la sección afectada, sin recargar.

```js
const respuesta = await fetch(urlApi, { headers: { Accept: "application/json" }, signal });
const json = await respuesta.json().catch(() => null);
if (!respuesta.ok || !json || json.status !== "success") {
  throw new Error(json?.message || `Error ${respuesta.status} del servidor.`);
}
mostrarResultados(termino, json.data);
```

| Script | Evento | Qué actualiza |
|--------|--------|---------------|
| `js/servicios.js` | `input` con espera de 250 ms (y `submit` con `preventDefault()`) | Recorre `json.data` con `map()` y arma una tarjeta `.bx-service-row` por servicio; muestra el contador o «sin resultados» en un área `aria-live`; actualiza `?q=` en la URL con `history.replaceState()` |
| `js/turnos.js` | `submit` con `preventDefault()` | Valida número y patente en el cliente, deshabilita el botón mientras consulta y arma la ficha del turno (badge de estado + lista `<dl>`) |
| `js/admin-turnos.js` | `change` del `<select>` de estado | Envía el POST JSON con el token CSRF; si sale bien cambia el color del select y muestra un *toast* de Bootstrap; si falla vuelve al estado anterior y muestra el error |

### Decisiones tomadas

- **Nada de `innerHTML` con datos del servidor.** Construyo los elementos con
  `document.createElement()` y les asigno el texto con `textContent`, así un nombre de servicio con
  `<script>` se muestra como texto y no se ejecuta (anti XSS).
- **Búsqueda sin carreras:** `servicios.js` cancela la petición anterior con `AbortController`, así
  solo se pinta la respuesta de la última tecla, y espera 250 ms antes de pedir para no mandar una
  petición por cada letra.
- **Misma validación en servidor y API:** `servicios.php` y `api/buscar_servicios.php` usan
  `Servicio::errorBusqueda()`; `turnos.php` (vía `SolicitudTurnoController`) y `api/consultar_turno.php`
  usan `Turno::normalizarPatente()` y `Turno::consultarEstado()`. El resultado sin JS y con JS es el mismo (por ejemplo, `?q[]=x` da el
  mismo error en la página y en la API).
- **El catálogo de servicios ahora sale de la tabla `servicios`** del TP6 (antes era un arreglo fijo
  en `servicios.php`), con su precio base.
- **CSRF en cabecera propia:** el POST de `turno_estado.php` exige `X-CSRF-Token`. Un formulario de
  otro sitio no puede enviar esa cabecera, y además el endpoint solo acepta `application/json`.
- **Mejora progresiva en el panel:** el `<select>` de estado viene con `hidden` y el badge de solo
  lectura visible; `admin-turnos.js` los intercambia al cargar.
- **Scripts por página:** cada página declara `$scripts = ['js/...']` (en las vistas MVC lo pasa el
  controlador a `render()`) y `includes/footer.php` los carga después de Bootstrap y `main.js`.
- **Conexión fallida también en JSON:** los modelos toman el PDO de `App\Core\Database::conexion()`,
  que incluye `config/conexion.php`; si la
  conexión falla y existe `responderJson()` (es decir, la pidió un endpoint), responde 500 en JSON
  en lugar del mensaje de texto plano que ve el resto del sitio.

## Cómo correrlo

Con la base `boxly` creada (ver «Cómo correrlo en local» en el `README.md` principal):

```bash
php -S localhost:8099
```

- Buscador en vivo: http://localhost:8099/servicios.php (escribir «frenos», «aceite», etc.).
- Consulta de turno: http://localhost:8099/turnos.php → panel «Consultá tu turno»
  (por ejemplo N° `1` y patente `AC123BD` con los datos de `tp6/consultas.sql`).
- Panel: http://localhost:8099/admin/ con `admin@boxly.com.ar` / `Boxly2026!` → cambiar el estado
  desde la columna «Estado».

## Pruebas realizadas

### Script automático

`docs/tp9/probar_api.sh` prueba con `curl` cada caso de `buscar_servicios.php` y `turno_estado.php`
(incluido el login de administrador para obtener la cookie y el token CSRF). El caso 200 de
`turno_estado.php` reenvía el mismo estado que ya tiene el turno, así no altera los datos.

```bash
bash docs/tp9/probar_api.sh 8099
# ...
# == Resultado: 27 OK, 0 FALLA(S) ==
```

### Pruebas manuales con curl

```bash
curl -i "http://localhost:8099/api/buscar_servicios.php?q=freno"
# HTTP/1.1 200 OK
# Content-Type: application/json; charset=utf-8
# {"status":"success","total":1,"data":[{"id":2,"nombre":"Frenos","descripcion":"Revisión y cambio de pastillas, discos y líquido de freno.","precio_base":60000}]}

curl -i "http://localhost:8099/api/buscar_servicios.php?q[]=x"
# HTTP/1.1 400 Bad Request
# {"status":"error","message":"El parámetro q debe ser un texto."}

curl -i "http://localhost:8099/api/consultar_turno.php?id=1&patente=ac-123-bd"
# HTTP/1.1 200 OK
# {"status":"success","data":{"id":1,"fecha":"2026-11-10","horario":"Mañana","estado":"Confirmado","vehiculo":"Volkswagen Gol","patente":"AC123BD","servicios":["Service"]}}

curl -i "http://localhost:8099/api/consultar_turno.php?id=abc&patente=ABC123"
# HTTP/1.1 400 Bad Request
# {"status":"error","message":"Ingresá un número de turno válido (entero positivo)."}

curl -i "http://localhost:8099/api/consultar_turno.php?id=999999&patente=ABC123"
# HTTP/1.1 404 Not Found
# {"status":"error","message":"No encontramos el turno N° 999999 para la patente ABC123."}

curl -i -X POST "http://localhost:8099/api/buscar_servicios.php"
# HTTP/1.1 405 Method Not Allowed

curl -i -X POST -H "Content-Type: application/json" -d '{"id":1,"estado_id":2}' \
  "http://localhost:8099/api/turno_estado.php"
# HTTP/1.1 401 Unauthorized
# {"status":"error","message":"Necesitás iniciar sesión para realizar esta acción."}
```

Con sesión de administrador y el token del panel:

| Cuerpo enviado | Respuesta |
|----------------|-----------|
| `{"id":1,"estado_id":2}` | 200 `{"status":"success","data":{"id":1,"estado_id":2,"estado":"Confirmado"}}` |
| `{"id":1,"estado_id":"x"}` | 400 `Los campos id y estado_id deben ser enteros positivos.` |
| `{"id":1,"estado_id":999}` | 400 `El estado indicado no existe.` |
| `{"id":999999,"estado_id":2}` | 404 `El turno #999999 no existe.` |
| `{id` | 400 `El cuerpo de la petición no es un JSON válido.` |
| sin `X-CSRF-Token` | 403 `Token de seguridad inválido. Recargá la página e intentá de nuevo.` |

También probé el 500 apuntando la conexión a un puerto sin MySQL: `buscar_servicios.php` respondió
`500` con `{"status":"error","message":"Error de conexión con la base de datos. Intentá de nuevo más tarde."}`.

### Verificaciones de sintaxis

```bash
php -l api/buscar_servicios.php      # No syntax errors detected (ídem resto de PHP tocados)
node --check js/servicios.js         # sin errores
node --check js/turnos.js
node --check js/admin-turnos.js
```
