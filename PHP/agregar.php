<?php
include("/PHP/conexion.php");
if($_POST){
    $Nombres       =  $_POST['Nombres'];
    $Apellidos     =  $_POST['Apellidos'];
    $FechaN        =  $_POST['FechaN'];
    $CI            =  $_POST['CI'];
    $optGenero     =  $_POST['optGenero'];
    $Tel           =  $_POST['Tel'];
    $Email         =  $_POST['Email'];
    $Direccion     =  $_POST['Direccion'];
    $CantPersonas  =  $_POST['CantPersonas'];
    $password      =  $_POST['contraseña'];

}

$hash = password_hash($contrasenia, PASSWORD_DEFAULT);

// creamos sentencia mysql de inserción
$sql = "INSERT INTO `usuarios`(`Nombres`, `Apellidos`, `FechaN`, `CI`, `optGenero`, `Tel`, `Email`, `Direccion`, `CantPersonas`, `Contrasenia`) 
VALUES ('$Nombres','$Apellidos','$FechaN','$CI', '$optGenero','$Tel' ,'$Email' ,'$Direccion' ,'$CantPersonas' ,'$Contrasenia')";

if($con->query($sql) === TRUE) {
  echo "Nuevo registro creado satisfactoriamente";
} else {
  echo "Error: " . $sql . "</n>" . $con->error;
}

$con->close();
?>