-- ============================================================
-- SCRIPT DE CREACIÓN - Sistema de Gestión de Incidencias
-- Motor: Microsoft SQL Server 2019
-- Versión: 1.0 — Migrado desde MySQL
-- ============================================================

USE master;
GO

-- Crear la base de datos si no existe
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'sistema_tickets')
BEGIN
    CREATE DATABASE sistema_tickets
        COLLATE SQL_Latin1_General_CP1_CI_AS;
END
GO

USE sistema_tickets;
GO

-- ============================================================
-- TABLAS (en orden por dependencias de FK)
-- ============================================================

-- 1. ROLES
IF OBJECT_ID('dbo.ROLES', 'U') IS NOT NULL DROP TABLE dbo.ROLES;
CREATE TABLE dbo.ROLES (
    id_rol    INT IDENTITY(1,1) PRIMARY KEY,
    nombre_rol NVARCHAR(50) NOT NULL
);
GO

-- 2. AREAS
IF OBJECT_ID('dbo.AREAS', 'U') IS NOT NULL DROP TABLE dbo.AREAS;
CREATE TABLE dbo.AREAS (
    id_area    INT IDENTITY(1,1) PRIMARY KEY,
    nombre_area NVARCHAR(100) NOT NULL
);
GO

-- 3. CATEGORIAS
IF OBJECT_ID('dbo.CATEGORIAS', 'U') IS NOT NULL DROP TABLE dbo.CATEGORIAS;
CREATE TABLE dbo.CATEGORIAS (
    id_categoria       INT IDENTITY(1,1) PRIMARY KEY,
    nombre_categoria   NVARCHAR(100) NOT NULL,
    palabras_clave     NVARCHAR(MAX),
    id_area_responsable INT,
    CONSTRAINT FK_CAT_AREA FOREIGN KEY (id_area_responsable) REFERENCES dbo.AREAS(id_area)
);
GO

-- 4. ESTADOS
IF OBJECT_ID('dbo.ESTADOS', 'U') IS NOT NULL DROP TABLE dbo.ESTADOS;
CREATE TABLE dbo.ESTADOS (
    id_estado    INT IDENTITY(1,1) PRIMARY KEY,
    nombre_estado NVARCHAR(50) NOT NULL,
    color_hex     NVARCHAR(7)
);
GO

-- 5. DISTRITOS
IF OBJECT_ID('dbo.DISTRITOS', 'U') IS NOT NULL DROP TABLE dbo.DISTRITOS;
CREATE TABLE dbo.DISTRITOS (
    id_distrito    INT IDENTITY(1,1) PRIMARY KEY,
    nombre_distrito NVARCHAR(100) NOT NULL
);
GO

-- 6. TIENDAS
IF OBJECT_ID('dbo.TIENDAS', 'U') IS NOT NULL DROP TABLE dbo.TIENDAS;
CREATE TABLE dbo.TIENDAS (
    id_tienda    INT IDENTITY(1,1) PRIMARY KEY,
    nombre_tienda NVARCHAR(100) NOT NULL,
    direccion     NVARCHAR(255),
    id_distrito   INT,
    CONSTRAINT FK_TIENDA_DISTRITO FOREIGN KEY (id_distrito) REFERENCES dbo.DISTRITOS(id_distrito)
);
GO

-- 7. USUARIOS
IF OBJECT_ID('dbo.USUARIOS', 'U') IS NOT NULL DROP TABLE dbo.USUARIOS;
CREATE TABLE dbo.USUARIOS (
    id_usuario    INT IDENTITY(1,1) PRIMARY KEY,
    nombre        NVARCHAR(100) NOT NULL,
    email         NVARCHAR(100) NOT NULL UNIQUE,
    password_hash NVARCHAR(255) NOT NULL,
    id_rol        INT,
    id_area       INT,
    activo        BIT DEFAULT 1,                 -- BIT reemplaza a BOOLEAN de MySQL
    CONSTRAINT FK_USU_ROL  FOREIGN KEY (id_rol)  REFERENCES dbo.ROLES(id_rol),
    CONSTRAINT FK_USU_AREA FOREIGN KEY (id_area) REFERENCES dbo.AREAS(id_area)
);
GO

