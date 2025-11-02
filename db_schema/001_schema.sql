-- db_schema/001_schema.sql
-- 1. Crea la tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('administrador', 'miembro_comision', 'usuario') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Inserta un usuario administrador (contraseña '123456')
INSERT INTO users (username, password_hash, role) VALUES (
    'admin',
    '$2y$10$oY7b4/3U2J.c7X3m3jA3VOGfJ2mF/j.v2vV4p8A5eJ0gR3L2gN7X2',
    'administrador'
);
