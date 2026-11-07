# BOXLY — Programación Web

Sitio del taller **BOXLY** (Salta): servicio mecánico automotor. Maquetación HTML5 + Bootstrap 5,
backend en PHP y persistencia en MySQL mediante PDO.

## Enlaces

- **Repositorio:** https://github.com/Antony27c/Programacion-Web
- **Mockups Figma:** https://www.figma.com/design/1MKE3Skw1MhIkSsq2L2091/Taller-Mecanico-Mockups-UI-UX-TP3
- **Versión estática (TP4) en GitHub Pages:** https://antony27c.github.io/Programacion-Web/entregas/tp4-html/

## Estructura

```
├── config/
│   ├── conexion.php                 ← TP7: conexión PDO con try-catch
│   ├── conexion.local.example.php   ← plantilla de credenciales
│   └── conexion.local.php           ← credenciales reales (NO se versiona)
├── tp6/                             ← TP6: esquema relacional normalizado
│   ├── NORMALIZACION.md             ← tabla sin normalizar, 1FN, 2FN, 3FN, PK/FK y diagrama ER
│   ├── schema.sql                   ← DDL: base `boxly`, 8 tablas, FK, auditoría e índices
│   └── consultas.sql                ← DML: datos iniciales + SELECT/JOIN, UPDATE y DELETE
├── app/                             ← TP10: refactor MVC del módulo de turnos (PHP puro)
│   ├── bootstrap.php                ← autoload PSR-4 (App\*) + helpers del sitio
│   ├── Core/                        ← Controller base, Database (envuelve config/conexion.php) y Model
│   ├── Models/                      ← Catalogo, Turno, Usuario, Vehiculo (SQL preparado)
│   ├── Controllers/                 ← TurnoController (CRUD admin) y SolicitudTurnoController (turno público)
│   └── Views/turnos/                ← index, form, solicitar y _campos (HTML escapado)
├── includes/
│   ├── header.php  footer.php       ← plantilla modular
│   ├── funciones.php                ← sanitización, listas blancas, CSRF
│   ├── auth_guard.php               ← guardián de sesión y roles (TP8)
│   ├── sesion.php                   ← cookie de sesión segura y expiración (TP8)
│   └── usuarios_db.php              ← acceso a datos de usuarios (TP8)
├── login.php registro.php logout.php mi-cuenta.php   ← TP8: autenticación
├── admin/                           ← CRUD de turnos (TP7) como front controllers MVC (TP10), solo rol Administrador
│   ├── index.php                    ← listado (Read) + filtro por estado
│   ├── crear.php                    ← alta (Create)
│   ├── editar.php                   ← modificación (Update)
│   └── eliminar.php                 ← baja (Delete)
├── turnos.php                       ← solicitud pública de turno (front controller MVC)
├── index.php nosotros.php servicios.php servicio-detalle.php
├── presupuesto.php testimonios.php galeria.php contacto.php faq.php
├── css/  js/  img/
├── entregas/
│   └── tp4-html/                    ← TP4: sitio estático HTML + auditoría Lighthouse
├── tp10-laravel/                    ← TP10: catálogo de servicios en Laravel 12 (base `boxly_laravel`)
└── docs/
    ├── tp3/                         ← wireframe, informe TP3 y mockups Figma
    ├── tp5/screenshots/             ← capturas TP5
    └── tp10/                        ← TP10: README del refactor MVC y del módulo Laravel
```

## Tecnología

- HTML5 semántico, Bootstrap 5.3 (CDN), CSS propio (paleta BOXLY)
- PHP 8 con includes modulares
- MySQL 8 + PDO (consultas preparadas)
- Prettier

## Paleta

| Token | Hex | Uso |
|-------|-----|-----|
| Ink | `#0F172A` | Header, footer, hero |
| Texto | `#1E293B` | Tipografía principal |
| Acento | `#0052FF` | CTA, marca, links |
| Titanio | `#6C757D` | Texto secundario |
| Fondo | `#F8F9FA` | Superficies |
| Éxito | `#10B981` | Confirmaciones |

## Cómo correrlo en local

1. **Base de datos:** en MySQL Workbench / phpMyAdmin ejecutar, en este orden,
   `tp6/schema.sql` (crea la base `boxly` y sus tablas) y `tp6/consultas.sql` (carga catálogos,
   clientes, vehículos y turnos de ejemplo y corre las pruebas CRUD).
2. **Credenciales:** completar la contraseña de MySQL en `config/conexion.local.php`
   (si no existe, copiar `config/conexion.local.example.php` con ese nombre).
3. **Servidor:**

   ```bash
   # Servidor embebido de PHP (o copiar el proyecto a htdocs de XAMPP)
   php -S localhost:8099
   ```

