-- -- db_schema/001_schema.sql
-- -- 1. Crea la tabla de usuarios
CREATE DATABASE IF NOT EXISTS samantha_db;

USE samantha_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('administrador', 'miembro_comision', 'usuario') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);