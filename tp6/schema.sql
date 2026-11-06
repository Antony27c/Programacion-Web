-- ============================================================
-- BOXLY — Esquema relacional normalizado (TP6)
-- Motor: MySQL 8 / MariaDB 10.4+ (XAMPP, Laragon, Workbench)
-- Orden de ejecución: 1) schema.sql  2) consultas.sql
-- ============================================================
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS boxly
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE boxly;

-- Se borran en orden inverso a las dependencias (primero las hijas)
DROP TABLE IF EXISTS turno_servicio;
DROP TABLE IF EXISTS turnos;
DROP TABLE IF EXISTS vehiculos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS marcas;
DROP TABLE IF EXISTS estados_turno;
DROP TABLE IF EXISTS roles;

-- ------------------------------------------------------------
-- Tablas maestras / secundarias
-- ------------------------------------------------------------
CREATE TABLE roles (
    id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    CONSTRAINT uq_roles_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

CREATE TABLE estados_turno (
    id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL,
    CONSTRAINT uq_estados_turno_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

CREATE TABLE marcas (
    id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    CONSTRAINT uq_marcas_nombre UNIQUE (nombre)
) ENGINE = InnoDB;

CREATE TABLE servicios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(60)   NOT NULL,
    descripcion VARCHAR(255)  NULL,
    precio_base DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_servicios_nombre UNIQUE (nombre),
    CONSTRAINT ck_servicios_precio CHECK (precio_base >= 0)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Usuarios (autenticación + rol + datos personales)
-- password_hash admite NULL: los clientes que piden turno desde la
-- web quedan registrados sin acceso hasta que creen su contraseña.
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id        INT UNSIGNED NOT NULL,
    nombre        VARCHAR(60)  NOT NULL,
    apellido      VARCHAR(60)  NOT NULL,
    email         VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NULL,
    telefono      VARCHAR(20)  NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_usuarios_email UNIQUE (email),
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Vehículos (cada uno pertenece a un usuario y a una marca)
-- ------------------------------------------------------------
CREATE TABLE vehiculos (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    marca_id   INT UNSIGNED NOT NULL,
    modelo     VARCHAR(60)  NOT NULL,
    anio       YEAR         NOT NULL,
    patente    VARCHAR(10)  NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_vehiculos_patente UNIQUE (patente),
    CONSTRAINT fk_vehiculos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_vehiculos_marca FOREIGN KEY (marca_id) REFERENCES marcas (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Tabla principal de negocio: turnos del taller
-- usuario_id = cliente que solicita el turno (puede no ser el dueño
-- registrado del vehículo, por eso no se deriva de vehiculos).
-- ------------------------------------------------------------
CREATE TABLE turnos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NOT NULL,
    vehiculo_id INT UNSIGNED NOT NULL,
    estado_id   INT UNSIGNED NOT NULL,
    fecha       DATE NOT NULL,
    horario     ENUM('Mañana', 'Tarde') NOT NULL,
    sintoma     TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_turnos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_turnos_vehiculo FOREIGN KEY (vehiculo_id) REFERENCES vehiculos (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_turnos_estado FOREIGN KEY (estado_id) REFERENCES estados_turno (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Tabla de relación N:M turno <-> servicio
-- precio = precio cobrado en ESE turno (se congela aunque luego
-- cambie servicios.precio_base).
-- ------------------------------------------------------------
CREATE TABLE turno_servicio (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    turno_id    INT UNSIGNED  NOT NULL,
    servicio_id INT UNSIGNED  NOT NULL,
    precio      DECIMAL(10,2) NOT NULL,
    CONSTRAINT uq_turno_servicio UNIQUE (turno_id, servicio_id),
    CONSTRAINT fk_ts_turno FOREIGN KEY (turno_id) REFERENCES turnos (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ts_servicio FOREIGN KEY (servicio_id) REFERENCES servicios (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT ck_ts_precio CHECK (precio >= 0)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Índices de rendimiento
-- (email y patente ya quedan indexados por sus UNIQUE;
--  cada FK también genera su propio índice en InnoDB)
-- ------------------------------------------------------------
-- Agenda del taller: listar turnos por día
CREATE INDEX idx_turnos_fecha ON turnos (fecha);
-- Panel admin: filtrar por estado y ordenar por fecha
CREATE INDEX idx_turnos_estado_fecha ON turnos (estado_id, fecha);
-- Búsqueda de clientes por apellido y nombre
CREATE INDEX idx_usuarios_apellido_nombre ON usuarios (apellido, nombre);
