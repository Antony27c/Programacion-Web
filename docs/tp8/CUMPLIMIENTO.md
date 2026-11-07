# TP8 — Cumplimiento: manejo de estado, sesiones y autenticación

Auditoría del enunciado del TP8 contra el código del sitio BOXLY.

Estados: **OK** = ya cumplía · **ARREGLADO** = se corrigió en esta revisión · **FALTA** = pendiente.

## Tabla de requisitos

| # | Requisito del enunciado | Archivo(s) | Estado | Detalle |
|---|---|---|---|---|
| 1 | Tabla `usuarios`: `id` INT PK auto_increment, `nombre` VARCHAR, `email` VARCHAR UNIQUE, `password` VARCHAR(255) para el hash, `rol` 'admin'/'usuario' o equivalente documentado, `created_at` TIMESTAMP | `tp6/schema.sql` | OK | El esquema del TP6 es el equivalente normalizado documentado: `id` INT UNSIGNED AUTO_INCREMENT PK, `nombre`/`apellido` VARCHAR(60), `email` VARCHAR(150) con `uq_usuarios_email UNIQUE`, `password_hash` VARCHAR(255) (guarda el hash bcrypt), `rol_id` FK → `roles.nombre` (*Administrador* / *Cliente*, sembrados en `tp6/consultas.sql`), `created_at`/`updated_at` TIMESTAMP. No falta ningún dato exigido. |
| 2a | Registro con sanitización estricta de entradas (XSS) | `registro.php`, `includes/usuarios_db.php` (`validarRegistro`) | ARREGLADO | `limpiar()` (trim, descarta no escalares) + `filter_var(FILTER_VALIDATE_EMAIL)` + regex de teléfono + `mb_check_encoding` UTF-8 + lista blanca de caracteres para nombre/apellido (`/^[\p{L}\s'.-]+$/u`, solo letras, espacios, apóstrofo, punto y guion). La lista blanca se agregó en esta revisión; la salida siempre pasa por `e()`. |
| 2b | Email único: verificación previa + constraint | `registro.php`, `includes/usuarios_db.php`, `tp6/schema.sql` | OK | `emailRegistrado()` consulta preparada antes del INSERT; el `UNIQUE` de la base cubre la carrera y el `catch (PDOException)` con código `23000` muestra "Ya existe una cuenta con ese email.". Email normalizado a minúsculas. |
| 2c | `password_hash($password, PASSWORD_BCRYPT)` | `includes/usuarios_db.php` (`registrarUsuario`) | OK | `password_hash($d['password'], PASSWORD_BCRYPT)` en el INSERT preparado; el password nunca se guarda en claro ni se reimprime. |
| 2d | Consultas preparadas (PDO o MySQLi) | `includes/usuarios_db.php`, `config/conexion.php` | OK | PDO con `EMULATE_PREPARES = false` y `ERRMODE_EXCEPTION`; todas las consultas (`buscarUsuarioPorEmail`, `emailRegistrado`, `registrarUsuario`, `autenticar`, `obtenerUsuario`) usan `prepare()` + `execute()` con parámetros. |
| 3a | Login con `password_verify()` | `includes/usuarios_db.php` (`autenticar`) | OK | `password_verify($password, $hash)` contra `password_hash` de la base; hash ficticio cuando el email no existe (no revela cuentas) y rehash automático si `password_needs_rehash()`. |
| 3b | `session_start()` ante login correcto | `includes/sesion.php` (`iniciarSesion`, `abrirSesion`) | OK | `session_start()` dentro de `iniciarSesion()` con `use_strict_mode`, `use_only_cookies`, nombre propio `BOXLYSESSID` y cookie `HttpOnly`/`SameSite=Lax`/`Secure` bajo HTTPS. |
| 3c | `session_regenerate_id(true)` | `includes/sesion.php:58` | OK | `abrirSesion()` regenera el ID con borrado del anterior (anti Session Fixation) y rota el token CSRF. Verificado: el `BOXLYSESSID` cambia tras el POST de login. |
| 3d | `$_SESSION['usuario_id']`, `['usuario_nombre']`, `['rol']` | `includes/sesion.php:60-62` | OK | `abrirSesion()` guarda los tres datos clave del usuario autenticado. |
| 3e | Redirección tras login correcto | `login.php` | OK | Admin → `admin/index.php`, cliente → `mi-cuenta.php` (closure `$destino` por rol). Si ya hay sesión, redirige directo. |
| 3f | `login.php` muestra mensaje para `error=acceso_denegado` | `login.php:41` | OK | `$avisos['acceso_denegado']` → alerta "Tenés que iniciar sesión para acceder a esa sección." (también `ok=registro` y `ok=logout`). |
| 4a | Guardián reutilizable con `isset($_SESSION['usuario_id'])` | `includes/auth_guard.php` | OK | `require` único que inicia sesión y exige `$_SESSION['usuario_id']`; pensado para `require` al inicio de cada vista protegida. |
| 4b | Sin sesión → `login.php?error=acceso_denegado` | `includes/auth_guard.php:15` | OK | `header('Location: ' . rutaBase() . 'login.php?error=acceso_denegado')` + `exit`; `rutaBase()` resuelve el prefijo desde subcarpetas (`admin/`). |
| 4c | Al menos dos vistas protegidas | `mi-cuenta.php`, `admin/index.php`, `admin/crear.php`, `admin/editar.php`, `admin/eliminar.php` | OK | 5 vistas: `mi-cuenta.php` para cualquier usuario logueado y todo el panel `admin/` (4 archivos). Verificado con curl: sin sesión ambas redirigen 302. |
| 4d | Restricción por rol (solo admin) | `includes/auth_guard.php:19-22`, `admin/*.php` | OK | `$rolesPermitidos = ['Administrador']` antes del `require`; otro rol → `mi-cuenta.php?error=sin_permiso`. Verificado: el usuario de prueba recibe 302 a `mi-cuenta.php?error=sin_permiso` al pedir `/admin/`. |
| 5a | Logout: unset de `$_SESSION` | `includes/sesion.php:71` (`cerrarSesion`) | OK | `$_SESSION = []` vacía la superglobal antes de destruir la sesión. |
| 5b | Eliminación de la cookie de sesión | `includes/sesion.php:72-82` | OK | `setcookie(session_name(), '', [...])` con `expires` en el pasado y los parámetros de `session_get_cookie_params()` (path, domain, secure, httponly, samesite). Verificado: `Set-Cookie: BOXLYSESSID=deleted; expires=Thu, 01 Jan 1970...`. |
| 5c | `session_destroy()` y redirección | `includes/sesion.php:83`, `logout.php` | OK | `session_destroy()` y `Location: login.php?ok=logout`. Solo acepta POST con token CSRF: un link externo no puede cerrar la sesión. |
| 6 | Cookies de sesión seguras (httponly, samesite) | `includes/sesion.php:19-25` | OK | `httponly => true`, `samesite => 'Lax'`, `secure` activo bajo HTTPS, `lifetime => 0` (cookie de sesión), `use_strict_mode` y `use_only_cookies`. Verificado en `Set-Cookie: BOXLYSESSID=...; path=/; HttpOnly; SameSite=Lax`. |
| 7 | Entradas manipuladas (password como array) | `login.php:22`, `includes/usuarios_db.php:95-96` | ARREGLADO | `(string) ($_POST['password'] ?? '')` emitía *"Array to string conversion"* si llegaba `password[]=x`. Ahora `is_scalar()` descarta el array y el login falla como credencial vacía / el registro marca error de validación. Mismo criterio que `limpiar()` (TP5). |

