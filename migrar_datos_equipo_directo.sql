-- ============================================================================
-- SCRIPT DE MIGRACIÓN: EQUIPO AFECTADO DIRECTO EN TICKETS
-- (Elimina dependencia de la tabla EQUIPOS_TIENDA y permite registro manual:
--  Nombre/Tipo, Marca/Modelo y N° de Serie)
-- ============================================================================

USE TICKET_DESK;
GO

-- 1. Agregar columnas directas a TICKETS si no existen
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'equipo_nombre')
BEGIN
    ALTER TABLE dbo.TICKETS ADD
        equipo_nombre       NVARCHAR(100) NULL,
        equipo_modelo       NVARCHAR(100) NULL,
        equipo_serie        NVARCHAR(100) NULL,
        desde_cuando_falla  NVARCHAR(100) NULL,
        estado_final_equipo NVARCHAR(50)  NULL;
    PRINT 'Columnas de equipo directo agregadas a TICKETS.';
END
GO

-- 2. Si existía una llave foránea id_equipo que apunte a EQUIPOS_TIENDA, quitar la restricción
DECLARE @fkName NVARCHAR(200);
SELECT @fkName = fk.name
FROM sys.foreign_keys fk
INNER JOIN sys.tables t ON fk.parent_object_id = t.object_id
WHERE t.name = 'TICKETS' AND fk.name LIKE '%EQUIPO%';

IF @fkName IS NOT NULL
BEGIN
    EXEC('ALTER TABLE dbo.TICKETS DROP CONSTRAINT ' + @fkName);
    PRINT 'Restricción FK de id_equipo eliminada.';
END
GO

-- 3. Eliminar la tabla EQUIPOS_TIENDA si existe
IF OBJECT_ID('dbo.EQUIPOS_TIENDA', 'U') IS NOT NULL
BEGIN
    DROP TABLE dbo.EQUIPOS_TIENDA;
    PRINT 'Tabla EQUIPOS_TIENDA eliminada con éxito.';
END
GO

PRINT 'Migración completada. Ahora los datos de equipo (Nombre, Modelo y Serie) se manejan directamente.';