4. Abrir http://localhost:8099 (sitio) y http://localhost:8099/admin/ (panel de turnos).
5. **Módulo Laravel (TP10, opcional):** usa su propia base `boxly_laravel` (crearla vacía antes de migrar).

   ```bash
   cd tp10-laravel
   composer install
   cp .env.example .env          # completar DB_PASSWORD
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve --port=8094
   ```

   Abrir http://localhost:8094/servicios. Detalle en [`tp10-laravel/README.md`](tp10-laravel/README.md).

---

## TP10 — Refactorización a MVC e implementación con framework

- **Parte A — MVC nativo:** el módulo de turnos (solicitud pública y CRUD del panel) quedó organizado en
  `app/Models/` (consultas PDO preparadas), `app/Controllers/` (request, validación, CSRF y redirecciones) y
  `app/Views/` (HTML escapado). `turnos.php` y `admin/*.php` son front controllers de pocas líneas.
- **Parte B — Laravel:** catálogo de servicios en Laravel 12 (`tp10-laravel/`) con routing, controlador,
  modelo Eloquent, vistas Blade y una API JSON de solo lectura.

Desarrollo completo, decisiones y pruebas en [`docs/tp10/README.md`](docs/tp10/README.md) y
[`tp10-laravel/README.md`](tp10-laravel/README.md).

---

## TP6 — Diseño y creación del esquema relacional en MySQL

Desarrollo completo en [`tp6/NORMALIZACION.md`](tp6/NORMALIZACION.md).

- **Parte 1 — Normalización:** se parte de una planilla de turnos con servicios y precios en
  una sola celda y datos de cliente/vehículo repetidos, y se lleva a 1FN, 2FN y 3FN.
- **Parte 2 — [`tp6/schema.sql`](tp6/schema.sql):** 8 tablas InnoDB con PK `AUTO_INCREMENT`,
  FK con `ON DELETE RESTRICT/CASCADE`, `UNIQUE`, `CHECK`, auditoría `created_at`/`updated_at`
  e índices explícitos (`CREATE INDEX`) sobre `turnos(fecha)`, `turnos(estado_id, fecha)` y
  `usuarios(apellido, nombre)`.
- **Parte 3 — [`tp6/consultas.sql`](tp6/consultas.sql):** `INSERT` en maestras y tabla
  principal, `SELECT` con `WHERE` + `ORDER BY`, `INNER JOIN` de 5 tablas, `UPDATE` y `DELETE`
  con `WHERE`.

| Tipo | Tablas |
|------|--------|
| Usuarios | `usuarios` (email, password_hash, rol, datos personales) |
| Maestras | `roles`, `estados_turno`, `marcas`, `servicios` |
| Negocio | `vehiculos`, `turnos` |
| Relación N:M | `turno_servicio` (servicios de cada turno con su precio) |

---

## TP8 — Manejo de estado, sesiones y autenticación

Usuarios sobre la tabla `usuarios` del TP6 (`email` UNIQUE, `password_hash` VARCHAR(255), rol por FK a
`roles`: *Administrador* / *Cliente*, `created_at`). Admin de prueba: `admin@boxly.com.ar` / `Boxly2026!`.

| Archivo | Función |
|---------|---------|
| `registro.php` | Alta de cliente: sanitización (`trim` + `htmlspecialchars` en la salida), validación en servidor, email no repetido y `password_hash($password, PASSWORD_BCRYPT)` con consulta preparada. |
| `login.php` | `password_verify()` contra el hash; si es correcto `session_regenerate_id(true)` (anti Session Fixation) y guarda `$_SESSION['usuario_id']`, `['usuario_nombre']` y `['rol']`. Admin → `admin/`, cliente → `mi-cuenta.php`. Mensaje de error genérico. |
| `includes/auth_guard.php` | Guardián reutilizable: sin `$_SESSION['usuario_id']` redirige a `login.php?error=acceso_denegado`; con `$rolesPermitidos` restringe por rol. |
| `logout.php` | `$_SESSION = []`, borra la cookie de sesión, `session_destroy()` y redirige al login. Solo por POST con token CSRF. |
| `includes/sesion.php` | Cookie de sesión `HttpOnly`, `SameSite=Lax`, `Secure` bajo HTTPS, `use_strict_mode` y expiración por 30 min de inactividad. |

**Vistas protegidas:** `mi-cuenta.php` (cualquier usuario logueado: sus datos y sus turnos) y todo
`admin/` (solo rol *Administrador*; un cliente es redirigido con aviso de falta de permisos). Con sesión
iniciada, `turnos.php` precarga los datos y registra el turno a nombre de la cuenta.

---

## TP7 — Persistencia y conexión PHP-MySQL segura

