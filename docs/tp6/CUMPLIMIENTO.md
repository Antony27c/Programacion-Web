# TP6 — Cumplimiento: diseño y creación del esquema relacional en MySQL

Auditoría del enunciado del TP6 contra los archivos de `tp6/` y la base `boxly`.

Estados: **OK** = ya cumplía · **ARREGLADO** = se corrigió en esta revisión · **FALTA** = pendiente.

## Tabla de requisitos

### Parte 1 — Normalización

| # | Requisito del enunciado | Archivo | Estado | Detalle |
|---|---|---|---|---|
| N1 | Tabla sin normalizar con campos multivalorados o redundantes | `tp6/NORMALIZACION.md` §1 | OK | Planilla de turnos con `servicios`/`precios` en una celda y datos de cliente y vehículo repetidos, con anomalías de actualización/inserción/borrado. |
| N2 | 1FN: valores atómicos, sin grupos repetitivos | `tp6/NORMALIZACION.md` §2.1 | OK | Se separan campos compuestos (cliente → nombre/apellido, vehículo → marca/modelo/año/patente) y el grupo repetitivo servicios/precios pasa a una fila por servicio de cada turno. |
| N3 | 2FN: sin dependencias parciales sobre la PK | `tp6/NORMALIZACION.md` §2.2 | OK | Se descompone en `turnos`, `servicios` y `turno_servicio`; se documentan las dependencias parciales detectadas. |
| N4 | 3FN: sin dependencias transitivas | `tp6/NORMALIZACION.md` §2.3 | OK | Se extraen `usuarios`, `roles`, `vehiculos`, `marcas` y `estados_turno`; incluye tabla de dependencias transitivas y decisiones de diseño. |
| N5 | Listado de tablas con PK y FK + diagrama | `tp6/NORMALIZACION.md` §3 | OK | Tabla resumen de las 8 tablas con PK/FK/índices y diagrama entidad-relación en Mermaid con cardinalidades. |

### Parte 2 — DDL (`tp6/schema.sql`)

| # | Requisito del enunciado | Archivo | Estado | Detalle |
|---|---|---|---|---|
| D1 | `CREATE DATABASE IF NOT EXISTS` + `USE` | `tp6/schema.sql` | OK | Crea `boxly` con `CHARACTER SET utf8mb4` / `utf8mb4_unicode_ci` y hace `USE boxly`. |
| D2 | Tabla de usuarios: email, password, roles, datos personales | `tp6/schema.sql` → `usuarios` | OK | `email` UNIQUE, `password_hash` (nullable para clientes web), `rol_id` FK a `roles`, nombre/apellido/teléfono. |
| D3 | Tabla secundaria/maestra | `tp6/schema.sql` → `roles`, `estados_turno`, `marcas`, `servicios` | OK | Catálogos con `nombre` UNIQUE; `servicios` suma `descripcion` y `precio_base`. |
| D4 | Tabla principal de negocio | `tp6/schema.sql` → `turnos`, `vehiculos` | OK | `turnos` (fecha, horario, sintoma + FK a usuario/vehículo/estado) y `vehiculos` (modelo, año, patente UNIQUE). |
| D5 | Tabla de relación N:M | `tp6/schema.sql` → `turno_servicio` | OK | Resuelve turno ↔ servicio con `UNIQUE(turno_id, servicio_id)` y `precio` congelado por turno. |
| D6 | PK autoincrementales `INT AUTO_INCREMENT PRIMARY KEY` | `tp6/schema.sql` | OK | Las 8 tablas usan `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`. |
| D7 | Claves foráneas con integridad referencial | `tp6/schema.sql` | OK | 8 constraints `FOREIGN KEY ... REFERENCES` con `ON UPDATE CASCADE` y `ON DELETE RESTRICT` (o `CASCADE` en el detalle). |
| D8 | Auditoría `created_at` / `updated_at` en tablas principales | `tp6/schema.sql` | OK | Presente en `servicios`, `usuarios`, `vehiculos` y `turnos`. `turno_servicio` es tabla de detalle N:M y no la lleva (decisión documentada en NORMALIZACION.md). |
| D9 | Índice explícito `CREATE INDEX` sobre campo de alta cardinalidad | `tp6/schema.sql` | OK | `idx_turnos_fecha`, `idx_turnos_estado_fecha` y `idx_usuarios_apellido_nombre` (email y patente ya quedan indexados por sus UNIQUE). |
| D10 | Restricciones adicionales de dominio | `tp6/schema.sql` | OK | `CHECK (precio_base >= 0)` y `CHECK (precio >= 0)`; `horario` como `ENUM('Mañana','Tarde')`. |