-- 8. TICKETS
IF OBJECT_ID('dbo.TICKETS', 'U') IS NOT NULL DROP TABLE dbo.TICKETS;
CREATE TABLE dbo.TICKETS (
    id_ticket          INT IDENTITY(1,1) PRIMARY KEY,
    codigo_ticket      NVARCHAR(20) NOT NULL UNIQUE,
    id_solicitante     INT,
    asunto             NVARCHAR(200) NOT NULL,
    descripcion        NVARCHAR(MAX),
    id_categoria       INT,
    id_estado          INT,
    prioridad          NVARCHAR(20),
    id_tienda          INT,
    escalar_no_resuelto BIT DEFAULT 0,
    fecha_vencimiento  DATETIME,
    fecha_creacion     DATETIME DEFAULT GETDATE(),
    fecha_actualizacion DATETIME DEFAULT GETDATE(),   -- Se actualiza manualmente en el UPDATE
    fecha_cierre       DATETIME NULL,
    CONSTRAINT FK_TKT_SOL  FOREIGN KEY (id_solicitante) REFERENCES dbo.USUARIOS(id_usuario),
    CONSTRAINT FK_TKT_CAT  FOREIGN KEY (id_categoria)   REFERENCES dbo.CATEGORIAS(id_categoria),
    CONSTRAINT FK_TKT_EST  FOREIGN KEY (id_estado)      REFERENCES dbo.ESTADOS(id_estado),
    CONSTRAINT FK_TKT_TDA  FOREIGN KEY (id_tienda)      REFERENCES dbo.TIENDAS(id_tienda)
);
GO

-- 9. ASIGNACIONES
IF OBJECT_ID('dbo.ASIGNACIONES', 'U') IS NOT NULL DROP TABLE dbo.ASIGNACIONES;
CREATE TABLE dbo.ASIGNACIONES (
    id_asignacion  INT IDENTITY(1,1) PRIMARY KEY,
    id_ticket      INT,
    id_tecnico     INT,
    fecha_asignacion DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_ASIG_TKT FOREIGN KEY (id_ticket)  REFERENCES dbo.TICKETS(id_ticket),
    CONSTRAINT FK_ASIG_TEC FOREIGN KEY (id_tecnico) REFERENCES dbo.USUARIOS(id_usuario)
);
GO

-- 10. HISTORIAL_TICKETS
IF OBJECT_ID('dbo.HISTORIAL_TICKETS', 'U') IS NOT NULL DROP TABLE dbo.HISTORIAL_TICKETS;
CREATE TABLE dbo.HISTORIAL_TICKETS (
    id_historial INT IDENTITY(1,1) PRIMARY KEY,
    id_ticket    INT,
    id_usuario   INT,
    accion       NVARCHAR(100),
    detalle      NVARCHAR(MAX),
    fecha_accion DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_HIST_TKT FOREIGN KEY (id_ticket)  REFERENCES dbo.TICKETS(id_ticket),
    CONSTRAINT FK_HIST_USU FOREIGN KEY (id_usuario) REFERENCES dbo.USUARIOS(id_usuario)
);
GO

-- 11. COMENTARIOS_TICKET
IF OBJECT_ID('dbo.COMENTARIOS_TICKET', 'U') IS NOT NULL DROP TABLE dbo.COMENTARIOS_TICKET;
CREATE TABLE dbo.COMENTARIOS_TICKET (
    id_comentario INT IDENTITY(1,1) PRIMARY KEY,
    id_ticket     INT,
    id_usuario    INT,
    comentario    NVARCHAR(MAX),
    fecha_comentario DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_COM_TKT FOREIGN KEY (id_ticket)  REFERENCES dbo.TICKETS(id_ticket),
    CONSTRAINT FK_COM_USU FOREIGN KEY (id_usuario) REFERENCES dbo.USUARIOS(id_usuario)
);
GO

