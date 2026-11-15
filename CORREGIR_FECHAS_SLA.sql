-- ============================================================================
-- SCRIPT DE CORRECCIÓN: HOMOLOGACIÓN DE FECHAS SLA EN SQL SERVER
-- ============================================================================
USE sistema_tickets;
GO

SET DATEFORMAT ymd;
GO

-- 1. Si fecha_vencimiento es menor a fecha_creacion (por inversión previa de día y mes bajo DATEFORMAT dmy),
-- se corrige sumando el plazo reglamentario según prioridad:
UPDATE dbo.TICKETS 
SET fecha_vencimiento = DATEADD(hour, 6, fecha_creacion)
WHERE fecha_vencimiento < fecha_creacion AND prioridad = 'Crítica';

UPDATE dbo.TICKETS 
SET fecha_vencimiento = DATEADD(hour, 26, fecha_creacion)
WHERE fecha_vencimiento < fecha_creacion AND prioridad = 'Alta';

UPDATE dbo.TICKETS 
SET fecha_vencimiento = DATEADD(hour, 48, fecha_creacion)
WHERE fecha_vencimiento < fecha_creacion AND (prioridad = 'Media' OR prioridad IS NULL);

UPDATE dbo.TICKETS 
SET fecha_vencimiento = DATEADD(hour, 72, fecha_creacion)
WHERE fecha_vencimiento < fecha_creacion AND prioridad = 'Baja';

-- 2. Corrección puntual para el ticket 9 (TKT-2026-24897):
-- Creado: 06/11/2026 21:44 | Prioridad Alta (24h + 2h = 26h) -> Vence: 07/11/2026 23:44
UPDATE dbo.TICKETS 
SET fecha_vencimiento = DATEADD(hour, 26, fecha_creacion),
    fecha_cierre = COALESCE(fecha_cierre, GETDATE())
WHERE id_ticket = 9;

-- 3. Asegurar que los tickets cerrados sin fecha_cierre tengan sellada su fecha
UPDATE dbo.TICKETS 
SET fecha_cierre = COALESCE(fecha_actualizacion, GETDATE())
WHERE id_estado = 3 AND fecha_cierre IS NULL;
GO

PRINT 'Fechas de SLA corregidas correctamente.';
GO

