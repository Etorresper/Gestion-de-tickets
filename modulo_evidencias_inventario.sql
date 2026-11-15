-- ============================================================================
-- MÓDULO DE EVIDENCIAS FOTOGRÁFICAS (ANTES, DURANTE, DESPUÉS), 
-- INVENTARIO DE EQUIPOS POR TIENDA Y CONFORMIDAD DE CIERRE
-- SQL Server 2019
-- ============================================================================

USE sistema_tickets;
GO

-- 1. TABLA EQUIPOS_TIENDA (Catálogo de activos por sede)
IF OBJECT_ID('dbo.EQUIPOS_TIENDA', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.EQUIPOS_TIENDA (
        id_equipo          INT IDENTITY(1,1) PRIMARY KEY,
        id_tienda          INT NOT NULL CONSTRAINT FK_EQ_TIENDA REFERENCES dbo.TIENDAS(id_tienda),
        tipo_equipo        NVARCHAR(50) NOT NULL, -- 'POS', 'Impresora Térmica', 'PC Caja', 'Lector Código', 'Switch/Router', 'Balanza', 'Zebra'
        marca_modelo       NVARCHAR(100) NOT NULL,
        numero_serie       NVARCHAR(100) NULL,
        codigo_patrimonial NVARCHAR(50) NULL,
        ubicacion_interna  NVARCHAR(100) DEFAULT 'Caja Principal', -- 'Caja 01', 'Almacén', 'Gerencia', etc.
        estado_equipo      NVARCHAR(30) DEFAULT 'Operativo', -- 'Operativo', 'En Falla', 'En Mantenimiento', 'De Baja'
        fecha_registro     DATETIME DEFAULT GETDATE()
    );

    CREATE INDEX IX_EQ_TIENDA ON dbo.EQUIPOS_TIENDA(id_tienda);
    PRINT 'Tabla EQUIPOS_TIENDA creada con éxito.';
END
GO

-- 2. ENRIQUECER TICKETS CON EQUIPO AFECTADO Y DETALLES DE FALLA
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'id_equipo')
BEGIN
    ALTER TABLE dbo.TICKETS ADD
        id_equipo           INT NULL CONSTRAINT FK_TKT_EQUIPO REFERENCES dbo.EQUIPOS_TIENDA(id_equipo),
        desde_cuando_falla  NVARCHAR(100) NULL,      -- Ej: "Desde esta mañana", "Hace 2 horas"
        estado_final_equipo NVARCHAR(50) NULL;       -- 'Operativo', 'Requiere Nueva Intervención'
    PRINT 'Columnas id_equipo y desde_cuando_falla agregadas a TICKETS.';
END
GO

-- 3. TABLA EVIDENCIAS_TICKET (Antes, Durante y Después)
IF OBJECT_ID('dbo.EVIDENCIAS_TICKET', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.EVIDENCIAS_TICKET (
        id_evidencia       INT IDENTITY(1,1) PRIMARY KEY,
        id_ticket          INT NOT NULL CONSTRAINT FK_EVID_TKT REFERENCES dbo.TICKETS(id_ticket),
        id_usuario         INT NOT NULL CONSTRAINT FK_EVID_USU REFERENCES dbo.USUARIOS(id_usuario),
        fase_evidencia     NVARCHAR(20) NOT NULL, -- 'Antes', 'Durante', 'Después'
        nombre_archivo     NVARCHAR(255) NOT NULL,
        ruta_archivo       NVARCHAR(255) NOT NULL,
        tipo_mime          NVARCHAR(50) NULL,
        descripcion        NVARCHAR(MAX) NULL,   -- Diagnóstico, componentes dañados o solución
        estado_equipo      NVARCHAR(50) NULL,    -- 'Operativo', 'Requiere Nueva Intervención' (en Después)
        fecha_registro     DATETIME DEFAULT GETDATE()
    );

    CREATE INDEX IX_EVID_TICKET ON dbo.EVIDENCIAS_TICKET(id_ticket, fase_evidencia);
    PRINT 'Tabla EVIDENCIAS_TICKET creada con éxito.';
END
GO

-- 4. POBLAR EQUIPOS ESTÁNDAR PARA CADA TIENDA (POS, Impresoras, Red, Lectores)
IF NOT EXISTS (SELECT 1 FROM dbo.EQUIPOS_TIENDA)
BEGIN
    -- Insertar equipos tipo para las tiendas existentes
    INSERT INTO dbo.EQUIPOS_TIENDA (id_tienda, tipo_equipo, marca_modelo, numero_serie, codigo_patrimonial, ubicacion_interna)
    SELECT 
        id_tienda, 
        'POS / Terminal de Venta', 
        'Ingenico Move 2500', 
        CONCAT('POS-', id_tienda, '-9876'), 
        CONCAT('ACT-POS-', id_tienda), 
        'Caja 01'
    FROM dbo.TIENDAS;

    INSERT INTO dbo.EQUIPOS_TIENDA (id_tienda, tipo_equipo, marca_modelo, numero_serie, codigo_patrimonial, ubicacion_interna)
    SELECT 
        id_tienda, 
        'Impresora Térmica', 
        'Epson TM-T20III', 
        CONCAT('PRN-', id_tienda, '-5432'), 
        CONCAT('ACT-PRN-', id_tienda), 
        'Caja 01'
    FROM dbo.TIENDAS;

    INSERT INTO dbo.EQUIPOS_TIENDA (id_tienda, tipo_equipo, marca_modelo, numero_serie, codigo_patrimonial, ubicacion_interna)
    SELECT 
        id_tienda, 
        'Lector de Código de Barras', 
        'Honeywell Voyager 1200g', 
        CONCAT('SCAN-', id_tienda, '-1122'), 
        CONCAT('ACT-SCN-', id_tienda), 
        'Caja 01'
    FROM dbo.TIENDAS;

    INSERT INTO dbo.EQUIPOS_TIENDA (id_tienda, tipo_equipo, marca_modelo, numero_serie, codigo_patrimonial, ubicacion_interna)
    SELECT 
        id_tienda, 
        'Switch / Conectividad de Red', 
        'D-Link DGS-1016D 16 Puertos', 
        CONCAT('SW-', id_tienda, '-3344'), 
        CONCAT('ACT-NET-', id_tienda), 
        'Rack de Comunicaciones'
    FROM dbo.TIENDAS;

    PRINT 'Catálogo de equipos tipo insertado para todas las tiendas.';
END
GO

