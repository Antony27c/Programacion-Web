# TP6 — Diseño y creación del esquema relacional en MySQL

## Objetivo

Modelar la base de datos del proyecto integrador (BOXLY, sistema de turnos para un taller
mecánico) aplicando normalización formal (1FN, 2FN y 3FN), y entregar el DDL (`schema.sql`)
y las consultas DML de prueba (`consultas.sql`) ejecutables en MySQL.

## Qué se hizo

### Parte 1 — Normalización

Partí de una "planilla de turnos" con datos multivalorados (`servicios` y `precios` en una
sola celda) y redundancia (los datos del cliente y del vehículo se repetían en cada turno).
El análisis completo está en [`tp6/NORMALIZACION.md`](../../tp6/NORMALIZACION.md):

- **1FN:** separé los campos compuestos en columnas atómicas y eliminé el grupo repetitivo:
  quedó una fila por cada servicio de cada turno.
- **2FN:** descompuse esa tabla en `turnos`, `servicios` y la relación N:M `turno_servicio`
  (con el precio cobrado en ese turno, que no depende del precio de lista).
- **3FN:** extraje las dependencias transitivas a catálogos propios: `usuarios`, `roles`,
  `vehiculos`, `marcas` y `estados_turno`.

Resultado: **8 tablas** (`roles`, `estados_turno`, `marcas`, `servicios`, `usuarios`,
`vehiculos`, `turnos`, `turno_servicio`) con PK sustitutas `AUTO_INCREMENT`, claves
candidatas como `UNIQUE` (`email`, `patente`, nombres de catálogo) y un diagrama ER.

### Parte 2 — DDL (`tp6/schema.sql`)

Script que crea la base `boxly` (utf8mb4) y las 8 tablas InnoDB con:

- PK `INT UNSIGNED AUTO_INCREMENT` en todas las tablas.
- 8 constraints `FOREIGN KEY` con `ON UPDATE CASCADE` y `ON DELETE RESTRICT`
  (`turno_servicio.turno_id` usa `CASCADE`: al borrar un turno se borran sus servicios).
- Auditoría `created_at` / `updated_at` en las tablas principales.
- `CHECK` de precios no negativos y `ENUM('Mañana','Tarde')` para el horario.
- Índices explícitos: `turnos(fecha)`, `turnos(estado_id, fecha)` y
  `usuarios(apellido, nombre)`.

### Parte 3 — DML (`tp6/consultas.sql`)

Datos de prueba y validación CRUD sobre el esquema:

- `INSERT` en las 4 maestras, 6 usuarios, 5 vehículos, 5 turnos y sus servicios N:M
  (las FK se resuelven por subconsultas sobre email/patente/nombre).
- `SELECT` con `WHERE` + `ORDER BY`, `INNER JOIN` de hasta 5 tablas (agenda completa con
  cliente, vehículo, marca y estado) y un JOIN con `turno_servicio` para totales.
- `UPDATE` de estado y de precio con `WHERE`, y `DELETE` del turno de prueba verificando
  el borrado en cascada.

## Archivos

| Archivo | Contenido |
|---------|-----------|
| [`NORMALIZACION.md`](../../tp6/NORMALIZACION.md) | Parte 1: tabla sin normalizar, 1FN/2FN/3FN, tablas resultantes y diagrama ER |
| [`schema.sql`](../../tp6/schema.sql) | Parte 2: DDL de la base `boxly` |
| [`consultas.sql`](../../tp6/consultas.sql) | Parte 3: DML de prueba |

## Cómo probarlo

1. Ejecutar en orden `tp6/schema.sql` y `tp6/consultas.sql` en MySQL
   (phpMyAdmin, Workbench o `mysql -u root -p < tp6/schema.sql`).
2. Los scripts corren sin errores: verificado sobre MySQL 8 en una copia de la base.
3. El sitio PHP (`turnos.php` y `admin/`) ya trabaja sobre este esquema.

## Decisiones tomadas

- **`turnos.usuario_id` ≠ `vehiculos.usuario_id`:** quien solicita el turno no siempre es el
  dueño del vehículo (alguien puede llevar el auto de un familiar), así que el solicitante
  no se deriva del vehículo.
- **`password_hash` nullable:** los clientes que piden turno desde la web quedan
  registrados sin contraseña hasta que creen su cuenta.
- **`turno_servicio.precio`:** guarda el precio pactado en ese turno; si luego cambia
  `servicios.precio_base`, los turnos ya cargados no se modifican.
- **`ON DELETE RESTRICT`** en las FK principales para no perder clientes, vehículos,
  servicios ni estados que tengan turnos asociados.
