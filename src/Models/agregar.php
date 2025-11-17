<?php
require_once(__DIR__ . '/../config/Database.php');

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre             = trim($_POST['Nombres']);
    $Apellido           = trim($_POST['Apellidos']);
    $FechaNacimiento    = $_POST['FechaN'];
    $Cedula             = trim($_POST['CI']);
    $Genero             = $_POST['optGenero'];
    $Telefono           = trim($_POST['Tel']);
    $Correo             = trim($_POST['Email']);
    $Direccion          = trim($_POST['Direccion']);
    $CantidadPersonas   = (int)$_POST['CantPersonas'];
    $Contrasenia        = $_POST['Contrasenia'];

    // Validar campos obligatorios
    if (empty($Nombre) || empty($Apellido) || empty($Cedula) || empty($Correo) || empty($Contrasenia)) {
        die("Por favor completa todos los campos obligatorios.");
    }

    // Encriptar la contraseña
    $ContraseniaHash = password_hash($Contrasenia, PASSWORD_DEFAULT);

    try {
        // IMPORTANTE: Usar prepared statements para evitar SQL injection
        // Y corregir el nombre de la tabla a "Usuarios" (plural) y columna "Contrasenia" (sin tilde)
        $sql = "INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Genero, Correo, Contrasenia, Direccion, CantidadPersonas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            $Cedula,
            $Nombre,
            $Apellido,
            $FechaNacimiento,
            $Genero,
            $Correo,
            $ContraseniaHash,
            $Direccion,
            $CantidadPersonas
        ]);

        if ($resultado) {
            // Obtener el ID del usuario insertado
            $ultimoId = $pdo->lastInsertId();

            // Insertar en TelefonoUsuario
            $sqlTelefono = "INSERT INTO TelefonoUsuario (Id_Usuario, Telefono) VALUES (?, ?)";
            $stmtTelefono = $pdo->prepare($sqlTelefono);
            $stmtTelefono->execute([$ultimoId, $Telefono]);

            // Iniciar sesión automáticamente
            session_start();
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $ultimoId;
            $_SESSION['Email'] = $Correo;
            $_SESSION['Nombre'] = $Nombre;

            // Redirigir a la página principal
            header("Location: /");
            exit();
        }

    } catch (PDOException $e) {
        die("Error al crear el usuario: " . $e->getMessage());
    }
} else {
    die("Método no permitido");
}
?>