## Notas sobre el esquema (punto 1 del enunciado)

El enunciado pide la tabla `usuarios` con columnas `password` y `rol`; el proyecto la
implementó normalizada en el TP6 y eso es lo que usa todo el sitio:

| Pide el enunciado | En `tp6/schema.sql` | Equivalente |
|---|---|---|
| `password` VARCHAR(255) | `password_hash` VARCHAR(255) NULL | Misma función (almacena el hash), nombre explícito. |
| `rol` VARCHAR 'admin'/'usuario' | `rol_id` FK → `roles.nombre` | *Administrador* / *Cliente* (catálogo en `roles`, evita strings mágicos). |
| `nombre` VARCHAR | `nombre` + `apellido` VARCHAR(60) | El formulario pide ambos. |
| `created_at` TIMESTAMP | `created_at` + `updated_at` TIMESTAMP | Además auditoría de actualización. |

## Evidencia

### Sintaxis (`php -l`, PHP 8.2)

```
No syntax errors detected in login.php
No syntax errors detected in registro.php
No syntax errors detected in logout.php
No syntax errors detected in mi-cuenta.php
No syntax errors detected in includes/auth_guard.php
No syntax errors detected in includes/sesion.php
No syntax errors detected in includes/usuarios_db.php
```

### Sin sesión → guardián de rutas (`php -S localhost:8095` + curl)

