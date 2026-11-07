# TP9 — Pruebas de la API (resultados reales)

Pruebas realizadas con `docs/tp9/probar_api.sh`, un script de curl que ejercita
los tres endpoints JSON del TP9 y verifica código HTTP esperado vs. obtenido y
`Content-Type` de cada respuesta.

## Entorno

- Servidor: `php -S localhost:8091` (PHP 8.2.12, `C:/xampp/php/php.exe`)
- Base de datos: MySQL 8 local, base `boxly`
- El worktree se probó sobre una copia temporal fuera del repo porque
  `config/conexion.php` estaba bloqueado por otro proceso; el archivo se
  restauró desde `git show HEAD:config/conexion.php` solo en la copia.
- Login de administrador usado por el script: `admin@boxly.com.ar`
  (la contraseña se puede pasar con `ADMIN_PASS`, el CSRF se obtiene del panel).

## Cómo reproducir

```bash
# 1) Levantar el servidor desde la raíz del proyecto
C:/xampp/php/php.exe -S localhost:8091

# 2) En otra terminal, correr la suite (puerto por defecto 8091)
bash docs/tp9/probar_api.sh
# o con otro puerto / credenciales:
PUERTO=8091 ADMIN_EMAIL=admin@boxly.com.ar ADMIN_PASS=*** bash docs/tp9/probar_api.sh 8091
```

## Resultados

**42 casos OK, 0 fallas.** Todos los endpoints respondieron JSON
(`Content-Type: application/json; charset=utf-8`) con el código esperado,
salvo el 404 de ruta inexistente que es el HTML propio de `php -S`.

### api/buscar_servicios.php (GET público) — 12/12 OK

| Caso | Esperado | Obtenido |
|---|---|---|
| Sin `q` (lista todos) | 200 | 200 — `total: 4` servicios |
| `?q=frenos` | 200 | 200 — 1 resultado (Frenos, $ 60.000) |
| `?q=diagnóstico` (tilde, UTF-8) | 200 | 200 — 1 resultado |
| `?q=zzzzzz` (sin resultados) | 200 | 200 — `data: []` |
| `?q=%` (comodín como texto literal) | 200 | 200 — `data: []` |
| `?q=` solo espacios | 200 | 200 — lista completa |
| `?q[]=x` (parámetro arreglo) | 400 | 400 — "El parámetro q debe ser un texto." |
| `?q=` + 61 caracteres | 400 | 400 — supera los 60 caracteres |
| `?q=%FF` (UTF-8 inválido) | 400 | 400 — caracteres no válidos |
| POST / DELETE | 405 | 405 — "Método no permitido. Usá GET." |
| `/api/no_existe.php` | 404 | 404 (HTML de `php -S`, no es de la API) |

### api/consultar_turno.php (GET público, id + patente) — 15/15 OK

Casos de validación (sin sesión):

| Caso | Esperado | Obtenido |
|---|---|---|
| Sin parámetros / solo `id` / solo `patente` | 400 | 400 |
| `id=abc`, `id=1.5`, `id=0`, `id=-3` | 400 | 400 — entero positivo |
| `id[]=1` (arreglo) | 400 | 400 — "deben ser texto" |
| `patente=123`, `patente=AB` | 400 | 400 — formato inválido |
| POST | 405 | 405 |
| `id=999999` (inexistente) | 404 | 404 — "No encontramos el turno…" |

Casos con datos reales (id y patente tomados de la primera fila del panel
admin, turno `id=5`, patente `MNO654`):

| Caso | Esperado | Obtenido |
|---|---|---|
| `?id=5&patente=MNO654` | 200 | 200 — estado "Cancelado", Renault Sandero |
| `?id=5&patente=mno654` (minúsculas) | 200 | 200 — la patente se normaliza |
| `?id=5&patente=ZNO654` (letra cambiada) | 404 | 404 — mismo mensaje, no revela qué falló |

### api/turno_estado.php (POST JSON, solo Administrador) — 15/15 OK

Sin sesión:

| Caso | Esperado | Obtenido |
|---|---|---|
| GET | 405 | 405 |
| POST sin sesión | 401 | 401 — "Necesitás iniciar sesión…" |

Con sesión de administrador + `X-CSRF-Token` (leído de `data-csrf` en
`admin/index.php`):

| Caso | Esperado | Obtenido |
|---|---|---|
| `{id:5, estado_id:4}` (mismo estado actual) | 200 | 200 — no altera datos |
| `id=999999` | 404 | 404 — "El turno #999999 no existe." |
| `estado_id=9999` | 400 | 400 — "El estado indicado no existe." |
| Falta `estado_id`, `id=1.5`, `id=-3`, `id=true` | 400 | 400 — enteros positivos |
| JSON mal formado / arreglo `[…]` | 400 | 400 — JSON inválido / se esperaba objeto |
| `Content-Type: form-urlencoded` | 415 | 415 — debe ser `application/json` |
| Sin `X-CSRF-Token` / token falso | 403 | 403 — token inválido |
| PUT con sesión | 405 | 405 |

## Observaciones

- El caso 200 de `turno_estado` reenvía el **mismo** `estado_id` que ya tiene
  el turno, así que la corrida no modifica la base compartida.
- La patente se acepta en cualquier capitalización y con espacios/guiones:
  `normalizarPatente()` la limpia antes de consultar.
- No se encontraron bugs: los tres endpoints devuelven siempre JSON con el
  código HTTP correcto y `php -l` no reporta errores de sintaxis en ningún
  archivo tocado por el TP9.
