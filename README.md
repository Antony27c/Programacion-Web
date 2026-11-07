# BOXLY — Programación Web

Sitio del taller **BOXLY** (Salta): servicio mecánico automotor. Maquetación HTML5 + Bootstrap 5,
backend en PHP y persistencia en MySQL mediante PDO.

## Enlaces

- **Repositorio:** https://github.com/Antony27c/Programacion-Web
- **Mockups Figma:** https://www.figma.com/design/1MKE3Skw1MhIkSsq2L2091/Taller-Mecanico-Mockups-UI-UX-TP3
- **Versión estática (TP4) en GitHub Pages:** https://antony27c.github.io/Programacion-Web/entregas/tp4-html/

## Vista previa

![Portada de BOXLY](docs/capturas/portada.png)

| Panel de administración | Celular |
|---|---|
| ![Panel de turnos](docs/capturas/panel-admin.png) | ![Portada en el celular](docs/capturas/portada-movil.png) |

Cada TP tiene sus propias capturas en su README (ver la tabla de abajo).

## Trabajos prácticos

Cada TP tiene su propio README con el objetivo según el enunciado, lo implementado, los archivos
principales, cómo probarlo y las pruebas realizadas. La columna *Rama* indica dónde quedó el trabajo
de cada TP: los iniciales (TP3 a TP5) en `main` y el resto en su rama `feature/tpN-...`.

| TP   | Tema                                              | Rama                  | README |
|------|---------------------------------------------------|-----------------------|--------|
| TP3  | Diseño UX: wireframe y mockups en Figma           | `main`                | [`docs/tp3/README.md`](docs/tp3/README.md) |
| TP4  | Maquetación estática HTML5 + Bootstrap            | `main`                | [`entregas/tp4-html/README.md`](entregas/tp4-html/README.md) |
| TP5  | Formularios POST/GET y seguridad web              | `main`                | [`docs/tp5/README.md`](docs/tp5/README.md) |
| TP6  | Esquema relacional en MySQL                       | `feature/tp6-esquema` | [`docs/tp6/README.md`](docs/tp6/README.md) |
| TP7  | Persistencia PDO y CRUD de turnos                 | `feature/tp7-crud`    | [`docs/tp7/README.md`](docs/tp7/README.md) |
| TP8  | Sesiones, autenticación y roles                   | `feature/tp8-auth`    | [`docs/tp8/README.md`](docs/tp8/README.md) |
| TP9  | Comunicación asincrónica AJAX + JSON              | `feature/tp9-ajax`    | [`docs/tp9/README.md`](docs/tp9/README.md) |
| TP10 | Refactor MVC y módulo en Laravel                  | `feature/tp10-mvc`    | [`docs/tp10/README.md`](docs/tp10/README.md) · [`tp10-laravel/README.md`](tp10-laravel/README.md) |

- **TP3** — Wireframe y mockups UI/UX de las vistas del sitio en Figma (`docs/tp3/`).
- **TP4** — Maquetación estática de las 10 vistas en HTML5 + Bootstrap 5 con auditoría Lighthouse (`entregas/tp4-html/`).
- **TP5** — Sitio en PHP con estructura modular: formularios POST (turnos, contacto, presupuesto) y GET
  (buscador de servicios), con sanitización anti-XSS y validación en servidor.
- **TP6** — Modelo relacional del taller normalizado (1FN a 3FN): 8 tablas InnoDB con PK, FK, índices
  y auditoría; `tp6/schema.sql` + `tp6/consultas.sql` y el análisis en `tp6/NORMALIZACION.md`.
- **TP7** — Conexión PDO segura con `try/catch` y CRUD de turnos integrado al sitio (`admin/`),
  con consultas preparadas, transacciones y protección CSRF.
- **TP8** — Registro, login y logout con sesiones seguras (`HttpOnly`, `SameSite`, regeneración de id),
  `password_hash`/`password_verify` y control de acceso por roles (Administrador / Cliente).
- **TP9** — Endpoints JSON en `api/` consumidos con Fetch API: búsqueda de servicios en vivo,
  consulta pública del estado de un turno y cambio de estado desde el panel admin, todo sin recargar la página.
