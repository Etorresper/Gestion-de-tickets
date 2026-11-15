-- ============================================================================
-- EJECUTAR ESTE SCRIPT EN SQL SERVER MANAGEMENT STUDIO (SSMS)
-- Abre una Nueva Consulta (New Query), pega esto y presiona F5 (Execute)
-- ============================================================================

USE sistema_tickets;
GO

-- 1. Agregar equipo_nombre si no existe
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_nombre')
BEGIN
    ALTER TABLE dbo.TICKETS ADD equipo_nombre NVARCHAR(100) NULL;
    PRINT 'Columna equipo_nombre agregada exitosamente.';
END
ELSE
    PRINT 'Columna equipo_nombre ya existia.';
GO

-- 2. Agregar equipo_modelo si no existe
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_modelo')
BEGIN
    ALTER TABLE dbo.TICKETS ADD equipo_modelo NVARCHAR(100) NULL;
    PRINT 'Columna equipo_modelo agregada exitosamente.';
END
ELSE
    PRINT 'Columna equipo_modelo ya existia.';
GO

-- 3. Agregar equipo_serie si no existe
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_serie')
BEGIN
    ALTER TABLE dbo.TICKETS ADD equipo_serie NVARCHAR(100) NULL;
    PRINT 'Columna equipo_serie agregada exitosamente.';
END
ELSE
    PRINT 'Columna equipo_serie ya existia.';
GO

-- 4. Agregar desde_cuando_falla si no existe
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'desde_cuando_falla')
BEGIN
    ALTER TABLE dbo.TICKETS ADD desde_cuando_falla NVARCHAR(100) NULL;
    PRINT 'Columna desde_cuando_falla agregada exitosamente.';
END
ELSE
    PRINT 'Columna desde_cuando_falla ya existia.';
GO

-- 5. Agregar estado_final_equipo si no existe
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'estado_final_equipo')
BEGIN
    ALTER TABLE dbo.TICKETS ADD estado_final_equipo NVARCHAR(50) NULL;
    PRINT 'Columna estado_final_equipo agregada exitosamente.';
END
ELSE
    PRINT 'Columna estado_final_equipo ya existia.';
GO

PRINT '¡LISTO! Todas las columnas para el reporte técnico están creadas en la base de datos.';

