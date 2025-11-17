<?php
session_start();

require_once(__DIR__ . '/../config/Database.php');

$correo      = $_POST['Email'];
$contrasenia = $_POST['Contrasenia'];

$_SESSION['Email'] = $correo;

$pdo = Database::connect();

// Buscar si el usuario existe
$sql = "SELECT * FROM Usuarios WHERE Correo = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$correo]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Usuario existe - verificar contraseña
    if (password_verify($contrasenia, $usuario['Contrasenia'])) {
        // Login exitoso
        $_SESSION['Nombre'] = $usuario['Nombre'];
        $_SESSION['Id_Usuario'] = $usuario['Id_Usuario'];
        header("Location: home.php");
        exit();
    } else {
        echo "<h1 class='bad'>Correo o contraseña incorrecto, verifique sus datos</h1>";
    }
} else {
    // Usuario NO existe - crear uno nuevo automáticamente
    
    // Hashear la contraseña
    $contraseniaHash = password_hash($contrasenia, PASSWORD_DEFAULT);
    
    // Generar datos por defecto para los campos requeridos
    // Cédula: últimos 10 dígitos del timestamp (cabe en VARCHAR(12))
    $cedula = substr(time(), -10);
    $nombre = explode('@', $correo)[0]; // Usar la parte antes del @ como nombre
    $apellido = 'Usuario';
    $fechaNacimiento = '2000-01-01';
    $genero = 'No especificado';
    $direccion = 'No especificada';
    $cantidadPersonas = 1;
    
    // Insertar el nuevo usuario
    $sqlInsert = "INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Genero, Correo, Contrasenia, Direccion, CantidadPersonas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = $pdo->prepare($sqlInsert);
    $resultado = $stmtInsert->execute([
        $cedula,
        $nombre,
        $apellido,
        $fechaNacimiento,
        $genero,
        $correo,
        $contraseniaHash,
        $direccion,
        $cantidadPersonas
    ]);
    
    if ($resultado) {
        // Usuario creado exitosamente - iniciar sesión automáticamente
        $_SESSION['Nombre'] = $nombre;
        $_SESSION['Id_Usuario'] = $pdo->lastInsertId();
        header("Location: home.php");
        exit();
    } else {
        echo "<h1 class='bad'>Error al crear el usuario. Intenta de nuevo.</h1>";
    }
}
?>