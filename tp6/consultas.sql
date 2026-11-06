-- ============================================================
-- BOXLY — Consultas de prueba CRUD (TP6)
-- Ejecutar DESPUÉS de schema.sql (necesita las tablas vacías)
-- ============================================================
SET NAMES utf8mb4;
USE boxly;

-- ============================================================
-- 1) CREATE — Datos iniciales
-- ============================================================

-- 1.a) Tablas maestras / secundarias
INSERT INTO roles (nombre) VALUES
('Administrador'),
('Cliente');

INSERT INTO estados_turno (nombre) VALUES
('Pendiente'),
('Confirmado'),
('Finalizado'),
('Cancelado');

INSERT INTO marcas (nombre) VALUES
('Chevrolet'), ('Fiat'), ('Ford'), ('Peugeot'),
('Renault'), ('Toyota'), ('Volkswagen');

INSERT INTO servicios (nombre, descripcion, precio_base) VALUES
('Service',     'Cambio de aceite, filtros y control de fluidos.',          45000.00),
('Frenos',      'Revisión y cambio de pastillas, discos y líquido de freno.', 60000.00),
('Diagnóstico', 'Escaneo computarizado y revisión de testigos del tablero.', 25000.00),
('Otro',        'Trabajo a cotizar según inspección.',                          0.00);

-- 1.b) Usuarios (la FK rol_id se resuelve por nombre de rol)
-- Contraseña de prueba del administrador: Boxly2026!
INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono) VALUES
((SELECT id FROM roles WHERE nombre = 'Administrador'), 'Antonio',  'Chocobar',  'admin@boxly.com.ar',
 '$2y$10$YA7rAi/Mb06qyYlH8lTV0etXE8bC1Q/MfWTpcoXOvUUqTexLtM39y', '+54 387 400-0000'),
((SELECT id FROM roles WHERE nombre = 'Cliente'), 'Lucía',     'Fernández', 'lucia.fernandez@gmail.com', NULL, '+54 387 455-1020'),
((SELECT id FROM roles WHERE nombre = 'Cliente'), 'Martín',    'Guaymás',   'martin.guaymas@gmail.com',  NULL, '+54 387 412-3344'),
((SELECT id FROM roles WHERE nombre = 'Cliente'), 'Sofía',     'Cruz',      'sofia.cruz@hotmail.com',    NULL, '387 15 600-7788'),
((SELECT id FROM roles WHERE nombre = 'Cliente'), 'Diego',     'Mamaní',    'diego.mamani@gmail.com',    NULL, '+54 387 499-0011'),
((SELECT id FROM roles WHERE nombre = 'Cliente'), 'Valentina', 'Ríos',      'valen.rios@yahoo.com',      NULL, '+54 387 433-5566');

-- 1.c) Vehículos (FK a usuarios y marcas)
INSERT INTO vehiculos (usuario_id, marca_id, modelo, anio, patente) VALUES
((SELECT id FROM usuarios WHERE email = 'lucia.fernandez@gmail.com'), (SELECT id FROM marcas WHERE nombre = 'Volkswagen'), 'Gol',     2018, 'AC123BD'),
((SELECT id FROM usuarios WHERE email = 'martin.guaymas@gmail.com'),  (SELECT id FROM marcas WHERE nombre = 'Toyota'),     'Hilux',   2021, 'AE456FG'),
((SELECT id FROM usuarios WHERE email = 'sofia.cruz@hotmail.com'),    (SELECT id FROM marcas WHERE nombre = 'Fiat'),       'Cronos',  2022, 'AF789HJ'),
((SELECT id FROM usuarios WHERE email = 'diego.mamani@gmail.com'),    (SELECT id FROM marcas WHERE nombre = 'Chevrolet'),  'Onix',    2020, 'AD321KL'),
((SELECT id FROM usuarios WHERE email = 'valen.rios@yahoo.com'),      (SELECT id FROM marcas WHERE nombre = 'Renault'),    'Sandero', 2017, 'MNO654');

-- 1.d) Tabla principal: turnos (FK a usuarios, vehiculos y estados_turno)
INSERT INTO turnos (usuario_id, vehiculo_id, estado_id, fecha, horario, sintoma) VALUES
((SELECT id FROM usuarios WHERE email = 'lucia.fernandez@gmail.com'), (SELECT id FROM vehiculos WHERE patente = 'AC123BD'),
 (SELECT id FROM estados_turno WHERE nombre = 'Confirmado'), '2026-11-10', 'Mañana', 'Service de los 60.000 km, cambio de aceite y filtros.'),
((SELECT id FROM usuarios WHERE email = 'martin.guaymas@gmail.com'), (SELECT id FROM vehiculos WHERE patente = 'AE456FG'),
 (SELECT id FROM estados_turno WHERE nombre = 'Pendiente'), '2026-11-11', 'Tarde', 'Chirrido al frenar y el pedal se siente esponjoso.'),
((SELECT id FROM usuarios WHERE email = 'sofia.cruz@hotmail.com'), (SELECT id FROM vehiculos WHERE patente = 'AF789HJ'),
 (SELECT id FROM estados_turno WHERE nombre = 'Pendiente'), '2026-11-12', 'Mañana', 'Se encendió la luz de check engine y pierde potencia.'),
((SELECT id FROM usuarios WHERE email = 'diego.mamani@gmail.com'), (SELECT id FROM vehiculos WHERE patente = 'AD321KL'),
 (SELECT id FROM estados_turno WHERE nombre = 'Finalizado'), '2026-11-03', 'Tarde', 'Ruido metálico en la suspensión delantera en calles de ripio.'),
