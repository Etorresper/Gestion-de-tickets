CREATE DATABASE IF NOT EXISTS sistema_tickets;
USE sistema_tickets;

CREATE TABLE ROLES (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

CREATE TABLE AREAS (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nombre_area VARCHAR(100) NOT NULL
);

CREATE TABLE CATEGORIAS (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    palabras_clave TEXT,
    id_area_responsable INT,
    FOREIGN KEY (id_area_responsable) REFERENCES AREAS(id_area)
);

CREATE TABLE ESTADOS (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estado VARCHAR(50) NOT NULL,
    color_hex VARCHAR(7)
);

CREATE TABLE DISTRITOS (
    id_distrito INT AUTO_INCREMENT PRIMARY KEY,
    nombre_distrito VARCHAR(100) NOT NULL
);

CREATE TABLE TIENDAS (
    id_tienda INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tienda VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    id_distrito INT,
    FOREIGN KEY (id_distrito) REFERENCES DISTRITOS(id_distrito)
);

CREATE TABLE USUARIOS (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT,
    id_area INT,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_rol) REFERENCES ROLES(id_rol),
    FOREIGN KEY (id_area) REFERENCES AREAS(id_area)
);

CREATE TABLE TICKETS (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    codigo_ticket VARCHAR(20) NOT NULL UNIQUE,
    id_solicitante INT,
    asunto VARCHAR(200) NOT NULL,
    descripcion TEXT,
    id_categoria INT,
    id_estado INT,
    prioridad VARCHAR(20),
    id_tienda INT,
    fecha_vencimiento DATETIME,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_cierre DATETIME NULL,
    FOREIGN KEY (id_solicitante) REFERENCES USUARIOS(id_usuario),
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIAS(id_categoria),
    FOREIGN KEY (id_estado) REFERENCES ESTADOS(id_estado),
    FOREIGN KEY (id_tienda) REFERENCES TIENDAS(id_tienda)
);

CREATE TABLE ASIGNACIONES (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT,
    id_tecnico INT,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES TICKETS(id_ticket),
    FOREIGN KEY (id_tecnico) REFERENCES USUARIOS(id_usuario)
);

CREATE TABLE HISTORIAL_TICKETS (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT,
    id_usuario INT,
    accion VARCHAR(100),
    detalle TEXT,
    fecha_accion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES TICKETS(id_ticket),
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario)
);

CREATE TABLE ADJUNTOS (
    id_adjunto INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT,
    nombre_archivo VARCHAR(255),
    ruta_archivo VARCHAR(255),
    tipo_mime VARCHAR(50),
    FOREIGN KEY (id_ticket) REFERENCES TICKETS(id_ticket)
);

-- Insertar datos semilla
INSERT INTO ROLES (nombre_rol) VALUES ('Administrador'), ('Soporte'), ('Empleado');
INSERT INTO AREAS (nombre_area) VALUES ('Sistemas'), ('Redes'), ('Hardware'), ('Mantenimiento');
INSERT INTO ESTADOS (nombre_estado, color_hex) VALUES ('Pendiente', '#dc3545'), ('En Proceso', '#ffc107'), ('Resuelto', '#28a745'), ('Cerrado', '#6c757d');
INSERT INTO CATEGORIAS (nombre_categoria, palabras_clave, id_area_responsable) VALUES 
('Problema de Red', '["internet", "wifi", "conexion", "red"]', 2),
('Hardware/Equipos', '["impresora", "teclado", "mouse", "monitor", "pantalla", "pc", "laptop"]', 3),
('Software/Sistemas', '["sistema", "login", "error", "app", "aplicacion", "lento"]', 1);

-- Usuario admin por defecto (pass: admin123)
-- El hash generado es de 'admin123' usando PASSWORD_DEFAULT (Bcrypt)
INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_area) VALUES 
('Admin Sistema', 'admin@empresa.com', '$2y$10$Wr3RBqQYnz93ucF.FpNq7uj4tgtrPv3Q29BphDeb29P9VovlK9d8e', 1, 1);
