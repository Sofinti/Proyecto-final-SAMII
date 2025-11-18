CREATE DATABASE IF NOT EXISTS samantha_db;
use samantha_db;

-- Tabla Usuarios
CREATE TABLE Usuarios (
    Id_Usuario INT AUTO_INCREMENT  PRIMARY KEY,
    Cedula VARCHAR(12) NOT NULL,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    FechaNacimiento DATE NOT NULL,
    Telefono VARCHAR(15),
    Correo VARCHAR(100) NOT NULL,
    Contrasenia VARCHAR(100) NOT NULL,
    Direccion VARCHAR(100) NOT NULL,
    Rol ENUM NOT NULL,
    Estado ENUM NOT NULL,
    FechaRegistro DATETIME NOT NULL
);

-- NumTarjeta del Usuario
CREATE TABLE NumerosTarjeta (
    Id_Usuario INT,
    NumTarjeta INT,
    PRIMARY KEY (Id_Usuario, NumTarjeta),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

--Unidad de vivienda
CREATE TABLE UnidadHabitacional (
    Id_Unidad INT PRIMARY KEY,
    Id_Usuario INT,
    NumeroUnidad VARCHAR,
    Descripcion VARCHAR,
    Estado ENUM,
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);


-- Horas Laborales
CREATE TABLE HorasLaborales (
    Id_Horas INT PRIMARY KEY,
    Id_Usuario INT,
    SemanaCompletada VARCHAR,
    HorasTrabajadas DECIMAL,
    HorasFaltantes DECIMAL,
    Motivo TEXT,
    SolicitaExoneracion BOOLEAN,
    MontoCompensatorio DECIMAL,
    Estado ENUM,
    FechaRevision DATETIME,
    Id_AdminAprobador NULL
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

-- Pagos
CREATE TABLE PagoInicial (
    id_PagoInicial INT PRIMARY KEY,
    Id_Usuario INT,
    Fecha DATETIME,
    Monto DECIMAL,
    Comprobante_url VARCHAR,
    Estado ENUM,
    FechaRevision DATETIME,
    Id_AdminAprobador NULL
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

CREATE TABLE PagoMensual (
    Id_PagoMensual INT PRIMARY KEY,
    Id_Usuario INT,
    Mes TINYINT,
    Ano SMALLINT,
    Monto DECIMAL
    Comprobante_url VARCHAR,
    Estado ENUM,
    FechaRevision DATETIME,
    Id_AdminAprobador NULL
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
);

--Comisión
CREATE TABLE Comision (
    Id_Comision INT PRIMARY KEY,
    Nombre VARCHAR,
    Descripcion TEXT
);

--Usuario de una comisión
CREATE TABLE UsuarioComision (
    Id_Usuario INT,
    Id_Comision INT,
    RolEnComision ENUM,
    PRIMARY KEY (Id_Usuario, Id_Comision),
    FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario),
    FOREIGN KEY (Id_Comision) REFERENCES Comision(Id_Comision)
);