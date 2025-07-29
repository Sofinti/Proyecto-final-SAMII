CREATE DATABASE IF NOT EXISTS Cooperativa;
use Cooperativa;
-- Tabla Administrador
CREATE TABLE Administrador (
    Id_Administrador INT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Contraseña VARCHAR(100) NOT NULL,
    Correo VARCHAR(100) NOT NULL
);

-- Teléfono del Administrador
CREATE TABLE TelefonoAdministrador (
    Id_Administrador INT,
    Telefono VARCHAR(15),
    PRIMARY KEY (Id_Administrador, Telefono),
    FOREIGN KEY (Id_Administrador) REFERENCES Administrador(Id_Administrador)
);

-- Tabla Usuario
CREATE TABLE Usuario (
    Id_Usuario INT PRIMARY KEY,
    Cedula VARCHAR(12) NOT NULL,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    FechaNacimiento DATE NOT NULL,
    Genero VARCHAR (50) NOT NULL,
    Correo VARCHAR(100) NOT NULL,
    Contraseña VARCHAR(100) NOT NULL,
    Direccion VARCHAR(100) NOT NULL,
    CantidadPersonas INT NOT NULL
);

-- Teléfono del Usuario
CREATE TABLE TelefonoUsuario (
    Id_Usuario INT,
    Telefono INT,
    PRIMARY KEY (Id_Usuario, Telefono),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);

-- NumTarjeta del Usuario
CREATE TABLE NumerosTarjeta (
    Id_Usuario INT,
    NumTarjeta INT,
    PRIMARY KEY (Id_Usuario, NumTarjeta),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);

-- Horas Laborales
CREATE TABLE HorasLaborales (
    Id_Horas INT PRIMARY KEY,
    Id_Usuario INT,
    Cantidad_Horas INT,
    Tiempo VARCHAR(20),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);

-- Pagos
CREATE TABLE Pago (
    Id_Pago INT PRIMARY KEY,
    Id_Usuario INT,
    Costo DECIMAL(10,2),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);

-- Relación Realiza Pago
CREATE TABLE Realiza (
    Id_Horas INT,
    Id_Usuario INT,
    PRIMARY KEY (Id_Horas, Id_Usuario),
    FOREIGN KEY (Id_Horas) REFERENCES HorasLaborales(Id_Horas),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);

-- Relación Ejecuta Pago
CREATE TABLE Ejecuta (
    Id_Pago INT,
    Id_Usuario INT,
    PRIMARY KEY (Id_Pago, Id_Usuario),
    FOREIGN KEY (Id_Pago) REFERENCES Pago(Id_Pago),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);

-- Relación Admite Pago
CREATE TABLE Admite (
    Id_Administrador INT,
    Id_Usuario INT,
    PRIMARY KEY (Id_Administrador, Id_Usuario),
    FOREIGN KEY (Id_Administrador) REFERENCES Administrador(Id_Administrador),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario(Id_Usuario)
);