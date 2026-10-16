-- ============================================================
-- BOXLY — Base de datos del taller (TP7)
-- Importar en MySQL Workbench: File > Open SQL Script > Execute
-- ============================================================
CREATE DATABASE IF NOT EXISTS boxly
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE boxly;

DROP TABLE IF EXISTS turnos;

CREATE TABLE turnos (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(120) NOT NULL,
    telefono       VARCHAR(20)  NOT NULL,
    vehiculo       VARCHAR(150) NOT NULL,
    servicio       ENUM('Service', 'Frenos', 'Diagnóstico', 'Otro') NOT NULL,
    fecha          DATE NOT NULL,
    horario        ENUM('Mañana', 'Tarde') NOT NULL,
    sintoma        TEXT NOT NULL,
    estado         ENUM('Pendiente', 'Confirmado', 'Finalizado', 'Cancelado') NOT NULL DEFAULT 'Pendiente',
    creado_en      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_turnos_fecha (fecha)
) ENGINE = InnoDB;

INSERT INTO turnos (nombre, telefono, vehiculo, servicio, fecha, horario, sintoma, estado) VALUES
('Lucía Fernández',  '+54 387 455-1020', 'Volkswagen Gol 2018',      'Service',     '2026-10-20', 'Mañana', 'Service de los 60.000 km, cambio de aceite y filtros.',        'Confirmado'),
('Martín Guaymás',   '+54 387 412-3344', 'Toyota Hilux 2021',        'Frenos',      '2026-10-21', 'Tarde',  'Chirrido al frenar y el pedal se siente esponjoso.',           'Pendiente'),
('Sofía Cruz',       '387 15 600-7788',  'Fiat Cronos 2022',         'Diagnóstico', '2026-10-22', 'Mañana', 'Se encendió la luz de check engine y pierde potencia.',        'Pendiente'),
('Diego Mamaní',     '+54 387 499-0011', 'Chevrolet Onix 2020',      'Otro',        '2026-10-14', 'Tarde',  'Ruido metálico en la suspensión delantera en calles de ripio.', 'Finalizado'),
('Valentina Ríos',   '+54 387 433-5566', 'Renault Sandero 2017',     'Service',     '2026-10-24', 'Mañana', 'Revisión general antes de un viaje largo a Cafayate.',          'Cancelado');
