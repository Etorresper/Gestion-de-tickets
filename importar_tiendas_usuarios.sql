-- ============================================================================
-- IMPORTACIÓN COMPLETA DE TIENDAS Y USUARIOS (CORPORACIÓN VEGA)
-- ============================================================================
USE sistema_tickets;
GO

-- 1. INSERTAR DISTRITOS
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Ate') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Ate'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Bella Vista') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Bella Vista'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Breña') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Breña'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Callao') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Callao'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Carabayllo') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Carabayllo'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Cercado de Lima') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Cercado de Lima'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Chorrillos') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Chorrillos'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Comas') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Comas'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'El Agustino') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'El Agustino'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Independencia') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Independencia'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Jesus Maria') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Jesus Maria'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'La Molina') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'La Molina'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'La Victoria') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'La Victoria'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Lima') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Lima'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Lince') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Lince'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Los Olivos') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Los Olivos'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Mi Peru') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Mi Peru'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Pueblo Libre') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Pueblo Libre'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Puente Piedra') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Puente Piedra'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Rimac') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Rimac'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Borja') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Borja'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Isidro') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Isidro'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Juan de Lurigancho') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Juan de Lurigancho'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Luis') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Luis'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Martin de Porres') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Martin de Porres'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Martín de Porres') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Martín de Porres'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'San Miguel') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'San Miguel'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Santa Anita') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Santa Anita'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Santiago de Surco') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Santiago de Surco'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Surco') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Surco'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Surquillo') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Surquillo'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Villa El Salvador') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Villa El Salvador'); END;
IF NOT EXISTS (SELECT 1 FROM DISTRITOS WHERE nombre_distrito = N'Villa Maria del Triunfo') BEGIN INSERT INTO DISTRITOS (nombre_distrito) VALUES (N'Villa Maria del Triunfo'); END;
GO
PRINT 'Distritos sincronizados.';
GO

-- 2. INSERTAR TIENDAS Y USUARIOS CON ROL EMPLEADO (ROL 3)
-- Contraseña asignada para cada empleado de tienda: Vega2024*
-- Hash Bcrypt: $2y$10$w0JbC3z/8tD782r529wL..VzC9g4sYQhU8B7oJ31UvK.r6HcqPkea (o admin123)
-- Usamos el hash de admin123 para consistencia global: $2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e
DECLARE @id_distrito INT;
DECLARE @id_tienda INT;


