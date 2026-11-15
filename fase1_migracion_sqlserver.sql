-- ============================================================================
-- FASE 1: ACTUALIZACIÓN DE BASE DE DATOS (SQL Server 2019)
-- Restricción por tienda, 7 estados de atención y control de desplazamientos
-- ============================================================================

USE sistema_tickets;
GO

-- 1. AGREGAR id_tienda A USUARIOS
IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'USUARIOS' AND COLUMN_NAME = 'id_tienda'
)
BEGIN
    ALTER TABLE dbo.USUARIOS 
    ADD id_tienda INT NULL CONSTRAINT FK_USU_TIENDA REFERENCES dbo.TIENDAS(id_tienda);
    PRINT 'Columna id_tienda agregada a USUARIOS.';
END
GO

-- 2. VINCULAR ENCARGADOS A SUS RESPECTIVAS TIENDAS
UPDATE u
SET u.id_tienda = t.id_tienda
FROM dbo.USUARIOS u
INNER JOIN dbo.TIENDAS t ON LTRIM(RTRIM(REPLACE(u.nombre, 'Encargado - ', ''))) = LTRIM(RTRIM(t.nombre_tienda))
WHERE u.id_rol = 3 AND u.id_tienda IS NULL;

-- Asignar tiendas iniciales a Carlos Empleado y Maria Ventas
UPDATE dbo.USUARIOS SET id_tienda = 1 WHERE email = 'carlos@empresa.com' AND id_tienda IS NULL;
UPDATE dbo.USUARIOS SET id_tienda = 2 WHERE email = 'maria@empresa.com' AND id_tienda IS NULL;

PRINT 'Encargados vinculados a sus tiendas con éxito.';
GO

-- 3. ACTUALIZAR Y HOMOLOGAR LOS 7 ESTADOS OFICIALES
-- Desactivar temporalmente FKs si fuera necesario, o actualizar/insertar respetando IDs
IF NOT EXISTS (SELECT 1 FROM dbo.ESTADOS WHERE id_estado = 7)
BEGIN
    SET IDENTITY_INSERT dbo.ESTADOS ON;

    -- Si ya existen estados del 1 al 5, actualizamos sus nombres y colores para respetar las FK existentes
    UPDATE dbo.ESTADOS SET nombre_estado = 'Pendiente', color_hex = '#dc3545' WHERE id_estado = 1;
    UPDATE dbo.ESTADOS SET nombre_estado = 'Asignado', color_hex = '#0dcaf0' WHERE id_estado = 2;
    UPDATE dbo.ESTADOS SET nombre_estado = 'En Desplazamiento', color_hex = '#fd7e14' WHERE id_estado = 3;
    UPDATE dbo.ESTADOS SET nombre_estado = 'En Atención', color_hex = '#ffc107' WHERE id_estado = 4;
    UPDATE dbo.ESTADOS SET nombre_estado = 'En Espera', color_hex = '#6f42c1' WHERE id_estado = 5;

    -- Insertamos el 6 y 7
    IF NOT EXISTS (SELECT 1 FROM dbo.ESTADOS WHERE id_estado = 6)
        INSERT INTO dbo.ESTADOS (id_estado, nombre_estado, color_hex) VALUES (6, 'Resuelto', '#198754');

    IF NOT EXISTS (SELECT 1 FROM dbo.ESTADOS WHERE id_estado = 7)
        INSERT INTO dbo.ESTADOS (id_estado, nombre_estado, color_hex) VALUES (7, 'Cerrado', '#6c757d');

    SET IDENTITY_INSERT dbo.ESTADOS OFF;
    PRINT '7 Estados oficiales configurados.';
END
GO

-- 4. AGREGAR CAMPOS REQUERIDOS A TICKETS
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'TICKETS' AND COLUMN_NAME = 'tipo_atencion')
BEGIN
    ALTER TABLE dbo.TICKETS ADD
        tipo_atencion           NVARCHAR(20) DEFAULT 'Remota',     -- 'Remota' o 'Presencial'
        prioridad_original      NVARCHAR(20) NULL,                 -- Prioridad seleccionada inicialmente
        motivo_cambio_prio      NVARCHAR(MAX) NULL,                -- Motivo por el cual se modificó la prioridad
        usuario_cambio_prio     INT NULL CONSTRAINT FK_TKT_UPRIO REFERENCES dbo.USUARIOS(id_usuario),
        fecha_cambio_prio       DATETIME NULL,
        conformidad_encargado   BIT DEFAULT 0,                     -- 1: Conforme, 0: Pendiente/Disconforme
        comentario_conformidad  NVARCHAR(MAX) NULL,
        fecha_conformidad       DATETIME NULL;

    PRINT 'Columnas agregadas a TICKETS (tipo_atencion, prioridad_original, conformidad, etc.).';
END
GO

-- Rellenar prioridad_original en tickets existentes si estaba nulo
UPDATE dbo.TICKETS 
SET prioridad_original = prioridad 
WHERE prioridad_original IS NULL;
GO

-- 5. CREAR TABLA DE DESPLAZAMIENTOS DE TÉCNICOS (DESDE COMAS A TIENDAS)
IF OBJECT_ID('dbo.DESPLAZAMIENTOS_TECNICOS', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DESPLAZAMIENTOS_TECNICOS (
        id_desplazamiento       INT IDENTITY(1,1) PRIMARY KEY,
        id_ticket               INT NOT NULL CONSTRAINT FK_DESP_TKT REFERENCES dbo.TICKETS(id_ticket),
        id_tecnico              INT NOT NULL CONSTRAINT FK_DESP_TEC REFERENCES dbo.USUARIOS(id_usuario),
        sede_origen             NVARCHAR(100) DEFAULT 'Sede Central Comas',
        id_tienda_destino       INT NOT NULL CONSTRAINT FK_DESP_TDA REFERENCES dbo.TIENDAS(id_tienda),
        fecha_hora_salida_base  DATETIME NULL,    -- Salida desde Comas
        fecha_hora_llegada_tda  DATETIME NULL,    -- Llegada a la tienda
        fecha_hora_inicio_atn   DATETIME NULL,    -- Inicio de labores en tienda
        fecha_hora_fin_atn      DATETIME NULL,    -- Fin de la reparación
        fecha_hora_retorno_base DATETIME NULL,    -- Retorno a Comas
        observaciones_traslado  NVARCHAR(MAX) NULL,
        estado_desplazamiento   NVARCHAR(30) DEFAULT 'En Camino', -- 'En Camino', 'En Tienda', 'Atendiendo', 'Retornado', 'Finalizado'
        fecha_registro          DATETIME DEFAULT GETDATE()
    );

    CREATE INDEX IX_DESP_TICKET  ON dbo.DESPLAZAMIENTOS_TECNICOS(id_ticket);
    CREATE INDEX IX_DESP_TECNICO ON dbo.DESPLAZAMIENTOS_TECNICOS(id_tecnico);
    CREATE INDEX IX_DESP_FECHA   ON dbo.DESPLAZAMIENTOS_TECNICOS(fecha_registro DESC);

    PRINT 'Tabla DESPLAZAMIENTOS_TECNICOS creada exitosamente.';
END
GO

PRINT '=== FASE 1 COMPLETADA CON ÉXITO EN SQL SERVER 2019 ===';
GO

