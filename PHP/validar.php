<?php
include("bd.php");

echo "<pre>";
print_r($_POST);
echo "</pre>";

$correo      = $_POST['Email'];
$contrasenia = $_POST['Contrasenia'];

// Iniciar sesión
session_start();
$_SESSION['Email'] = $correo;

// Buscar al usuario por su email
$sql = "SELECT * FROM Usuarios WHERE Correo = '$correo'";
$resultado = mysqli_query($con, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);

    // Verificar la contraseña con password_verify
    if (password_verify($contrasenia, $usuario['Contraseña'])) {
        // Redirigir al home si todo está bien
        header("Location: home.php");
        exit();
    } else {
        // Contraseña incorrecta
        echo "<h1 class='bad'>Correo o contraseña incorrecto, verifique sus datos</h1>";
    }
} else {
    // Email no encontrado
    echo "<h1 class='bad'>Correo o contraseña incorrecto, verifique sus datos</h1>";
}

// Liberar memoria y cerrar conexión
mysqli_free_result($resultado);
mysqli_close($con);
?>