-- 12. ADJUNTOS
IF OBJECT_ID('dbo.ADJUNTOS', 'U') IS NOT NULL DROP TABLE dbo.ADJUNTOS;
CREATE TABLE dbo.ADJUNTOS (
    id_adjunto    INT IDENTITY(1,1) PRIMARY KEY,
    id_ticket     INT,
    nombre_archivo NVARCHAR(255),
    ruta_archivo   NVARCHAR(255),
    tipo_mime      NVARCHAR(50),
    CONSTRAINT FK_ADJ_TKT FOREIGN KEY (id_ticket) REFERENCES dbo.TICKETS(id_ticket)
);
GO

-- ============================================================
-- DATOS SEMILLA (INSERT con SET IDENTITY_INSERT)
-- ============================================================

SET IDENTITY_INSERT dbo.ROLES ON;
INSERT INTO dbo.ROLES (id_rol, nombre_rol) VALUES (1,'Administrador'),(2,'Soporte'),(3,'Empleado');
SET IDENTITY_INSERT dbo.ROLES OFF;
GO

SET IDENTITY_INSERT dbo.AREAS ON;
INSERT INTO dbo.AREAS (id_area, nombre_area) VALUES (1,'Sistemas'),(2,'Redes'),(3,'Hardware'),(4,'Mantenimiento');
SET IDENTITY_INSERT dbo.AREAS OFF;
GO

SET IDENTITY_INSERT dbo.ESTADOS ON;
INSERT INTO dbo.ESTADOS (id_estado, nombre_estado, color_hex) VALUES
    (1,'Pendiente',  '#dc3545'),
    (2,'En Proceso', '#ffc107'),
    (3,'Resuelto',   '#28a745'),
    (4,'Cerrado',    '#6c757d'),
    (5,'Escalado',   '#6610f2');
SET IDENTITY_INSERT dbo.ESTADOS OFF;
GO

SET IDENTITY_INSERT dbo.CATEGORIAS ON;
INSERT INTO dbo.CATEGORIAS (id_categoria, nombre_categoria, palabras_clave, id_area_responsable) VALUES
    (1, 'Problema de Red',    N'["internet","wifi","conexion","red"]',                                   2),
    (2, 'Hardware/Equipos',   N'["impresora","teclado","mouse","monitor","pantalla","pc","laptop"]',      3),
    (3, 'Software/Sistemas',  N'["sistema","login","error","app","aplicacion","lento"]',                  1);
SET IDENTITY_INSERT dbo.CATEGORIAS OFF;
GO

-- Usuario administrador por defecto (contraseña: admin123)
-- Hash Bcrypt generado con password_hash('admin123', PASSWORD_DEFAULT)
SET IDENTITY_INSERT dbo.USUARIOS ON;
INSERT INTO dbo.USUARIOS (id_usuario, nombre, email, password_hash, id_rol, id_area, activo) VALUES
    (1, 'Admin Sistema', 'admin@empresa.com',
     '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e',
     1, 1, 1);
SET IDENTITY_INSERT dbo.USUARIOS OFF;
GO

-- ============================================================
-- ÍNDICES RECOMENDADOS
-- ============================================================
CREATE INDEX IX_TICKETS_ESTADO   ON dbo.TICKETS(id_estado);
CREATE INDEX IX_TICKETS_SOL      ON dbo.TICKETS(id_solicitante);
CREATE INDEX IX_TICKETS_CREACION ON dbo.TICKETS(fecha_creacion DESC);
CREATE INDEX IX_ASIG_TICKET      ON dbo.ASIGNACIONES(id_ticket);
CREATE INDEX IX_ASIG_TECNICO     ON dbo.ASIGNACIONES(id_tecnico);
GO

PRINT 'Base de datos sistema_tickets creada correctamente en SQL Server 2019.';
GO

