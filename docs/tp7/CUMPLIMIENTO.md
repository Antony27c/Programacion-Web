# TP7 — Cumplimiento: persistencia y conexión PHP-MySQL segura

Auditoría del enunciado del TP7 contra `config/conexion.php`, `includes/turnos_db.php`,
`admin/` y `turnos.php`, sobre el esquema del TP6.

Estados: **OK** = ya cumplía · **ARREGLADO** = se corrigió en esta revisión · **FALTA** = pendiente.

## Tabla de requisitos

### Conexión segura (`config/conexion.php`)

| # | Requisito del enunciado | Archivo | Estado | Detalle |
|---|---|---|---|---|
| C1 | Instancia PDO con manejo de excepciones `try/catch (PDOException)` | `config/conexion.php` | OK | `new PDO(...)` dentro de `try` con `catch (\PDOException $e)`. |
| C2 | `ERRMODE_EXCEPTION` + fetch asociativo + prepares nativos | `config/conexion.php` | OK | `ATTR_ERRMODE => ERRMODE_EXCEPTION`, `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`, `ATTR_EMULATE_PREPARES => false`. |
| C3 | Charset `utf8mb4` | `config/conexion.php` | OK | `charset=utf8mb4` en el DSN (además de `SET NAMES utf8mb4` en los scripts del TP6). |
| C4 | Sin credenciales versionadas | `config/conexion.php`, `config/conexion.local.php`, `config/conexion.local.example.php`, `.gitignore` | OK | Credenciales por defecto vacías; las reales viven en `conexion.local.php`, que está en `.gitignore` y no figura en `git ls-files`. Hay plantilla `conexion.local.example.php`. |
| C5 | Error de conexión controlado | `config/conexion.php` | OK | El detalle va a `error_log`, la respuesta es HTTP 500 con mensaje genérico (no filtra host ni credenciales). |

### Integración y CRUD

| # | Requisito del enunciado | Archivo | Estado | Detalle |
|---|---|---|---|---|
| I1 | CRUD integrado a la maquetación del sitio (no ejemplo aislado) | `admin/index.php`, `admin/crear.php`, `admin/editar.php`, `admin/eliminar.php`, `admin/_formulario.php` | OK | El panel usa `includes/header.php`/`footer.php` y las clases propias del sitio (`bx-*`, Bootstrap); listado con filtro por estado y acciones por fila. |
| I2 | La entidad corresponde al modelo del TP6 | `includes/turnos_db.php`, `tp6/schema.sql` | OK | Se gestionan `turnos` con sus FK a `usuarios`, `vehiculos`, `estados_turno` y la relación N:M `turno_servicio`. |
| I3 | Flujo completo formulario → persistencia → reflejo en BD | `turnos.php`, `admin/*` | OK | Verificado en vivo: login admin → `/admin/` lista los turnos; alta/edición/baja persisten en MySQL (evidencia abajo). |

### Seguridad

| # | Requisito del enunciado / de la auditoría | Archivo | Estado | Detalle |
|---|---|---|---|---|
| P1 | Sentencias preparadas en INSERT/UPDATE/SELECT/DELETE | `includes/turnos_db.php`, `admin/eliminar.php` | OK | Todas las consultas usan `prepare()` + `execute()` con `:parametros` o `?`. `catalogos()` usa `query()` solo con SQL estático (sin entrada de usuario). Los `id` se validan con `FILTER_VALIDATE_INT`. |
| P2 | Listas blancas y validación en servidor | `includes/funciones.php` (`validarTurno`, `enCatalogo`, `limpiar`), `includes/campos_turno.php` | OK | Marca, servicios y estado se validan contra los catálogos de la base; patente/teléfono por regex; fecha válida; UTF-8 válido antes de insertar. |
| X1 | `htmlspecialchars()` en toda variable impresa | todas las vistas (`e()` en `includes/funciones.php`) | OK | Toda salida pasa por `e()` = `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`, incluidos los `value`/`selected`/`checked` del formulario compartido. |
| S1 | Protección CSRF en formularios que modifican datos | `admin/_formulario.php`, `admin/crear.php`, `admin/editar.php`, `admin/eliminar.php`, `login.php`, `registro.php`, `logout.php` | OK | Token de sesión `csrfToken()`/`csrfValido()` (`hash_equals`); la baja solo acepta POST con token (un GET o un POST sin token redirigen a `status=invalid`). |
| S2 | Errores de BD controlados (sin stack trace ni errores no controlados) | `admin/crear.php`, `admin/editar.php`, `admin/eliminar.php`, `admin/index.php`, `includes/turnos_db.php` | ARREGLADO | `guardarTurno()` corre en transacción con `rollBack()` ante cualquier `Throwable`. El intento anterior agregó `try/catch (PDOException)` en crear/editar; en esta revisión se completó el patrón en `eliminar.php` (nuevo estado `dberror` con mensaje en `index.php`). |
| S3 | `turnos.php`: formulario público del sitio | `turnos.php` | ARREGLADO | El POST de `turnos.php` no validaba token CSRF ni envolvía `guardarTurno()` en `try/catch`. Se agregó el campo oculto `csrf` al formulario, la validación `csrfValido()` con mensaje amigable (`$errorGeneral`) y el `try/catch (PDOException)` con `error_log`, reutilizando `csrfToken()`/`csrfValido()`/`e()` de `includes/funciones.php` sin modificarlas. |

