-- ============================================================================
-- SCRIPT: UNIFICACIÓN A 4 ESTADOS LIMPIOS
-- 1: Pendiente (#dc3545 - Rojo)
-- 2: En Proceso (#0dcaf0 - Cyan)
-- 3: Cerrado   (#198754 - Verde)
-- 4: Escalado  (#fd7e14 - Naranja)
-- ============================================================================

USE sistema_tickets;
GO

-- 1. Actualizar tickets existentes que estuvieran en estado 4, 5, 6 o 7
-- Si estaba en 5 (antiguo escalado) -> pasa a 4 (nuevo escalado)
-- Si estaba en 6 o 7 (antiguo resuelto/cerrado) -> pasa a 3 (nuevo cerrado)
UPDATE dbo.TICKETS SET id_estado = 4 WHERE id_estado = 5;
UPDATE dbo.TICKETS SET id_estado = 3 WHERE id_estado IN (6, 7);
GO

-- 2. Homologar la tabla ESTADOS a solo 4 registros
UPDATE dbo.ESTADOS SET nombre_estado = 'Pendiente',  color_hex = '#dc3545' WHERE id_estado = 1;
UPDATE dbo.ESTADOS SET nombre_estado = 'En Proceso', color_hex = '#0dcaf0' WHERE id_estado = 2;
UPDATE dbo.ESTADOS SET nombre_estado = 'Cerrado',    color_hex = '#198754' WHERE id_estado = 3;
UPDATE dbo.ESTADOS SET nombre_estado = 'Escalado',   color_hex = '#fd7e14' WHERE id_estado = 4;

-- Eliminar cualquier estado mayor a 4
DELETE FROM dbo.ESTADOS WHERE id_estado > 4;
GO

PRINT '¡LISTO! Sistema homologado a 4 estados: Pendiente (1), En Proceso (2), Cerrado (3) y Escalado (4).';

