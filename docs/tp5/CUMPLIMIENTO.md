# TP5 — Cumplimiento: procesamiento de formularios y seguridad web

Auditoría del enunciado del TP5 contra el código del sitio BOXLY.

Estados: **OK** = ya cumplía · **ARREGLADO** = se corrigió en esta revisión · **FALTA** = pendiente.

## Tabla de requisitos

| # | Requisito del enunciado | Archivo(s) | Estado | Detalle |
|---|---|---|---|---|
| A1 | Formulario enviado por **POST** (contacto / alta) | `contacto.php`, `presupuesto.php`, `turnos.php` | OK | Los tres usan `method="post"` y procesan solo si `$_SERVER['REQUEST_METHOD'] === 'POST'`. |
| A2 | Formulario enviado por **GET** (búsqueda / filtro) | `servicios.php` (`?q=`), `admin/index.php` (`?estado=`) | OK | Buscador de servicios con `filter_input(INPUT_GET, 'q', ...)` y filtro en servidor con `mb_stripos()`. |
| B1 | Ninguna variable de `$_POST`/`$_GET` se imprime sin sanitizar | todas las vistas | OK | Toda salida pasa por `e()` = `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`; los valores re-impresos usan `valor()`. |
| B2 | Uso de `trim()` / `htmlspecialchars()` / `filter_input()` / `filter_var()` | `includes/funciones.php`, `servicios.php`, `contacto.php`, `presupuesto.php` | OK | `limpiar()` (trim), `e()` (htmlspecialchars), `filter_input()` en GET, `filter_var(FILTER_VALIDATE_EMAIL / FILTER_VALIDATE_INT)`. |
| B3 | Entradas manipuladas (campo enviado como array, ej. `nombre[]=x`) | `includes/funciones.php` → `limpiar()` | ARREGLADO | Antes: aviso *"Array to string conversion"* que mostraba la ruta del servidor y el campo se aceptaba con el valor `Array`. Ahora `limpiar()` descarta valores no escalares (devuelve `''`) y el campo falla como obligatorio. La firma no cambió. |
| B4 | Campos requeridos no vacíos (`empty()`/`isset()`) | `contacto.php`, `presupuesto.php`, `includes/funciones.php` (`validarTurno`) | OK | Cada campo obligatorio se controla tras `limpiar()` (`=== ''`), con mensaje propio. |
| B5 | Validación de formatos específicos (email, etc.) | `contacto.php`, `presupuesto.php`, `includes/funciones.php` | OK | Email con `FILTER_VALIDATE_EMAIL`; teléfono y km con regex; urgencia, horario, marcas y servicios contra listas blancas; fecha con `DateTime::createFromFormat`; año con `FILTER_VALIDATE_INT`. |
| B6 | Longitud máxima y codificación válida | `contacto.php`, `presupuesto.php` | ARREGLADO | Se agregaron topes (nombre 60, email 150, vehículo 80, mensaje/problema 2000) y `mb_check_encoding()`: con UTF-8 inválido `htmlspecialchars()` devolvía `''` y el campo se perdía sin aviso. `turnos.php` ya lo tenía en `validarTurno()`. |
| C1 | Mensajes claros de éxito / error | `contacto.php`, `presupuesto.php`, `turnos.php`, `servicios.php` | OK | `alert-success` / `alert-danger` generales + `is-invalid` / `invalid-feedback` por campo (`claseError()`, `errorCampo()`). |
| C2 | Persistencia de los datos ante errores | `contacto.php`, `presupuesto.php`, `turnos.php` | OK | `value="<?= valor($datos, ...) ?>"`, `seleccionado()` y `marcado()` para select/checkbox. |
| D1 | Capturas de pantalla | `docs/tp5/screenshots/` | OK | `post-errores.png`, `post-exito.png`, `get-busqueda.png`, `get-sin-resultados.png`, `xss-sanitizado.png`, enlazadas desde el README. |
| D2 | README con apartado de la clase 5 (formularios POST y GET + capturas) | `README.md` | OK | Sección "TP5 — Procesamiento de formularios y seguridad web (PHP)". |
| D3 | Carpeta denominada exactamente `clase5` | historial git | OK (entregado) | La entrega del TP5 se hizo en la carpeta `clase5/` (commit `abe0fde`). Luego, en el commit `5575793`, reorganicé el repositorio para tener un único sitio PHP en la raíz y la documentación en `docs/tp5/`; la versión entregada sigue disponible en ese commit. |

