# TP10 — Refactorización a MVC e implementación con framework

## Objetivo

Según el enunciado, el TP10 tiene dos partes complementarias:

- **Parte A — Refactorización nativa a MVC:** tomar un módulo ya desarrollado en la materia y reorganizarlo en
  `app/Models/`, `app/Views/` y `app/Controllers/`, dejando las consultas SQL (PDO con sentencias preparadas)
  en clases Modelo, el manejo de la solicitud en un Controlador y el HTML limpio en las Vistas.
- **Parte B — Módulo en framework:** crear un proyecto base con un framework PHP e implementar un módulo
  independiente configurando routing, un controlador propio, un modelo con el ORM del framework y una vista con
  su motor de plantillas.

Para la parte A elegí el **módulo de turnos** (TP7 + TP8): la solicitud pública de turno (`turnos.php`) y el
CRUD del panel de administración (`admin/`). Para la parte B usé **Laravel 12** con el **catálogo de servicios**
del taller, en la carpeta [`tp10-laravel/`](../../tp10-laravel/).

---

## Parte A — MVC nativo en PHP puro

### Qué hice

Antes del TP10 el módulo de turnos estaba repartido en scripts que mezclaban todo: `admin/*.php` validaban,
consultaban la base e imprimían HTML; las consultas vivían en funciones sueltas de `includes/turnos_db.php`, la
validación en `includes/funciones.php` y el formulario en `admin/_formulario.php` / `includes/campos_turno.php`.

Lo reorganicé así:

- **Modelos** (`app/Models/`): todas las consultas del módulo, siempre con `prepare()` + `execute()`.
- **Controladores** (`app/Controllers/`): leen la request (`$_GET` / `$_POST`), validan, verifican el token CSRF,
  llaman a los modelos y deciden si redirigir o qué vista mostrar.