-- Tienda y Empleado: AÑO NUEVO CASH
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Comas';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'AÑO NUEVO CASH')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'AÑO NUEVO CASH', N'Sede Comas', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmanonuevo@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - AÑO NUEVO CASH', N'tiendasmanonuevo@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: HUAMANTANGA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Puente Piedra';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'HUAMANTANGA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'HUAMANTANGA', N'Sede Puente Piedra', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmhuamantanga@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - HUAMANTANGA', N'tiendasmhuamantanga@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: INFANTAS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Martin de Porres';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'INFANTAS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'INFANTAS', N'Sede San Martin de Porres', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamyinfantas@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - INFANTAS', N'tiendamyinfantas@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: COLLIQUE
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Comas';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'COLLIQUE')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'COLLIQUE', N'Sede Comas', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamycollique@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - COLLIQUE', N'tiendamycollique@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: CHORRILLOS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Chorrillos';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'CHORRILLOS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'CHORRILLOS', N'Sede Chorrillos', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmchorrillos@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - CHORRILLOS', N'tiendasmchorrillos@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: SAN ANTONIO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Carabayllo';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'SAN ANTONIO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'SAN ANTONIO', N'Sede Carabayllo', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmsanantonio@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - SAN ANTONIO', N'tiendasmsanantonio@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: NARANJAL
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Martin de Porres';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'NARANJAL')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'NARANJAL', N'Sede San Martin de Porres', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmnaranjal@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - NARANJAL', N'tiendasmnaranjal@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: SAN DIEGO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Martin de Porres';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'SAN DIEGO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'SAN DIEGO', N'Sede San Martin de Porres', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmsandiego@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - SAN DIEGO', N'tiendasmsandiego@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: SURCO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santiago de Surco';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'SURCO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'SURCO', N'Sede Santiago de Surco', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmsurcobenavides@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - SURCO', N'tiendasmsurcobenavides@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MALVINAS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Cercado de Lima';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MALVINAS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MALVINAS', N'Sede Cercado de Lima', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmalvinas@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MALVINAS', N'tiendamkmalvinas@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: COLONIAL
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Lima';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'COLONIAL')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'COLONIAL', N'Sede Lima', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmcolonial@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - COLONIAL', N'tiendasmcolonial@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: PRODUCTORES
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santa Anita';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'PRODUCTORES')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'PRODUCTORES', N'Sede Santa Anita', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamyproductores@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - PRODUCTORES', N'tiendamyproductores@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: SANTO DOMINGO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Carabayllo';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'SANTO DOMINGO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'SANTO DOMINGO', N'Sede Carabayllo', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamksantodomingo@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - SANTO DOMINGO', N'tiendamksantodomingo@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: IZAGUIRRE
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Martín de Porres';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'IZAGUIRRE')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'IZAGUIRRE', N'Sede San Martín de Porres', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkizaguirre@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - IZAGUIRRE', N'tiendamkizaguirre@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: CHIMU
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Juan de Lurigancho';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'CHIMU')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'CHIMU', N'Sede San Juan de Lurigancho', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkchimu@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - CHIMU', N'tiendamkchimu@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: PALMERAS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Los Olivos';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'PALMERAS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'PALMERAS', N'Sede Los Olivos', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkpalmeras@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - PALMERAS', N'tiendamkpalmeras@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: CANEVARO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Lince';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'CANEVARO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'CANEVARO', N'Sede Lince', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkcanevaro@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - CANEVARO', N'tiendamkcanevaro@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: HUANDOY
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Los Olivos';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'HUANDOY')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'HUANDOY', N'Sede Los Olivos', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkhuandoy@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - HUANDOY', N'tiendamkhuandoy@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARIATEGUI
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Villa Maria del Triunfo';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARIATEGUI')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARIATEGUI', N'Sede Villa Maria del Triunfo', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkvillamariategui@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARIATEGUI', N'tiendamkvillamariategui@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: AVIACIÓN
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Borja';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'AVIACIÓN')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'AVIACIÓN', N'Sede San Borja', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkaviacion@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - AVIACIÓN', N'tiendamkaviacion@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MONTENEGRO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Juan de Lurigancho';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MONTENEGRO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MONTENEGRO', N'Sede San Juan de Lurigancho', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmontenegro@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MONTENEGRO', N'tiendamkmontenegro@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: FILOMENO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Rimac';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'FILOMENO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'FILOMENO', N'Sede Rimac', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmfilomeno@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - FILOMENO', N'tiendasmfilomeno@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: BOCANEGRA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Callao';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'BOCANEGRA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'BOCANEGRA', N'Sede Callao', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkbocanegra@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - BOCANEGRA', N'tiendamkbocanegra@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: UNIVERSAL
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santa Anita';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'UNIVERSAL')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'UNIVERSAL', N'Sede Santa Anita', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkuniversal@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - UNIVERSAL', N'tiendamkuniversal@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: AMARANTO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Carabayllo';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'AMARANTO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'AMARANTO', N'Sede Carabayllo', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkamaranto@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - AMARANTO', N'tiendamkamaranto@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MI PERU
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Mi Peru';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MI PERU')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MI PERU', N'Sede Mi Peru', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmiperu@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MI PERU', N'tiendamkmiperu@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: ESCARDO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Miguel';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'ESCARDO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'ESCARDO', N'Sede San Miguel', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkescardo@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - ESCARDO', N'tiendamkescardo@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: CANTA CALLAO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Callao';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'CANTA CALLAO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'CANTA CALLAO', N'Sede Callao', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkcantacallao@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - CANTA CALLAO', N'tiendamkcantacallao@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: LIMA VES
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Villa El Salvador';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'LIMA VES')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'LIMA VES', N'Sede Villa El Salvador', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmlimavilla@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - LIMA VES', N'tiendasmlimavilla@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: PRODUCTORES 3
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santa Anita';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'PRODUCTORES 3')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'PRODUCTORES 3', N'Sede Santa Anita', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamyproductores3@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - PRODUCTORES 3', N'tiendamyproductores3@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: RIOBAMBA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Martin de Porres';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'RIOBAMBA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'RIOBAMBA', N'Sede San Martin de Porres', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkriobamba@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - RIOBAMBA', N'tiendamkriobamba@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: SANTA CLARA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Ate';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'SANTA CLARA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'SANTA CLARA', N'Sede Ate', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmsantaclara@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - SANTA CLARA', N'tiendasmsantaclara@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: IGNACIO MERINO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Lince';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'IGNACIO MERINO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'IGNACIO MERINO', N'Sede Lince', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmerino@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - IGNACIO MERINO', N'tiendamkmerino@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: BELAUNDE
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Comas';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'BELAUNDE')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'BELAUNDE', N'Sede Comas', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmbelaunde@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - BELAUNDE', N'tiendasmbelaunde@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: CD TRAPICHE
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Comas';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'CD TRAPICHE')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'CD TRAPICHE', N'Sede Comas', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'franquicias.cd@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - CD TRAPICHE', N'franquicias.cd@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: INSTITUCIONES
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Comas';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'INSTITUCIONES')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'INSTITUCIONES', N'Sede Comas', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'ventasinstituciones@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - INSTITUCIONES', N'ventasinstituciones@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: PRODUCTORES FERIA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santa Anita';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'PRODUCTORES FERIA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'PRODUCTORES FERIA', N'Sede Santa Anita', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamyproductoresferia@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - PRODUCTORES FERIA', N'tiendamyproductoresferia@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: SAN ISIDRO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Isidro';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'SAN ISIDRO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'SAN ISIDRO', N'Sede San Isidro', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamsanisidro@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - SAN ISIDRO', N'tiendamsanisidro@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MINKA CASH
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Callao';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MINKA CASH')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MINKA CASH', N'Sede Callao', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmminka@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MINKA CASH', N'tiendasmminka@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA DELIVERY
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Comas';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA DELIVERY')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA DELIVERY', N'Sede Comas', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'asistente.delivery@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA DELIVERY', N'asistente.delivery@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: ROOSEVELT
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santiago de Surco';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'ROOSEVELT')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'ROOSEVELT', N'Sede Santiago de Surco', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendarooselvelt@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - ROOSEVELT', N'tiendarooselvelt@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: OVALO HIGUERETA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Santiago de Surco';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'OVALO HIGUERETA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'OVALO HIGUERETA', N'Sede Santiago de Surco', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkovalohiguereta@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - OVALO HIGUERETA', N'tiendamkovalohiguereta@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: BENAVIDES
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Surco';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'BENAVIDES')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'BENAVIDES', N'Sede Surco', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkbenavides@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - BENAVIDES', N'tiendamkbenavides@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: HUSARES JUNIN
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Jesus Maria';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'HUSARES JUNIN')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'HUSARES JUNIN', N'Sede Jesus Maria', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkhusaresjunin@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - HUSARES JUNIN', N'tiendamkhusaresjunin@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: ALAYZA CANEVARO II
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Lince';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'ALAYZA CANEVARO II')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'ALAYZA CANEVARO II', N'Sede Lince', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkalayza@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - ALAYZA CANEVARO II', N'tiendamkalayza@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARANGA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Miguel';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARANGA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARANGA', N'Sede San Miguel', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmaranga@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARANGA', N'tiendamkmaranga@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: LA CULTURA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Borja';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'LA CULTURA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'LA CULTURA', N'Sede San Borja', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamklacultura@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - LA CULTURA', N'tiendamklacultura@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: TRES REGIONES CASH
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Puente Piedra';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'TRES REGIONES CASH')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'TRES REGIONES CASH', N'Sede Puente Piedra', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmtresregiones@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - TRES REGIONES CASH', N'tiendasmtresregiones@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: NÉSTOR GAMBETA CASH
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Puente Piedra';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'NÉSTOR GAMBETA CASH')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'NÉSTOR GAMBETA CASH', N'Sede Puente Piedra', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendasmgambetta@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - NÉSTOR GAMBETA CASH', N'tiendasmgambetta@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA MARKET CLEMENT
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Pueblo Libre';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA MARKET CLEMENT')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA MARKET CLEMENT', N'Sede Pueblo Libre', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkclement@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA MARKET CLEMENT', N'tiendamkclement@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA MARKET PLAZA SANTA CATALINA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'La Victoria';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA MARKET PLAZA SANTA CATALINA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA MARKET PLAZA SANTA CATALINA', N'Sede La Victoria', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamksantacatalina@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA MARKET PLAZA SANTA CATALINA', N'tiendamksantacatalina@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET ROSPIGLIOSI
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Lince';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET ROSPIGLIOSI')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET ROSPIGLIOSI', N'Sede Lince', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkrospigliosi@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET ROSPIGLIOSI', N'tiendamkrospigliosi@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET VARA DE ORO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Juan de Lurigancho';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET VARA DE ORO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET VARA DE ORO', N'Sede San Juan de Lurigancho', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkvaradeoro@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET VARA DE ORO', N'tiendamkvaradeoro@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA MARKET LOS ALISOS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Los Olivos';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA MARKET LOS ALISOS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA MARKET LOS ALISOS', N'Sede Los Olivos', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkalisos@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA MARKET LOS ALISOS', N'tiendamkalisos@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA MARKET LOS OLIVOS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Martin de Porres';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA MARKET LOS OLIVOS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA MARKET LOS OLIVOS', N'Sede San Martin de Porres', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamklosolivos@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA MARKET LOS OLIVOS', N'tiendamklosolivos@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA MARKET LORETO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Breña';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA MARKET LORETO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA MARKET LORETO', N'Sede Breña', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkloreto@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA MARKET LORETO', N'tiendamkloreto@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET MARIANO PASTOR
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Pueblo Libre';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET MARIANO PASTOR')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET MARIANO PASTOR', N'Sede Pueblo Libre', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmarianopastor@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET MARIANO PASTOR', N'tiendamkmarianopastor@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: VEGA MARKET AMANCAES 3
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Rimac';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'VEGA MARKET AMANCAES 3')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'VEGA MARKET AMANCAES 3', N'Sede Rimac', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkamancaes3@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - VEGA MARKET AMANCAES 3', N'tiendamkamancaes3@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET ALAMEDA LOS CEDROS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Chorrillos';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET ALAMEDA LOS CEDROS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET ALAMEDA LOS CEDROS', N'Sede Chorrillos', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkalamedaloscedros@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET ALAMEDA LOS CEDROS', N'tiendamkalamedaloscedros@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET SALAMANCA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Ate';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET SALAMANCA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET SALAMANCA', N'Sede Ate', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamksalamanca@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET SALAMANCA', N'tiendamksalamanca@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET BELLAVISTA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Bella Vista';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET BELLAVISTA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET BELLAVISTA', N'Sede Bella Vista', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkbellavista@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET BELLAVISTA', N'tiendamkbellavista@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET MARIANO CORNEJO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Pueblo Libre';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET MARIANO CORNEJO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET MARIANO CORNEJO', N'Sede Pueblo Libre', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmarianocornejo@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET MARIANO CORNEJO', N'tiendamkmarianocornejo@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET ALAMEDA LOS CÓNDORES
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'La Molina';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET ALAMEDA LOS CÓNDORES')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET ALAMEDA LOS CÓNDORES', N'Sede La Molina', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkalamedaloscondores@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET ALAMEDA LOS CÓNDORES', N'tiendamkalamedaloscondores@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET LAS GUINDAS - EL AGUSTINO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'El Agustino';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET LAS GUINDAS - EL AGUSTINO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET LAS GUINDAS - EL AGUSTINO', N'Sede El Agustino', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamklasguindas@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET LAS GUINDAS - EL AGUSTINO', N'tiendamklasguindas@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET SAN LUIS
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'San Luis';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET SAN LUIS')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET SAN LUIS', N'Sede San Luis', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamksanluis@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET SAN LUIS', N'tiendamksanluis@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET INDEPENDENCIA
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Independencia';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET INDEPENDENCIA')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET INDEPENDENCIA', N'Sede Independencia', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkindependencia@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET INDEPENDENCIA', N'tiendamkindependencia@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET GUARDIA CIVIL NORTE
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Surco';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET GUARDIA CIVIL NORTE')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET GUARDIA CIVIL NORTE', N'Sede Surco', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkguardiacivil@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET GUARDIA CIVIL NORTE', N'tiendamkguardiacivil@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET VILLARAN-SURQUILLO
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Surquillo';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET VILLARAN-SURQUILLO')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET VILLARAN-SURQUILLO', N'Sede Surquillo', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmanuelvillaran@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET VILLARAN-SURQUILLO', N'tiendamkmanuelvillaran@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET MICAELA - ATE VITARTE
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Ate';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET MICAELA - ATE VITARTE')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET MICAELA - ATE VITARTE', N'Sede Ate', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamkmicaelabastidas@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET MICAELA - ATE VITARTE', N'tiendamkmicaelabastidas@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;


-- Tienda y Empleado: MARKET TOMASAL
SELECT @id_distrito = id_distrito FROM DISTRITOS WHERE nombre_distrito = N'Surco';

IF NOT EXISTS (SELECT 1 FROM TIENDAS WHERE nombre_tienda = N'MARKET TOMASAL')
BEGIN
    INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES (N'MARKET TOMASAL', N'Sede Surco', @id_distrito);
END;

IF NOT EXISTS (SELECT 1 FROM USUARIOS WHERE email = N'tiendamktomasal@corporacionvega.pe')
BEGIN
    -- Rol 3 = Empleado, Area 4 = Mantenimiento/Operaciones
    INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area, activo)
    VALUES (N'Encargado - MARKET TOMASAL', N'tiendamktomasal@corporacionvega.pe', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4, 1);
END;

GO
PRINT 'Todas las tiendas y usuarios empleados han sido creados exitosamente.';
GO