## Evidencia

### Sintaxis (`php -l`, PHP 8.2)

```
No syntax errors detected in contacto.php
No syntax errors detected in presupuesto.php
No syntax errors detected in includes/funciones.php
No syntax errors detected in turnos.php
No syntax errors detected in servicios.php
```

### XSS por POST — `php -S localhost:8097` + curl

```
$ curl -s -X POST --data-urlencode 'nombre=<script>alert(1)</script>' \
       --data-urlencode 'email=a@b.com' \
       --data-urlencode 'mensaje=<script>alert(1)</script> hola' \
       http://localhost:8097/contacto.php | grep 'alert(1)'
<strong>Mensaje enviado.</strong> Gracias &lt;script&gt;alert(1)&lt;/script&gt;, te responderemos a <strong>a@b.com</strong> a la brevedad.
<input class="form-control" id="nombre" name="nombre" ... value="&lt;script&gt;alert(1)&lt;/script&gt;" />
<textarea class="form-control" id="mensaje" name="mensaje" ...>&lt;script&gt;alert(1)&lt;/script&gt; hola</textarea>
```

Intento de romper el atributo `value` (caso con error de validación):

```
$ curl -s -X POST --data-urlencode 'email="><script>alert(1)</script>' ... http://localhost:8097/contacto.php
<input class="form-control is-invalid" id="email" ... value="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;" />
<div class="invalid-feedback">El formato del email no es válido.</div>
```

`presupuesto.php` (con `urgencia` fuera de la lista blanca):

```
<strong>Revisá el formulario:</strong> encontramos 2 error(es) de validación.
<input ... id="nombre" ... value="&lt;script&gt;alert(1)&lt;/script&gt;" />
<div class="invalid-feedback">Ingresá un email válido.</div>
<div class="invalid-feedback">Seleccioná una urgencia válida.</div>
<textarea ... id="problema" ...>&lt;script&gt;alert(1)&lt;/script&gt;</textarea>
```

### XSS por GET

```
$ curl -s -G --data-urlencode 'q=<script>alert(1)</script>' http://localhost:8097/servicios.php | grep 'alert(1)'
<input class="form-control" id="q" name="q" type="search" ... value="&lt;script&gt;alert(1)&lt;/script&gt;" />
No encontramos servicios para «&lt;script&gt;alert(1)&lt;/script&gt;». Probá con otra palabra.
```

### Conteo de `<script>alert(1)` sin escapar en la respuesta

```
contacto.php (POST)    → 0
presupuesto.php (POST) → 0
servicios.php (GET)    → 0
turnos.php (POST)      → 0
```

### Correcciones de esta revisión

```
# Campo como array — antes
<b>Warning</b>:  Array to string conversion in <b>...\includes\funciones.php</b> on line <b>13</b>
<strong>Mensaje enviado.</strong> Gracias Array, ...

# Campo como array — después
<div class="alert alert-danger" role="alert">
<div class="invalid-feedback">Ingresá tu nombre.</div>

# Nombre de 70 caracteres
<div class="invalid-feedback">No puede superar los 60 caracteres.</div>

# Byte UTF-8 inválido (nombre=ab%FFcd)
<div class="invalid-feedback">Contiene caracteres no válidos.</div>
```

### Persistencia ante errores

```
$ curl -s -X POST --data-urlencode 'nombre=Juan Perez' --data-urlencode 'email=malo' \
       --data-urlencode 'mensaje=Hola, quiero consultar' http://localhost:8097/contacto.php
<input ... id="nombre" ... value="Juan Perez" />
<input class="form-control is-invalid" id="email" ... value="malo" />
<div class="invalid-feedback">El formato del email no es válido.</div>
<textarea ... id="mensaje" ...>Hola, quiero consultar</textarea>
```

### Regresión de otras páginas que usan `limpiar()`

`turnos.php`, `login.php`, `registro.php`, `servicios.php` e `index.php` responden HTTP 200;
`admin/index.php?estado=...` sin sesión redirige (302) como antes.
