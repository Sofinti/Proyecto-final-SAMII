USE samantha_db;

-- Insertar usuarios de prueba
-- La contraseña hasheada corresponde a "12345678"
INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Correo, Contrasenia, Direccion) VALUES 
('12345678', 'Juan', 'Palmares', '1990-01-15', 'juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Calle 123'),
('87654321', 'Lucía', 'Fernández', '1995-05-20', 'lucia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Avenida 456'),
('11223344', 'Mateo', 'Rodríguez', '1988-08-10', 'mateo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Boulevard 789'),
('55667788', 'Nicolás', 'Gómez', '1992-12-25', 'nicolas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Plaza 321');

-- Usuario para tus pruebas (contraseña: "2341412421412")
INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Correo, Contrasenia, Direccion) VALUES 
('99999999', 'Facu', 'Teemo', '2000-01-01', 'facuteemo@gmail.com', '$2y$10$YgB7Z8JEkostPRJ3fHxXPuHztKhBPwzCiH9z1Z0K0kGBvOmjD8vhm', 'Mi Casa 123');