## Evidencia

### Sintaxis (`php -l`, PHP 8.2.12)

```
No syntax errors detected in config/conexion.php
No syntax errors detected in config/conexion.local.example.php
No syntax errors detected in admin/index.php
No syntax errors detected in admin/crear.php
No syntax errors detected in admin/editar.php
No syntax errors detected in admin/eliminar.php
No syntax errors detected in admin/_formulario.php
No syntax errors detected in includes/turnos_db.php
No syntax errors detected in includes/campos_turno.php
No syntax errors detected in turnos.php
```

### Demo en vivo — `php -S localhost:8096` + curl

```
# Login como administrador
$ curl -c cookies.txt http://localhost:8096/login.php          → 200, token CSRF en el form
$ curl -b cookies.txt -c cookies.txt -d "csrf=...&email=admin@boxly.com.ar&password=***" \
       http://localhost:8096/login.php                        → 302 → /admin/index.php

# Panel admin autenticado
$ curl -b cookies.txt http://localhost:8096/admin/            → 200, 18.949 bytes
   "Gestión de turnos" + tabla con 5 turnos (#1–#5), 6 tokens csrf en el HTML
$ curl -b cookies.txt http://localhost:8096/admin/crear.php   → 200
$ curl -b cookies.txt "http://localhost:8096/admin/editar.php?id=1" → 200

# La baja no acepta GET ni POST sin token
$ curl -b cookies.txt "http://localhost:8096/admin/eliminar.php?id=1"  → 302 → index.php?status=invalid
$ curl -b cookies.txt -d "id=1" http://localhost:8096/admin/eliminar.php → 302 → index.php?status=invalid

# Formulario público turnos.php — ciclo completo con token CSRF
$ curl http://localhost:8096/turnos.php            → 200, <input type="hidden" name="csrf" ...>
# POST SIN token (datos válidos): rechazado, no se guarda nada
$ curl -X POST ... http://localhost:8096/turnos.php
   → "El formulario expiró o la solicitud no es válida. Recargá la página e intentá nuevamente."
# POST CON token + datos inválidos: pasa el CSRF y cae en validación
   → "Revisá el formulario: encontramos 12 error(es) de validación."
# POST CON token + datos válidos: alta real
   → "Solicitud N° 10 registrada correctamente"
# Baja del turno de prueba por admin (token CSRF + POST)
$ curl -d "csrf=...&id=10" http://localhost:8096/admin/eliminar.php → 302 → index.php?status=deleted
$ SELECT COUNT(*) FROM turnos WHERE id=10;         → 0
$ SELECT COUNT(*) FROM turno_servicio WHERE turno_id=10; → 0   (ON DELETE CASCADE)
```

### Correcciones de esta revisión

- `admin/eliminar.php`: `DELETE` envuelto en `try/catch (PDOException)` con `error_log` y
  redirect a `index.php?status=dberror`; se agregó el mensaje `dberror` en `admin/index.php`.
- `includes/turnos_db.php` → `sincronizarServicios()`: guarda para lista de servicios
  vacía (antes `NOT IN ()` sería SQL inválido si alguna vez se llamaba sin servicios).
- `turnos.php`: campo oculto `csrf` en el formulario, validación `csrfValido()` en el POST
  y `try/catch (PDOException)` alrededor de `guardarTurno()` con mensaje amigable.

## Pendientes

Ninguno dentro del alcance auditado.
