USE sistema_tickets;

-- Datos para DISTRITOS
INSERT INTO DISTRITOS (nombre_distrito) VALUES ('Surco'), ('Miraflores'), ('San Isidro'), ('Lince'), ('Ate');

-- Datos para TIENDAS
INSERT INTO TIENDAS (nombre_tienda, direccion, id_distrito) VALUES 
('Tienda Surco 01', 'Av. Benavides 1234', 1),
('Tienda Miraflores Central', 'Calle Shell 456', 2),
('Tienda San Isidro Business', 'Av. Rivera Navarrete 789', 3),
('Tienda Lince Express', 'Av. Arequipa 2010', 4),
('Tienda Ate Industrial', 'Carr. Central Km 5', 5);

-- Más Usuarios (Empleados y Soporte)
-- Las contraseñas de todos ellos es: admin123
INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area) VALUES 
('Carlos Empleado', 'carlos@empresa.com', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4),
('Maria Ventas', 'maria@empresa.com', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 3, 4),
('Tecnico Juan', 'juan.soporte@empresa.com', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 2, 1),
('Tecnica Ana', 'ana.redes@empresa.com', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 2, 2);

-- Insertar Tickets de prueba
-- Estados: 1:Pendiente, 2:En Proceso, 3:Resuelto, 4:Cerrado
-- Categorías: 1:Red, 2:Hardware, 3:Software

INSERT INTO TICKETS (codigo_ticket, id_solicitante, asunto, descripcion, id_categoria, id_estado, fecha_creacion, fecha_actualizacion) VALUES 
('TKT-2023-1001', 2, 'No hay internet en mi laptop', 'He intentado conectarme a la red de la oficina pero aparece "Sin acceso a internet".', 1, 2, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
('TKT-2023-1002', 3, 'Impresora atasca papel', 'La impresora del pasillo principal está atascando las hojas cada vez que intento imprimir facturas.', 2, 1, DATE_SUB(NOW(), INTERVAL 5 HOUR), NOW()),
('TKT-2023-1003', 2, 'Sistema de ventas muy lento', 'El sistema está tardando mucho en cargar los productos. Los clientes se están quejando.', 3, 3, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
('TKT-2023-1004', 3, 'Olvide mi contraseña de correo', 'Por favor, necesito restablecer mi contraseña corporativa porque no la recuerdo.', 3, 4, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY)),
('TKT-2023-1005', 2, 'Mi mouse no funciona', 'El click derecho de mi mouse dejó de funcionar, ¿pueden darme uno nuevo?', 2, 1, DATE_SUB(NOW(), INTERVAL 2 HOUR), NOW());

-- Insertar Asignaciones
INSERT INTO ASIGNACIONES (id_ticket, id_tecnico, fecha_asignacion) VALUES 
(1, 5, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 4, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Insertar Historial
INSERT INTO HISTORIAL_TICKETS (id_ticket, id_usuario, accion, detalle, fecha_accion) VALUES 
(1, 2, 'Creación', 'Ticket creado manualmente por el usuario.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 5, 'En Proceso', 'Revisando la configuración del router.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 3, 'Creación', 'Ticket creado manualmente por el usuario.', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(3, 2, 'Creación', 'Ticket creado manualmente por el usuario.', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 4, 'Asignación', 'Asignado a Técnico Juan.', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 4, 'Resuelto', 'Se limpió la caché del sistema y se reinició el servidor de base de datos.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 3, 'Creación', 'Ticket creado manualmente por el usuario.', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(4, 4, 'Cerrado', 'Se reseteó la contraseña y se envió por SMS.', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(5, 2, 'Creación', 'Ticket creado automáticamente.', DATE_SUB(NOW(), INTERVAL 2 HOUR));