- **Vistas** (`app/Views/turnos/`): solo HTML; cada variable que se imprime pasa por `e()`
  (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`).
- **Front controllers**: `turnos.php` y `admin/*.php` quedaron de 3 o 4 líneas: aplican el guardián de rol del
  TP8 (en `admin/`), cargan `app/bootstrap.php` y ejecutan la acción del controlador.

El comportamiento para el usuario no cambió: mismas URLs, mismos formularios, mismos mensajes.

### Estructura

```
├── app/
│   ├── bootstrap.php                  ← autoload PSR-4 simple (App\ → app/) + helpers del sitio
│   ├── Core/
│   │   ├── Controller.php             ← base: render() con header/footer, redirect(), esPost()
│   │   ├── Model.php                  ← base: recibe el PDO (o lo pide a Database)
│   │   └── Database.php               ← una sola conexión por request, envuelve config/conexion.php
│   ├── Models/
│   │   ├── Turno.php                  ← listar, obtener, guardar (transacción), eliminar + reglas de validación
│   │   ├── Usuario.php                ← obtener cuenta, buscar/crear cliente por email
│   │   ├── Vehiculo.php               ← buscar/crear vehículo por patente
│   │   └── Catalogo.php               ← marcas, servicios y estados (selects y listas blancas)
│   ├── Controllers/
│   │   ├── TurnoController.php        ← CRUD del panel: index, crear, editar, eliminar
│   │   └── SolicitudTurnoController.php ← solicitud pública de turno
│   └── Views/turnos/
│       ├── index.php                  ← listado + filtro por estado + botones editar/eliminar
│       ├── form.php                   ← alta / edición (panel)
│       ├── solicitar.php              ← formulario público + resumen al confirmar
│       └── _campos.php                ← campos compartidos del formulario de turno
├── admin/index.php crear.php editar.php eliminar.php   ← front controllers del panel
├── turnos.php                         ← front controller de la solicitud pública
└── config/conexion.php                ← conexión PDO del TP7 (se sigue usando)
```

Archivos que dejaron de existir porque su contenido pasó a la estructura MVC:

| Antes | Ahora |
|-------|-------|
| `includes/turnos_db.php` | `app/Models/Turno.php`, `Usuario.php`, `Vehiculo.php`, `Catalogo.php` |
| `validarTurno()` / `turnoVacio()` en `includes/funciones.php` | `Turno::validar()` / `Turno::vacio()` |
| `admin/_formulario.php` | `app/Views/turnos/form.php` |
| `includes/campos_turno.php` | `app/Views/turnos/_campos.php` |

### Flujo de una petición

```
GET /admin/index.php?estado=2
  └─ admin/index.php                   front controller
       ├─ includes/auth_guard.php      TP8: sin sesión → login, rol ≠ Administrador → mi-cuenta
       ├─ app/bootstrap.php            autoload + helpers
       └─ TurnoController::index()
            ├─ valida el filtro contra el catálogo de estados (lista blanca)
            ├─ Turno::listar(2)        SELECT con JOIN y parámetros
            └─ render('turnos/index')  header + vista + footer
```

### Responsabilidades

| Capa | Archivo | Qué hace |
|------|---------|----------|
| Conexión | `app/Core/Database.php` | Incluye `config/conexion.php` (TP7: PDO con `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES = false`, `try/catch` y `error_log`) en un ámbito aislado y guarda el `$pdo` para reutilizarlo en todo el request. |
| Modelo | `app/Models/Turno.php` | `listar($estadoId, $usuarioId)` con `INNER JOIN` a usuarios, vehículos, marcas y estados; `obtener($id)`; `guardar($datos, $admin, $id)` en una transacción (usuario → vehículo → turno → `turno_servicio`) con `rollBack()` si algo falla; `eliminar($id)`. También concentra las reglas de negocio del turno (`validar()`, `vacio()`, `HORARIOS`). |
| Modelo | `app/Models/Usuario.php`, `Vehiculo.php`, `Catalogo.php` | Consultas de las tablas relacionadas, todas preparadas. `Usuario::obtener()` lo usa también `mi-cuenta.php`. |
| Controlador | `app/Controllers/TurnoController.php` | Lee `$_GET`/`$_POST`, valida ids con `FILTER_VALIDATE_INT`, exige el token CSRF en alta, edición y baja (`csrfValido()`), solo acepta la baja por `POST`, y redirige con `?status=created/updated/deleted/notfound/invalid`. |
| Controlador | `app/Controllers/SolicitudTurnoController.php` | Precarga los datos de la cuenta si hay sesión (TP8), valida el `POST` y guarda el turno como *Pendiente*. Desde el sitio público un email o una patente existentes se reutilizan sin pisar sus datos. |
| Vista | `app/Views/turnos/*.php` | Solo marcado HTML + Bootstrap. No hay SQL ni acceso a `$_POST`; todo se imprime con `e()`. |
| Base | `app/Core/Controller.php` | `render($vista, $datos)` hace `extract()` dentro de una función y arma la página con `includes/header.php` + vista + `includes/footer.php`, así las vistas reutilizan el layout de todo el sitio. |

### Decisiones

- **Sin Composer en la parte A:** el autoload es un `spl_autoload_register()` de pocas líneas en
  `app/bootstrap.php` que traduce `App\Models\Turno` a `app/Models/Turno.php`. Así se ve cómo funciona por
  dentro lo que un framework hace solo.
- **Front controllers por archivo, no un router único:** mantuve las URLs del TP7 (`admin/crear.php`,
  `admin/editar.php?id=…`) para no romper links ni el guardián de rol del TP8, que se aplica en cada punto de
  entrada antes de cargar el controlador.
- **`config/conexion.php` sigue siendo la conexión:** `Database` lo envuelve en lugar de duplicar la lógica, así
  el TP7 queda igual y los modelos reciben el PDO por constructor (se puede pasar otro, por ejemplo en una
  transacción compartida entre `Turno`, `Usuario` y `Vehiculo`). La constante opcional `BOXLY_CONEXION` permite
  apuntar a otro archivo de conexión (la usé para probar contra una base de pruebas).
- **Validación en el modelo, invocada por el controlador:** las reglas del turno (campos obligatorios, regex de
  patente y teléfono, listas blancas contra los catálogos) son reglas de negocio de la entidad, por eso quedaron
  en `Turno::validar()`; el controlador decide cuándo validar y qué hacer con los errores.
- **Login y registro del TP8** quedaron como estaban (`includes/usuarios_db.php`), solo que ahora toman la
  conexión de `App\Core\Database`; el módulo refactorizado es el de turnos.

### Cómo probarlo

1. Base `boxly` creada con `tp6/schema.sql` y `tp6/consultas.sql`, y credenciales en `config/conexion.local.php`.
2. Desde la raíz del proyecto:

   ```bash
   php -S localhost:8093
   ```

3. Abrir <http://localhost:8093/turnos.php> (solicitud pública) y <http://localhost:8093/admin/>
   (ingresar con `admin@boxly.com.ar` / `Boxly2026!`).

### Pruebas realizadas

- `php -l` sobre todos los `.php` del sitio (39 archivos, sin contar `tp10-laravel/`): sin errores de sintaxis.
- Revisé todos los `require`/`include` del sitio: ninguno apunta a los archivos que se eliminaron.
- Con `php -S localhost:8093` y `curl` (cookie jar `-c`/`-b`, tomando el token CSRF de cada formulario):

| Prueba | Resultado |
|--------|-----------|
| `GET /admin/` sin sesión | `302` → `../login.php?error=acceso_denegado` |
| `GET /turnos.php` | `200`, formulario con los servicios del catálogo |
| `POST /login.php` como admin | `302` → `admin/index.php` |
| `GET /admin/` con sesión de admin | `200`, listado con los 5 turnos de ejemplo |
| `GET /admin/index.php?estado=1` | `200`, solo los turnos *Pendiente* |
| `POST /admin/crear.php` con datos inválidos | `200` con 13 errores de validación |
| `POST /admin/crear.php` sin token CSRF | `302` → `index.php?status=invalid` |
| `POST /admin/crear.php` válido | `302` → `index.php?status=created`, el turno aparece en el listado |
| `POST /admin/editar.php` (cambio de estado y servicios) | `302` → `index.php?status=updated`, aparece al filtrar por *Confirmado* |
| `GET /admin/editar.php?id=99999` | `302` → `index.php?status=notfound` |
| `GET /admin/eliminar.php?id=…` | `302` → `index.php?status=invalid` (no borra por GET) |
| `POST /admin/eliminar.php` con token | `302` → `index.php?status=deleted` |
| `POST /turnos.php` (solicitud pública) | `200`, "Solicitud N° … registrada correctamente" |
| Registro + login de un cliente y `GET /admin/` | `302` → `../mi-cuenta.php?error=sin_permiso`; `mi-cuenta.php` responde `200` |
| `GET /logout.php` | `302` → `index.php` sin cerrar la sesión (`mi-cuenta.php` sigue en `200`) |
| `POST /logout.php` con token y luego `GET /mi-cuenta.php` | `302` → `login.php?ok=logout`; después `302` → `login.php?error=acceso_denegado` |

Las pruebas que escriben (alta, edición, baja, solicitud y registro) las corrí contra una copia de la base
(`boxly_tp10_prueba`, creada con los mismos `schema.sql` y `consultas.sql`) para no modificar los datos de `boxly`.

---

## Parte B — Módulo en Laravel

El detalle completo (instalación, rutas y pruebas) está en [`tp10-laravel/README.md`](../../tp10-laravel/README.md).
Resumen:

### Qué hice

Un proyecto **Laravel 12** independiente en `tp10-laravel/` con el **catálogo de servicios** del taller, con su
propia base `boxly_laravel` creada por migraciones y cargada con un seeder:

- CRUD completo de servicios (listar con búsqueda, ver, crear, editar y eliminar).
- Validación con Form Request y mensajes en español.
- No deja eliminar un servicio que tenga turnos asociados (FK `turno_servicio.servicio_id` con `ON DELETE RESTRICT`).
- API JSON de solo lectura (`/api/servicios` y `/api/servicios/{id}`).

### Dónde está cada parte

| Requisito del enunciado | Archivo |
|-------------------------|---------|
| Routing | `tp10-laravel/routes/web.php` (`Route::resource('servicios', ...)` + grupo `api`) |
| Controlador propio | `tp10-laravel/app/Http/Controllers/ServicioController.php` y `Api/ServicioApiController.php` |
| Modelo (Eloquent) | `tp10-laravel/app/Models/Servicio.php` (relación `belongsToMany` con `Turno` por `turno_servicio`) |
| Vistas (Blade) | `tp10-laravel/resources/views/layouts/app.blade.php` y `resources/views/servicios/` |
| Validación | `tp10-laravel/app/Http/Requests/ServicioRequest.php` |
| Migraciones y seeder | `tp10-laravel/database/migrations/`, `database/seeders/ServicioSeeder.php` |
| Tests | `tp10-laravel/tests/Feature/ServicioTest.php` |

### Cómo correrlo

```bash
cd tp10-laravel
composer install
cp .env.example .env
php artisan key:generate
# completar DB_PASSWORD en .env y crear la base boxly_laravel
php artisan migrate --seed
php artisan serve --port=8094
```

Abrir <http://localhost:8094/servicios>. Los tests se corren con `php artisan test` (SQLite en memoria).

---

## Comparación: MVC nativo vs. Laravel

| Aspecto | Parte A (PHP puro) | Parte B (Laravel) |
|---------|--------------------|-------------------|
| Ruteo | Un archivo por acción (`admin/crear.php`, …) | `routes/web.php` con `Route::resource` |
| Autoload | `spl_autoload_register` propio en `app/bootstrap.php` | Composer (PSR-4) |
| Modelo | Clases con PDO y SQL escrito a mano (sentencias preparadas) | Eloquent ORM |
| Vistas | PHP con `e()` + `include` de header/footer | Blade (`{{ }}` escapa solo, `@extends`, `@include`) |
| Validación | `Turno::validar()` propio | Form Request (`ServicioRequest`) |
| CSRF | `csrfToken()` / `csrfValido()` propios | Middleware de Laravel + `@csrf` |
| Base de datos | `boxly` (esquema del TP6) | `boxly_laravel` (migraciones + seeder) |

Hacer primero la versión nativa me sirvió para entender qué resuelve el framework: en la parte A tuve que escribir
el autoload, el `render()` con el layout, el manejo del CSRF y las redirecciones; en Laravel todo eso viene dado y
el código del módulo queda reducido a las reglas propias del catálogo.
