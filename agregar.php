<?php
include("PHP/conexion.php");

if ($_POST) {
    $Nombre             = $_POST['Nombres'];
    $Apellido           = $_POST['Apellidos'];
    $FechaNacimiento    = $_POST['FechaN'];
    $Cedula             = $_POST['CI'];
    $Genero             = $_POST['optGenero'];
    $Telefono           = $_POST['Tel'];
    $Correo             = $_POST['Email'];
    $Direccion          = $_POST['Direccion'];
    $CantidadPersonas   = $_POST['CantPersonas'];
    $Contrasenia        = $_POST['Contrasenia'];

    // Encriptar la contraseña
    $Contrasenia = password_hash($Contrasenia, PASSWORD_DEFAULT);

    // Insertar en Usuarios
    $sql = "INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Genero, Correo, Contraseña, Direccion, CantidadPersonas)
            VALUES ('$Cedula', '$Nombre', '$Apellido', '$FechaNacimiento', '$Genero', '$Correo', '$Contrasenia', '$Direccion', '$CantidadPersonas')";

    if ($con->query($sql) === TRUE) {
        $ultimoId = $con->insert_id; // ID generado automáticamente

        // Insertar en TelefonoUsuario
        $sqlTelefono = "INSERT INTO TelefonoUsuario (Id_Usuario, Telefono) VALUES ('$ultimoId', '$Telefono')";
        $con->query($sqlTelefono);

        echo "Nuevo registro creado satisfactoriamente";
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }

    $con->close();
}
?>