((SELECT id FROM usuarios WHERE email = 'valen.rios@yahoo.com'), (SELECT id FROM vehiculos WHERE patente = 'MNO654'),
 (SELECT id FROM estados_turno WHERE nombre = 'Cancelado'), '2026-11-14', 'Mañana', 'Revisión general antes de un viaje largo a Cafayate.');

-- 1.e) Tabla de relación N:M: servicios de cada turno (precio congelado al momento)
INSERT INTO turno_servicio (turno_id, servicio_id, precio)
SELECT t.id, s.id, s.precio_base
FROM turnos t
INNER JOIN vehiculos v ON v.id = t.vehiculo_id
CROSS JOIN servicios s
WHERE (v.patente, s.nombre) IN (
    ('AC123BD', 'Service'),
    ('AE456FG', 'Frenos'), ('AE456FG', 'Diagnóstico'),
    ('AF789HJ', 'Diagnóstico'),
    ('AD321KL', 'Otro'),
    ('MNO654',  'Service'), ('MNO654', 'Frenos')
);

-- 1.f) Registro de prueba que se elimina en el punto 4
INSERT INTO turnos (usuario_id, vehiculo_id, estado_id, fecha, horario, sintoma) VALUES
((SELECT id FROM usuarios WHERE email = 'sofia.cruz@hotmail.com'), (SELECT id FROM vehiculos WHERE patente = 'AF789HJ'),
 (SELECT id FROM estados_turno WHERE nombre = 'Pendiente'), '2026-11-20', 'Tarde', 'REGISTRO DE PRUEBA: turno duplicado por error.');
SET @turno_prueba = LAST_INSERT_ID();

INSERT INTO turno_servicio (turno_id, servicio_id, precio)
SELECT @turno_prueba, id, precio_base FROM servicios WHERE nombre = 'Diagnóstico';

-- ============================================================
-- 2) READ — Consultas
-- ============================================================

-- 2.a) WHERE + ORDER BY: agenda de turnos activos (pendientes o confirmados)
--      desde una fecha, ordenada por día y franja horaria
SELECT id, fecha, horario, sintoma
FROM turnos
WHERE estado_id IN (SELECT id FROM estados_turno WHERE nombre IN ('Pendiente', 'Confirmado'))
  AND fecha >= '2026-11-01'
ORDER BY fecha ASC, horario ASC;

-- 2.b) WHERE + ORDER BY: servicios con precio cargado, del más caro al más barato
SELECT nombre, precio_base
FROM servicios
WHERE precio_base > 0
ORDER BY precio_base DESC;

-- 2.c) INNER JOIN: agenda completa con cliente, vehículo, marca y estado
SELECT t.id                                  AS turno,
       t.fecha,
       t.horario,
       CONCAT(u.apellido, ', ', u.nombre)    AS cliente,
       u.telefono,
       CONCAT(m.nombre, ' ', v.modelo, ' ', v.anio) AS vehiculo,
       v.patente,
       e.nombre                              AS estado
FROM turnos t
INNER JOIN usuarios      u ON u.id = t.usuario_id
INNER JOIN vehiculos     v ON v.id = t.vehiculo_id
INNER JOIN marcas        m ON m.id = v.marca_id
INNER JOIN estados_turno e ON e.id = t.estado_id
ORDER BY t.fecha DESC;

-- 2.d) INNER JOIN con la tabla N:M: servicios y total presupuestado por turno
SELECT t.id                                                   AS turno,
       CONCAT(u.nombre, ' ', u.apellido)                      AS cliente,
       GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR ', ') AS servicios,
       SUM(ts.precio)                                         AS total
FROM turnos t
INNER JOIN usuarios       u  ON u.id  = t.usuario_id
INNER JOIN turno_servicio ts ON ts.turno_id = t.id
INNER JOIN servicios      s  ON s.id  = ts.servicio_id
GROUP BY t.id, u.nombre, u.apellido
ORDER BY total DESC;

-- 2.e) Búsqueda de usuario por email (usa el índice UNIQUE) con su rol
SELECT u.id, u.nombre, u.apellido, u.email, r.nombre AS rol
FROM usuarios u
INNER JOIN roles r ON r.id = u.rol_id
WHERE u.email = 'admin@boxly.com.ar';

-- ============================================================
-- 3) UPDATE — Modificación con WHERE
-- ============================================================

-- 3.a) Confirmar el turno de frenos de Martín (Toyota Hilux AE456FG)
UPDATE turnos
SET estado_id = (SELECT id FROM estados_turno WHERE nombre = 'Confirmado')
WHERE vehiculo_id = (SELECT id FROM vehiculos WHERE patente = 'AE456FG')
  AND fecha = '2026-11-11';

-- 3.b) Actualizar el precio base del Service
--      (los turnos ya cargados conservan el precio que tenían)
UPDATE servicios
SET precio_base = 48000.00
WHERE nombre = 'Service';

-- Verificación del UPDATE
SELECT t.id, t.fecha, e.nombre AS estado, t.updated_at
FROM turnos t
INNER JOIN estados_turno e ON e.id = t.estado_id
WHERE t.vehiculo_id = (SELECT id FROM vehiculos WHERE patente = 'AE456FG');

-- ============================================================
-- 4) DELETE — Baja del registro de prueba con WHERE
--    (sus filas en turno_servicio se borran por ON DELETE CASCADE)
-- ============================================================
DELETE FROM turnos
WHERE id = @turno_prueba;

-- Verificación del DELETE: ambas cuentas deben dar 0
SELECT
    (SELECT COUNT(*) FROM turnos         WHERE id       = @turno_prueba) AS turnos_restantes,
    (SELECT COUNT(*) FROM turno_servicio WHERE turno_id = @turno_prueba) AS servicios_restantes;
