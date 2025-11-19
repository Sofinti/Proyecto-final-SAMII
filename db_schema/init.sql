-- db_schema/init.sql

CREATE DATABASE IF NOT EXISTS samantha_db;
USE samantha_db;


-- ========================================================
-- 1. TABLA USUARIOS (ACTUALIZADA)
-- ========================================================
-- Incluye campos nuevos (Telefono) y mantiene los de nuestro sistema (Activo, Id_TipoUsuario)
CREATE TABLE Usuarios (
    Id_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Cedula VARCHAR(20) NOT NULL,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    FechaNacimiento DATE NOT NULL,
    Telefono VARCHAR(20) NULL,
    Correo VARCHAR(100) NOT NULL UNIQUE,
    Contrasenia VARCHAR(255) NOT NULL,
    Direccion VARCHAR(100) NOT NULL,
    
    -- Control de Acceso (Vital para tu Login)
    Id_TipoUsuario INT DEFAULT 2,     -- 1=Admin, 2=Socio
    Activo TINYINT DEFAULT 0,         -- 0=Pendiente, 1=Habilitado
    HabilitadoTrabajo TINYINT DEFAULT 0, -- Nuevo campo
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ========================================================
-- 2. TABLA HORAS LABORALES (LA NUEVA)
-- ========================================================
CREATE TABLE HorasLaborales (
    Id_Horas INT AUTO_INCREMENT PRIMARY KEY,
    Id_Usuario INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    SemanaCompletada VARCHAR(50) NULL,
    HorasTrabajadas DECIMAL(10,2) NOT NULL, 
    HorasFaltantes DECIMAL(10,2) DEFAULT 0,
    Motivo TEXT, 
    SolicitaExoneracion TINYINT(1) DEFAULT 0,
    MontoCompensatorio DECIMAL(10,2) DEFAULT 0,
    Estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    FechaRevision DATETIME NULL,
    Id_AdminAprobador INT NULL,
    
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario) ON DELETE CASCADE
);

-- ========================================================
-- 3. OTRAS TABLAS NUEVAS (Reordenadas para que no rompan la llave foránea)
-- ========================================================

-- La tabla Administrador ya no tiene FK, así que va primero
CREATE TABLE Administrador (
    Id_Administrador INT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Correo VARCHAR(100) NOT NULL,
    Contrasenia VARCHAR(100) NOT NULL
);

CREATE TABLE TelefonoAdministrador (
    Id_Administrador INT,
    Telefono VARCHAR(15),
    PRIMARY KEY (Id_Administrador, Telefono),
    FOREIGN KEY (Id_Administrador) REFERENCES Administrador(Id_Administrador)
);
-- Teléfono de Usuario y Tarjeta no tienen problemas de dependencia con Usuarios

CREATE TABLE TelefonoUsuario (
    Id_Usuario INT,
    Telefono VARCHAR(15),
    PRIMARY KEY (Id_Usuario, Telefono),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE NumerosTarjeta (
    Id_Usuario INT,
    NumTarjeta VARCHAR(20),
    PRIMARY KEY (Id_Usuario, NumTarjeta),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE UnidadHabitacional (
    Id_Unidad INT AUTO_INCREMENT PRIMARY KEY,
    Id_Usuario INT,
    NumeroUnidad VARCHAR(50),
    Descripcion VARCHAR(255),
    Estado ENUM('Ocupada', 'Libre', 'Mantenimiento') DEFAULT 'Ocupada',
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE Comision (
    Id_Comision INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100),
    Descripcion TEXT
);

-- Las tablas que referencian a Pagos y Horas Laborales deben ir al final
CREATE TABLE Pago (
    Id_Pago INT PRIMARY KEY,
    Id_Usuario INT,
    Costo DECIMAL(10,2),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE PagoInicial (
    id_PagoInicial INT AUTO_INCREMENT PRIMARY KEY,
    Id_Usuario INT,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Monto DECIMAL(10,2),
    Comprobante_url VARCHAR(255),
    Estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE PagoMensual (
    Id_PagoMensual INT AUTO_INCREMENT PRIMARY KEY,
    Id_Usuario INT,
    Mes TINYINT,
    Ano SMALLINT,
    Monto DECIMAL(10,2),
    Comprobante_url VARCHAR(255),
    Estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE UsuarioComision (
    Id_Usuario INT,
    Id_Comision INT,
    RolEnComision ENUM('Presidente', 'Secretario', 'Vocal', 'Miembro') DEFAULT 'Miembro',
    PRIMARY KEY (Id_Usuario, Id_Comision),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario),
    FOREIGN KEY (Id_Comision) REFERENCES Comision(Id_Comision)
);

-- Las tablas de relación deben ir al final de todo
CREATE TABLE Realiza (
    Id_Horas INT,
    Id_Usuario INT,
    PRIMARY KEY (Id_Horas, Id_Usuario),
    FOREIGN KEY (Id_Horas) REFERENCES HorasLaborales(Id_Horas),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE Ejecuta (
    Id_Pago INT,
    Id_Usuario INT,
    PRIMARY KEY (Id_Pago, Id_Usuario),
    FOREIGN KEY (Id_Pago) REFERENCES Pago(Id_Pago),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE Admite (
    Id_Administrador INT,
    Id_Usuario INT,
    PRIMARY KEY (Id_Administrador, Id_Usuario),
    FOREIGN KEY (Id_Administrador) REFERENCES Administrador(Id_Administrador),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);