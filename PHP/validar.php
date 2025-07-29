<?php
include("bd.php");
$Email = $_POST['Email'];
$contrasenia = $_POST['contrasenia'];

// Creamos una sesión basada en un identificador
// de sesión pasado mediante una petición GET o POST
session_start();
$SESSION['Email'] = $Email;

// Sentencia de consulta para saber si ese usr con esa pwd esta en la BD.
$sql="SELECT * FROM Email
    WHERE Email ='$Email' AND contrasenia = '$contrasenia'";
$resultado = mysqli_query($con,$sql);
$filas = mysqli_num_rows($resultado);

// Validamos
if($filas){
    Header("Location: home.php");
}else{
    ?>
    <?php 
        include("index.php");
    ?>
    <h1 class="bad">Email o contraseña incorrecto, verifique sus datos </h1>

<?php
}

// Vaciamos el resultado y cerramos la conexion
mysqli_free_result($resultado);
mysqli_close($con);
?>
