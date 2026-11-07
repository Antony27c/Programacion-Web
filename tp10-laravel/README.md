# BOXLY · TP10 Parte B — Catálogo de servicios en Laravel

## Objetivo

La parte B del TP10 pide rehacer un módulo del sitio usando un framework MVC: definir el routing para la URI del
módulo, un controlador propio, un modelo con el ORM del framework y las vistas con su motor de plantillas.
Elegí **Laravel 12** y el módulo de **catálogo de servicios** del taller (Service, Frenos, Diagnóstico, etc.),
que en el sitio PHP principal se maneja con la tabla `servicios`.

## Qué hice

Armé un módulo independiente con su propia base de datos `boxly_laravel` (migraciones + seeder). Incluye:

- CRUD completo (listar con búsqueda, ver, crear, editar y eliminar).
- Validación con Form Request y mensajes en español.
- No deja eliminar un servicio si algún turno lo usa (la FK `turno_servicio.servicio_id` es `ON DELETE RESTRICT`).
- API JSON de solo lectura.

## Requisitos

- PHP 8.2 o superior (extensiones `pdo_mysql`, `mbstring`, `openssl`).
- Composer 2.
- MySQL 8 (o MariaDB 10.4+) con una base vacía `boxly_laravel`.

> **Por qué una base propia (decisión):** el módulo crea sus tablas con migraciones y las carga con un seeder, así se instala
> desde cero con un solo comando, los tests corren aislados (SQLite en memoria) y no se arriesga ninguna tabla
> de la base `boxly` que usa el sitio PHP principal. Las tablas `servicios` y `turno_servicio` replican la estructura
> de `boxly` (nombre único, precio con 2 decimales, FK `ON DELETE RESTRICT`); `turnos` es una versión reducida.
> Sesión y caché se guardan en archivos (`storage/`).

## Instalación

```bash
cd tp10-laravel
composer install
cp .env.example .env          # en Windows: copy .env.example .env
php artisan key:generate
```

Completar en `.env` la contraseña de MySQL (`DB_PASSWORD`) y, si hace falta, `DB_HOST`, `DB_PORT` y `DB_USERNAME`.
Crear la base y cargar tablas y datos iniciales:

```sql
CREATE DATABASE boxly_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate --seed
php artisan serve --port=8094
```

Abrir <http://localhost:8094>.

## Tests

```bash
php artisan test
```

`tests/Feature/ServicioTest.php` usa SQLite en memoria (requiere `pdo_sqlite`): listado web 200, redirección del inicio,
API JSON (listado y 404), alta con validación y bloqueo del borrado de un servicio con turnos.

### Pruebas realizadas

- `php artisan test`: 7 tests en verde (28 aserciones).
- `php artisan route:list --except-vendor`: aparecen las 7 rutas del resource `servicios`, las 2 de la API y `/`.
- Con `php artisan serve --port=8094` probé con `curl`:
  - `GET /servicios` → `200 OK`, HTML con los 4 servicios del seeder.
  - `GET /api/servicios` → `200 OK`, `Content-Type: application/json`, `"total":4`.
  - `GET /api/servicios/999` → `404` con `{"status":"error",...}`.
- En los tests de Feature: alta con datos inválidos (errores de `nombre` y `precio_base`), alta con precio con coma
  decimal (`30.000,50` se guarda como `30000.50`) y borrado de "Service" bloqueado porque tiene un turno asociado.

## Rutas

### Web

| Método    | URI                         | Nombre               | Acción                              |
|-----------|-----------------------------|----------------------|-------------------------------------|
| GET       | `/`                         | —                    | Redirige al listado                 |
| GET       | `/servicios?q=texto`        | `servicios.index`    | Listado con búsqueda                |
| GET       | `/servicios/create`         | `servicios.create`   | Formulario de alta                  |
| POST      | `/servicios`                | `servicios.store`    | Guarda un servicio nuevo            |
| GET       | `/servicios/{servicio}`     | `servicios.show`     | Detalle                             |
| GET       | `/servicios/{servicio}/edit`| `servicios.edit`     | Formulario de edición               |
| PUT/PATCH | `/servicios/{servicio}`     | `servicios.update`   | Actualiza                           |
| DELETE    | `/servicios/{servicio}`     | `servicios.destroy`  | Elimina (si no tiene turnos)        |

### API JSON

| Método | URI                     | Nombre                | Respuesta                                             |
|--------|-------------------------|-----------------------|-------------------------------------------------------|
| GET    | `/api/servicios?q=texto`| `api.servicios.index` | `{"status":"success","total":n,"data":[...]}`         |
| GET    | `/api/servicios/{id}`   | `api.servicios.show`  | `{"status":"success","data":{...}}` o 404 `{"status":"error","message":"..."}` |

## Dónde está cada parte (MVC)

| Parte        | Archivo                                                                 |
|--------------|-------------------------------------------------------------------------|
| Routing      | `routes/web.php`                                                        |
| Controlador  | `app/Http/Controllers/ServicioController.php` (resource)                |
| Controlador API | `app/Http/Controllers/Api/ServicioApiController.php`                 |
| Validación   | `app/Http/Requests/ServicioRequest.php`                                 |
| Modelo       | `app/Models/Servicio.php` (y `app/Models/Turno.php` para la relación)   |
| Migraciones  | `database/migrations/2026_11_06_000100_create_servicios_table.php`, `..._000200_create_turnos_table.php` |
| Seeder       | `database/seeders/ServicioSeeder.php`                                   |
| Tests        | `tests/Feature/ServicioTest.php`                                        |
| Vistas       | `resources/views/layouts/app.blade.php` y `resources/views/servicios/` (`index`, `create`, `edit`, `show`, `_form`) |
