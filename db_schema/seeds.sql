USE samantha_db;

-- Insertar usuarios de prueba
-- La contraseña hasheada corresponde a "12345678"
INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Genero, Correo, Contrasenia, Direccion, CantidadPersonas) VALUES 
('12345678', 'Juan', 'Palmares', '1990-01-15', 'Masculino', 'juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Calle 123', 1),
('87654321', 'Lucía', 'Fernández', '1995-05-20', 'Femenino', 'lucia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Avenida 456', 2),
('11223344', 'Mateo', 'Rodríguez', '1988-08-10', 'Masculino', 'mateo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Boulevard 789', 3),
('55667788', 'Nicolás', 'Gómez', '1992-12-25', 'Masculino', 'nicolas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Plaza 321', 1);

-- Usuario para tus pruebas (contraseña: "2341412421412")
INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Genero, Correo, Contrasenia, Direccion, CantidadPersonas) VALUES 
('99999999', 'Facu', 'Teemo', '2000-01-01', 'Masculino', 'facuteemo@gmail.com', '$2y$10$YgB7Z8JEkostPRJ3fHxXPuHztKhBPwzCiH9z1Z0K0kGBvOmjD8vhm', 'Mi Casa 123', 1);