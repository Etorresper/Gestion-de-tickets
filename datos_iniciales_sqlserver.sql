-- ============================================================================
-- SCRIPT DE DATOS INICIALES (TIENDAS, DISTRITOS, USUARIOS Y TICKETS DE EJEMPLO)
-- Compatible con SQL Server 2019
-- ============================================================================

USE sistema_tickets;
GO

-- 1. DISTRITOS
IF NOT EXISTS (SELECT 1 FROM DISTRITOS)
BEGIN
    SET IDENTITY_INSERT dbo.DISTRITOS ON;
    INSERT INTO dbo.DISTRITOS (id_distrito, nombre_distrito) VALUES 
        (1, 'Surco'),
        (2, 'Miraflores'),
        (3, 'San Isidro'),
        (4, 'Lince'),
        (5, 'Ate');
    SET IDENTITY_INSERT dbo.DISTRITOS OFF;
    PRINT 'Distritos insertados.';
END
GO

-- 2. TIENDAS
IF NOT EXISTS (SELECT 1 FROM TIENDAS)
BEGIN
    SET IDENTITY_INSERT dbo.TIENDAS ON;
    INSERT INTO dbo.TIENDAS (id_tienda, nombre_tienda, direccion, id_distrito) VALUES 
        (1, 'Tienda Surco 01', 'Av. Benavides 1234', 1),
        (2, 'Tienda Miraflores Central', 'Calle Shell 456', 2),
        (3, 'Tienda San Isidro Business', 'Av. Rivera Navarrete 789', 3),
        (4, 'Tienda Lince Express', 'Av. Arequipa 2010', 4),
        (5, 'Tienda Ate Industrial', 'Carr. Central Km 5', 5);
    SET IDENTITY_INSERT dbo.TIENDAS OFF;
    PRINT 'Tiendas insertadas.';
END
GO

-- 3. USUARIOS (Técnicos de Soporte y Empleados)
-- Contraseña de todos: admin123
-- Hash Bcrypt: $2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e
IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE id_usuario = 2)
BEGIN
    SET IDENTITY_INSERT dbo.USUARIOS ON;
    INSERT INTO dbo.USUARIOS (id_usuario, nombre, email, password_hash, id_rol, id_area, activo) VALUES 
        -- Técnicos asignados por IA según prioridad:
        (2, 'Edwin Torres',    'edwin.torres@empresa.com',    '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 2, 3, 1), -- Rol 2: Soporte (Hardware)
        (3, 'Jofre Melgarejo', 'jofre.melgarejo@empresa.com', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 2, 1, 1), -- Rol 2: Soporte (Sistemas)
        (4, 'Henri Gonzales',  'henri.gonzales@empresa.com',  '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 2, 2, 1), -- Rol 2: Soporte (Redes)
        -- Empleados solicitantes:
        (5, 'Carlos Empleado', 'carlos@empresa.com',          '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1), -- Rol 3: Empleado
        (6, 'Maria Ventas',    'maria@empresa.com',           '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);  -- Rol 3: Empleado
    SET IDENTITY_INSERT dbo.USUARIOS OFF;
    PRINT 'Usuarios insertados correctamente.';
END
GO

-- 4. TICKETS INICIALES DE DEMOSTRACIÓN (Opcional)
IF NOT EXISTS (SELECT 1 FROM TICKETS)
BEGIN
    INSERT INTO dbo.TICKETS (codigo_ticket, id_solicitante, asunto, descripcion, id_categoria, id_estado, prioridad, id_tienda, fecha_vencimiento, fecha_creacion, fecha_actualizacion) VALUES 
        ('TKT-2024-1001', 5, 'No hay conexion de red en terminal POS', 'El punto de venta no sincroniza las transacciones con el servidor central.', 1, 2, 'Alta', 1, DATEADD(hour, 24, GETDATE()), DATEADD(day, -2, GETDATE()), DATEADD(day, -1, GETDATE())),
        ('TKT-2024-1002', 6, 'Impresora termica atasca papel continuo', 'La impresora de comprobantes de caja 2 tiene problemas con la alimentacion del rollo.', 2, 1, 'Media', 2, DATEADD(hour, 48, GETDATE()), DATEADD(hour, -5, GETDATE()), GETDATE()),
        ('TKT-2024-1003', 5, 'Lentitud general en aplicacion de caja', 'La pantalla de cobros demora mas de 15 segundos en registrar cada item.', 3, 3, 'Baja', 3, DATEADD(hour, 72, GETDATE()), DATEADD(day, -4, GETDATE()), DATEADD(day, -1, GETDATE())),
        ('TKT-2024-1004', 6, 'Scanner de codigo de barras no lee etiquetas', 'El lector optico no emite luz roja y no responde a la lectura.', 2, 1, 'Media', 4, DATEADD(hour, 48, GETDATE()), DATEADD(hour, -2, GETDATE()), GETDATE());

    -- Asignaciones iniciales
    INSERT INTO dbo.ASIGNACIONES (id_ticket, id_tecnico, fecha_asignacion) VALUES 
        (1, 4, DATEADD(day, -2, GETDATE())), -- Henri Gonzales (Redes/Alta)
        (2, 3, DATEADD(hour, -5, GETDATE())), -- Jofre Melgarejo (Media)
        (3, 2, DATEADD(day, -4, GETDATE())), -- Edwin Torres (Baja)
        (4, 3, DATEADD(hour, -2, GETDATE())); -- Jofre Melgarejo (Media)

    -- Historial inicial
    INSERT INTO dbo.HISTORIAL_TICKETS (id_ticket, id_usuario, accion, detalle, fecha_accion) VALUES 
        (1, 5, 'Creacion', 'Ticket registrado desde la plataforma web.', DATEADD(day, -2, GETDATE())),
        (1, 1, 'Asignacion Automatica', 'Ticket asignado automaticamente a Henri Gonzales segun prioridad Alta.', DATEADD(day, -2, GETDATE())),
        (2, 6, 'Creacion', 'Ticket registrado desde la plataforma web.', DATEADD(hour, -5, GETDATE())),
        (2, 1, 'Asignacion Automatica', 'Ticket asignado automaticamente a Jofre Melgarejo segun prioridad Media.', DATEADD(hour, -5, GETDATE())),
        (3, 5, 'Creacion', 'Ticket registrado desde la plataforma web.', DATEADD(day, -4, GETDATE())),
        (3, 2, 'Cambio de Estado a Resuelto', 'Se optimizaron los indices y se reinicio el servicio local.', DATEADD(day, -1, GETDATE())),
        (4, 6, 'Creacion', 'Ticket registrado desde la plataforma web.', DATEADD(hour, -2, GETDATE()));

    PRINT 'Tickets, asignaciones e historial iniciales cargados con exito.';
END
GO

