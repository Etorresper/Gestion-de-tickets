-- ============================================================================
-- SCRIPT DE AJUSTE DE ESTADOS A ESTÁNDAR ITIL
-- Estados requeridos:
-- 1: Pendiente (#dc3545 - Rojo)
-- 2: En Proceso (#0dcaf0 - Cyan/Celeste)
-- 3: Resuelto  (#198754 - Verde)
-- 4: Cerrado   (#6c757d - Gris)
-- 5: Escalado  (#fd7e14 - Naranja)
-- ============================================================================

USE sistema_tickets;
GO

-- 1. Actualizar tickets que puedan haber quedado en los estados antiguos 6 o 7
-- 6 (antiguo Resuelto) -> 3 (nuevo Resuelto)
-- 7 (antiguo Cerrado)  -> 4 (nuevo Cerrado)
-- 3 (antiguo En Desplazamiento) y 4 (antiguo En Atención) -> 2 (nuevo En Proceso)
UPDATE dbo.TICKETS SET id_estado = 2 WHERE id_estado IN (3, 4);
UPDATE dbo.TICKETS SET id_estado = 3 WHERE id_estado = 6;
UPDATE dbo.TICKETS SET id_estado = 4 WHERE id_estado = 7;
GO

-- 2. Homologar la tabla ESTADOS con los 5 estados definidos
UPDATE dbo.ESTADOS SET nombre_estado = 'Pendiente',  color_hex = '#dc3545' WHERE id_estado = 1;
UPDATE dbo.ESTADOS SET nombre_estado = 'En Proceso', color_hex = '#0dcaf0' WHERE id_estado = 2;
UPDATE dbo.ESTADOS SET nombre_estado = 'Resuelto',   color_hex = '#198754' WHERE id_estado = 3;
UPDATE dbo.ESTADOS SET nombre_estado = 'Cerrado',    color_hex = '#6c757d' WHERE id_estado = 4;
UPDATE dbo.ESTADOS SET nombre_estado = 'Escalado',   color_hex = '#fd7e14' WHERE id_estado = 5;

-- Si existían los IDs 6 y 7, eliminarlos para dejar limpia la tabla
DELETE FROM dbo.ESTADOS WHERE id_estado IN (6, 7);
GO

-- 3. (Opcional) Eliminar tabla de desplazamientos si se desea limpiar la base de datos
IF OBJECT_ID('dbo.DESPLAZAMIENTOS_TECNICOS', 'U') IS NOT NULL
BEGIN
    DROP TABLE dbo.DESPLAZAMIENTOS_TECNICOS;
    PRINT 'Tabla DESPLAZAMIENTOS_TECNICOS eliminada de la base de datos.';
END
GO

PRINT 'Estados homologados con éxito a: Pendiente (1), En Proceso (2), Resuelto (3), Cerrado (4), Escalado (5).';