Entidad gestionada: **`turnos`** del taller. Trabaja sobre el esquema normalizado del TP6:
cada turno referencia a un cliente (`usuarios`), un vehículo (`vehiculos`), un estado
(`estados_turno`) y uno o más servicios (`turno_servicio`). El flujo completo queda integrado al sitio:

- El cliente pide un turno desde **`turnos.php`** → se valida en servidor y se guarda en MySQL.
- El taller lo gestiona desde **`admin/`**: lo lista, filtra por estado, edita (p. ej. pasa de
  *Pendiente* a *Confirmado*) o elimina.

### Seguridad aplicada

| Requisito | Implementación |
|-----------|----------------|
| Conexión segura | `config/conexion.php`: PDO con `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES = false` y `try/catch (PDOException)`. El detalle del error va a `error_log`, no al navegador. Credenciales fuera del repo. |
| SQL Injection | **Todas** las consultas (`SELECT`, `INSERT`, `UPDATE`, `DELETE`) usan `prepare()` + `execute()` con parámetros (en el TP7 `includes/turnos_db.php`; desde el TP10 los modelos de `app/Models/`). Los `id` se validan con `FILTER_VALIDATE_INT`. |
| XSS | Toda variable impresa pasa por `e()` → `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. |
| Validación | `validarTurno()` en servidor (desde el TP10, `Turno::validar()` en `app/Models/Turno.php`): obligatorios, largo, email, regex de teléfono y patente, año, fecha válida, UTF-8 válido y listas blancas: marca, servicios y estado se validan contra los catálogos de la base. |
| Integridad | Alta y edición corren en una **transacción** (usuario → vehículo → turno → servicios): si algo falla se hace `rollBack()`. Desde el sitio público un email o patente existentes se reutilizan sin modificar sus datos. |
| CSRF | Alta, edición y baja exigen un token de sesión (`csrfToken()` / `csrfValido()`). La baja solo acepta `POST`: un link GET no puede borrar registros. |

### Modelo de datos

Ver el esquema completo y el diagrama ER en [`tp6/NORMALIZACION.md`](tp6/NORMALIZACION.md).

---

## TP5 — Procesamiento de formularios y seguridad web (PHP)

El sitio completo en **PHP** con estructura modular (`includes/header.php`, `includes/footer.php`,
`includes/funciones.php`) y lógica de servidor para capturar y procesar datos del navegador.

### Formulario POST — Solicitud de turno (`turnos.php`)

- Sanitización de `$_POST` con `trim()` + `htmlspecialchars()` (helpers `limpiar()` y `e()`) → previene **XSS**.
- Validación en servidor: obligatorios, teléfono con regex, fecha válida y no pasada, listas
  blancas y **formato de email** con `filter_var(..., FILTER_VALIDATE_EMAIL)` en `contacto.php` y `presupuesto.php`.
- Retroalimentación con `alert-success` / `alert-danger` + `is-invalid` / `invalid-feedback` por campo.
- Persistencia de los valores cargados (escapados) ante errores.

### Formulario GET — Buscador de servicios (`servicios.php?q=`)

Filtra el catálogo en servidor con `filter_input(INPUT_GET, ...)` y `mb_stripos()`; todo resultado
se imprime escapado.

### Capturas

**POST — Errores de validación y persistencia de campos**

![POST con errores de validación y campos persistidos](docs/tp5/screenshots/post-errores.png)

**POST — Envío exitoso con resumen de datos procesados**

![POST exitoso con resumen de datos sanitizados](docs/tp5/screenshots/post-exito.png)

**GET — Buscador con resultados filtrados** (`servicios.php?q=Frenos`)

![Buscador GET con resultados filtrados](docs/tp5/screenshots/get-busqueda.png)

**GET — Búsqueda sin coincidencias** (`servicios.php?q=xys`)

![GET sin coincidencias](docs/tp5/screenshots/get-sin-resultados.png)

**XSS neutralizado** — el `<script>` se imprime como texto, no se ejecuta

![Intento de script sanitizado en la salida](docs/tp5/screenshots/xss-sanitizado.png)

---

## TP3 / TP4 — Mockups y maquetación

- Mockups UI/UX en Figma: [`docs/tp3/mockups/`](docs/tp3/mockups/) · wireframe: [`docs/tp3/wireframe-figma-taller.png`](docs/tp3/wireframe-figma-taller.png)
- Maquetación estática de las 10 vistas (HTML + Bootstrap): [`entregas/tp4-html/`](entregas/tp4-html/) · auditoría: [`auditoria-lighthouse.md`](entregas/tp4-html/auditoria-lighthouse.md)

## Alumno

Chocobar Antonio  
Tecnicatura Superior en Análisis de Sistemas y Desarrollo de Software — IES N° 6001