- **TP10** — Refactor del módulo de turnos a arquitectura MVC nativa en `app/` (Models, Controllers,
  Views con PHP puro) y módulo de catálogo de servicios en Laravel 12 dentro de `tp10-laravel/`.

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
│   ├── Models/                      ← Catalogo, Turno, Usuario, Vehiculo y Servicio (TP9) con SQL preparado
│   ├── Controllers/                 ← TurnoController (CRUD admin) y SolicitudTurnoController (turno público)
│   └── Views/turnos/                ← index, form, solicitar y _campos (HTML escapado)
├── api/                             ← TP9: endpoints JSON para Fetch API (usan los modelos de app/)
│   ├── buscar_servicios.php         ← GET ?q= → servicios que coinciden (200/400/405/500)
│   ├── consultar_turno.php          ← GET ?id&patente → estado de un turno (200/400/404/405/500)
│   └── turno_estado.php             ← POST JSON + X-CSRF-Token → cambio de estado (solo admin)
├── includes/
│   ├── header.php  footer.php       ← plantilla modular (footer carga el JS de cada página)
│   ├── funciones.php                ← sanitización, listas blancas, CSRF
│   ├── auth_guard.php               ← guardián de sesión y roles (TP8)
│   ├── sesion.php                   ← cookie de sesión segura y expiración (TP8)
│   ├── usuarios_db.php              ← acceso a datos de usuarios (TP8)
│   └── api.php                      ← TP9: helpers JSON (responderJson, errorJson, exigirMetodo)
├── login.php registro.php logout.php mi-cuenta.php   ← TP8: autenticación
├── admin/                           ← CRUD de turnos (TP7) como front controllers MVC (TP10), solo rol Administrador
│   ├── index.php                    ← listado (Read) + filtro por estado
│   ├── crear.php                    ← alta (Create)
│   ├── editar.php                   ← modificación (Update)
│   └── eliminar.php                 ← baja (Delete)
├── turnos.php                       ← solicitud pública de turno (front controller MVC)
├── index.php nosotros.php servicios.php servicio-detalle.php
├── presupuesto.php testimonios.php galeria.php contacto.php faq.php
├── css/  img/
├── js/
│   ├── main.js                      ← comportamiento general del sitio
│   ├── servicios.js                 ← TP9: búsqueda en vivo de servicios (Fetch + JSON)
│   ├── turnos.js                    ← TP9: consulta de estado de turno sin recargar
│   └── admin-turnos.js              ← TP9: cambio de estado de turnos desde el panel
├── entregas/
│   └── tp4-html/                    ← TP4: sitio estático HTML + auditoría Lighthouse
├── tp10-laravel/                    ← TP10: catálogo de servicios en Laravel 12 (base `boxly_laravel`)
└── docs/
    ├── DISENO.md                    ← guía de estilo del rediseño (paleta, tipografía, componentes)
    ├── capturas/                    ← vista previa del sitio para este README
    ├── tp3/                         ← README, wireframe, informe TP3 y mockups Figma
    ├── tp5/                         ← README, cumplimiento, capturas/ y screenshots/ del TP5
    ├── tp6/  tp7/  tp8/             ← README, cumplimiento y capturas/ de cada TP
    ├── tp9/                         ← TP9: README, capturas/, pruebas de la API (probar_api.sh) y resultados (PRUEBAS.md)
    └── tp10/                        ← TP10: README y capturas/ del refactor MVC y del módulo Laravel
```

## Tecnología

- HTML5 semántico, Bootstrap 5.3 (CDN), CSS propio con variables (sistema de diseño BOXLY)
- Tipografías Plus Jakarta Sans e Inter, íconos e ilustraciones SVG propios
- PHP 8 con arquitectura MVC propia (TP10) y Laravel 12 para el módulo de servicios
- MySQL 8 + PDO (consultas preparadas)
- Prettier

## Diseño

El sitio tiene un rediseño completo pensado para que sea más amigable: portada con llamadas a la
acción claras ("Sacar turno", "Pedir presupuesto"), formularios por pasos con campos obligatorios
marcados y errores visibles, panel de administración con resumen por estado y buena versión para el
celular. Todos los colores cumplen contraste WCAG AA. El detalle está en
[`docs/DISENO.md`](docs/DISENO.md).

| Token | Hex | Uso |
|-------|-----|-----|
| Ink | `#0F172A` | Barra superior, hero, footer |
| Texto | `#1E293B` | Tipografía principal |
| Azul de marca | `#0052FF` | Botones, links, íconos |
| Texto secundario | `#475569` | Descripciones y ayudas |
| Arena | `#FBF7F0` | Secciones alternadas cálidas |
| Fondo | `#F8FAFC` | Superficies |
| Éxito | `#047857` | Confirmaciones |
| Error | `#B91C1C` | Errores de formulario |

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
5. **API JSON (TP9):** con el servidor levantado, correr las pruebas con curl de los endpoints
   (inicia sesión como administrador para los casos de `turno_estado.php`):

   ```bash
   bash docs/tp9/probar_api.sh 8099
   ```

6. **Módulo Laravel (TP10, opcional):** usa su propia base `boxly_laravel` (crearla vacía antes de migrar).

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

## TP9 — Comunicación asincrónica con AJAX y JSON

Endpoints PHP independientes en `api/` que responden JSON (consultas preparadas en los modelos de `app/Models/`,
`json_encode()` y códigos HTTP correctos) y se consumen con Fetch API y `async/await`: búsqueda de
servicios en vivo, consulta del estado de un turno y cambio de estado desde el panel admin, todo
sin recargar la página. Los resultados se arman en el DOM con `textContent` (sin `innerHTML`).
Detalle completo en [`docs/tp9/README.md`](docs/tp9/README.md) y resultados de las pruebas en [`docs/tp9/PRUEBAS.md`](docs/tp9/PRUEBAS.md).

| Endpoint | Método | Uso | Códigos |
|----------|--------|-----|---------|
| `api/buscar_servicios.php?q=` | GET | Buscador en vivo de `servicios.php` (`js/servicios.js`) | 200 · 400 · 405 · 500 |
| `api/consultar_turno.php?id=&patente=` | GET | Panel «Consultá tu turno» de `turnos.php` (`js/turnos.js`) | 200 · 400 · 404 · 405 · 500 |
| `api/turno_estado.php` | POST (JSON + `X-CSRF-Token`) | Estado de cada turno en `admin/index.php` (`js/admin-turnos.js`), solo Administrador | 200 · 400 · 401 · 403 · 404 · 405 · 415 · 500 |

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
| CSRF | Alta, edición y baja (y la solicitud pública de `turnos.php`) exigen un token de sesión (`csrfToken()` / `csrfValido()`). La baja solo acepta `POST`: un link GET no puede borrar registros. |

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