### Parte 3 — DML (`tp6/consultas.sql`)

| # | Requisito del enunciado | Archivo | Estado | Detalle |
|---|---|---|---|---|
| Q1 | ≥2 INSERT en tablas maestras/secundarias | `tp6/consultas.sql` §1.a | OK | 2 roles, 4 estados, 7 marcas y 4 servicios. |
| Q2 | ≥2 INSERT en tabla principal vinculando FKs | `tp6/consultas.sql` §1.b–1.e | OK | 6 usuarios, 5 vehículos, 5 turnos y sus servicios N:M, resolviendo FK por subconsultas. |
| Q3 | SELECT con `WHERE` + `ORDER BY` | `tp6/consultas.sql` §2.a–2.b | OK | Agenda filtrada por estado/fecha y servicios ordenados por precio. |
| Q4 | SELECT con `INNER JOIN` de ≥2 tablas | `tp6/consultas.sql` §2.c–2.e | OK | JOIN de 5 tablas (turno + cliente + vehículo + marca + estado), JOIN con la tabla N:M y JOIN usuario↔rol. |
| Q5 | `UPDATE` con `WHERE` | `tp6/consultas.sql` §3 | OK | Cambio de estado de un turno y actualización de `precio_base`, ambos por `WHERE`. |
| Q6 | `DELETE` con `WHERE` | `tp6/consultas.sql` §4 | OK | Baja del turno de prueba; verifica `ON DELETE CASCADE` en `turno_servicio` (ambas cuentas en 0). |

### Criterios de entrega

| # | Requisito del enunciado | Archivo | Estado | Detalle |
|---|---|---|---|---|
| C1 | Archivos en `tp6/` del repositorio | `tp6/` | OK | `NORMALIZACION.md`, `schema.sql` y `consultas.sql`. |
| C2 | Los scripts se ejecutan sin errores en local | `tp6/schema.sql`, `tp6/consultas.sql` | OK | Verificado en MySQL 8 sobre una copia `boxly_audit` (ver Evidencia). |

## Evidencia

### Ejecución de los scripts (copia `boxly_audit`, MySQL 8)

Los scripts se copiaron a un directorio temporal cambiando únicamente el nombre de la
base (`boxly` → `boxly_audit`) para no tocar la base real; al final se eliminó.

```
$ mysql -u root -p < schema_audit.sql     → EXIT 0, sin errores
$ mysql -u root -p < consultas_audit.sql  → EXIT 0, sin errores
```

Resultados relevantes de `consultas.sql`:

```
# SELECT agenda (WHERE estado IN (...) AND fecha >= ... ORDER BY fecha, horario)
1  2026-11-10  Mañana  Service de los 60.000 km...
2  2026-11-11  Tarde   Chirrido al frenar...
3  2026-11-12  Mañana  Se encendió la luz de check engine...
6  2026-11-20  Tarde   REGISTRO DE PRUEBA: turno duplicado por error.

# INNER JOIN 5 tablas: turno | fecha | cliente | vehículo | patente | estado
6  2026-11-20  Cruz, Sofía        Fiat Cronos 2022   AF789HJ  Pendiente
5  2026-11-14  Ríos, Valentina    Renault Sandero 2017 MNO654 Cancelado
3  2026-11-12  Cruz, Sofía        Fiat Cronos 2022   AF789HJ  Pendiente
2  2026-11-11  Guaymás, Martín    Toyota Hilux 2021   AE456FG  Pendiente
1  2026-11-10  Fernández, Lucía   Volkswagen Gol 2018  AC123BD  Confirmado
4  2026-11-03  Mamaní, Diego      Chevrolet Onix 2020   AD321KL  Finalizado

# JOIN con tabla N:M (servicios + total por turno)
5  Valentina Ríos   Frenos, Service   105000.00
2  Martín Guaymás   Diagnóstico, Frenos 85000.00
1  Lucía Fernández  Service            45000.00
3  Sofía Cruz       Diagnóstico        25000.00
6  Sofía Cruz       Diagnóstico        25000.00
4  Diego Mamaní     Otro               0.00

# Verificación UPDATE (estado del turno de AE456FG)
2  2026-11-11  Confirmado

# Verificación DELETE (turno de prueba + sus servicios por CASCADE)
turnos_restantes  servicios_restantes
0                0
```

## Observaciones

- `turno_servicio` no tiene `created_at`/`updated_at`: el enunciado pide auditoría en las
  "tablas principales" y esta es la tabla de detalle N:M; el precio congelado ya registra
  el dato relevante.
- `password_hash` admite `NULL` para que los clientes que piden turno desde la web queden
  registrados sin acceso hasta crear su contraseña (documentado en NORMALIZACION.md).