```
$ curl -s -o /dev/null -w '%{http_code} -> %{redirect_url}' http://localhost:8095/admin/
302 -> http://localhost:8095/login.php?error=acceso_denegado

$ curl -s -o /dev/null -w '%{http_code} -> %{redirect_url}' http://localhost:8095/mi-cuenta.php
302 -> http://localhost:8095/login.php?error=acceso_denegado
```

El mensaje se muestra en `login.php?error=acceso_denegado`:

```
alert alert-warning" role="status">Tenés que iniciar sesión para acceder a esa sección.
```

### Login de administrador

```
$ curl -b admin.jar -c admin.jar -X POST --data-urlencode "email=admin@boxly.com.ar" \
       --data-urlencode "password=Boxly2026!" --data-urlencode "csrf=$CSRF" \
       http://localhost:8095/login.php
302 -> http://localhost:8095/admin/index.php

$ curl -s -b admin.jar -o /dev/null -w '%{http_code}' http://localhost:8095/admin/
200
```

ID de sesión regenerado en el login (anti Session Fixation):

```
ANTES=22hl87ke3cf4ohpm6bi22upagb
DESPUES=q1v8gkkcm0oktg7vvadtk0e7rv
```

Cookie emitida:

```
Set-Cookie: BOXLYSESSID=5ccr317afa1l2gvlts6kls2p5n; path=/; HttpOnly; SameSite=Lax
```

### Registro de usuario de prueba y restricción por rol

Usuario: `auditoria<timestamp>@boxly.test` (rol *Cliente*).

```
$ curl -b cli.jar -c cli.jar -X POST ... http://localhost:8095/registro.php
302 -> http://localhost:8095/login.php?ok=registro

$ curl -b cli2.jar -c cli2.jar -X POST ... http://localhost:8095/login.php   # login del cliente
302 -> http://localhost:8095/mi-cuenta.php

$ curl -s -b cli2.jar -o /dev/null -w '%{http_code} -> %{redirect_url}' http://localhost:8095/admin/
302 -> http://localhost:8095/mi-cuenta.php?error=sin_permiso

$ curl -s -b cli2.jar -o /dev/null -w '%{http_code}' http://localhost:8095/mi-cuenta.php
200
```

### Logout — limpieza de sesión

```
$ curl -b cli2.jar -c cli2.jar -X POST --data-urlencode "csrf=$CSRF" http://localhost:8095/logout.php
302 -> http://localhost:8095/login.php?ok=logout
Set-Cookie: BOXLYSESSID=deleted; expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-Age=0; path=/; HttpOnly; SameSite=Lax

$ curl -s -b cli2.jar -o /dev/null -w '%{http_code} -> %{redirect_url}' http://localhost:8095/admin/
302 -> http://localhost:8095/login.php?error=acceso_denegado

$ curl -s -b cli2.jar -o /dev/null -w '%{http_code} -> %{redirect_url}' http://localhost:8095/mi-cuenta.php
302 -> http://localhost:8095/login.php?error=acceso_denegado
```

### Casos de error verificados

```
# Password incorrecto → mensaje genérico (no revela si el email existe)
alert alert-danger" role="alert">Email o contraseña incorrectos.

# Email ya registrado
invalid-feedback">Ya existe una cuenta con ese email.

# password[]=x (entrada manipulada) → sin warning, validación normal
```
