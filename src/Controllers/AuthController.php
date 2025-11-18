<?php
// /src/Controllers/AuthController.php

class AuthController {
    
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm() {
        require __DIR__ . '/../Views/html/logIn.html'; 
    }

    /**
     * Procesa el login
     */
    public function handleLogin() {
        try {
            // 1. Recoge datos
            $correo = $_POST['Email'] ?? '';
            $contrasenia = $_POST['Contrasenia'] ?? '';
            
            if (empty($correo) || empty($contrasenia)) {
                echo "<h1 class='bad'>Por favor completa todos los campos</h1>";
                echo "<a href='/login'>Volver al login</a>";
                return;
            }

            // 2. Conecta con base de datos
            $pdo = Database::connect();
            
            // 3. Busca si el usuario existe
            $sql = "SELECT * FROM Usuarios WHERE Correo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Usuario existe - verificar contraseña
            if (password_verify($contrasenia, $usuario['Contrasenia'])) {
                // Login exitoso
                session_start();
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $usuario['Id_Usuario'];
                $_SESSION['Email'] = $usuario['Correo'];
                $_SESSION['Nombre'] = $usuario['Nombre'];
                    
                // Redirigir usando ruta absoluta
                header('Location: http://127.0.0.1/');
                exit();
            } else {
                echo "<h1 class='bad'>Correo o contraseña incorrecto</h1>";
                echo "<a href='/login'>Volver al login</a>";
            }

        } catch (Exception $e) {
            echo "<h1 class='bad'>Error: " . $e->getMessage() . "</h1>";
            echo "<a href='/login'>Volver al login</a>";
        }
    }

    /**
     * Cierra la sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: http://127.0.0.1/login');
        exit();
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegistroForm() {
        require __DIR__ . '/src/Views/html/signIn.html'; 
    }

    /**
     * Procesa el formulario de registro
     */
    public function handleRegistro() {
        try {
            // 1. Recoge todos los datos del formulario
            $Nombre             = $_POST['Nombres'] ?? '';
            $Apellido           = $_POST['Apellidos'] ?? '';
            $FechaNacimiento    = $_POST['FechaN'] ?? '';
            $Cedula             = $_POST['CI'] ?? '';
            $Telefono           = $_POST['Tel'] ?? '';
            $Correo             = $_POST['Email'] ?? '';
            $Direccion          = $_POST['Direccion'] ?? '';
            $Contrasenia        = $_POST['Contrasenia'] ?? '';

            // 2. Conecta a la BD
            $pdo = Database::connect();

            // 3. Revisa si el correo o la cédula ya existen
            $sqlCheck = "SELECT * FROM Usuarios WHERE Correo = ? OR Cedula = ?";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute([$Correo, $Cedula]);
            if ($stmtCheck->fetch()) {
                echo "<h1 class='bad'>El correo electrónico o la cédula ya están registrados.</h1>";
                echo "<a href='/login'>Intenta iniciar sesión</a>";
                return;
            }

            // 4. Hashea la contraseña
            $ContraseniaHash = password_hash($Contrasenia, PASSWORD_DEFAULT);

            // 5. Inserta el nuevo usuario
            $sqlInsert = "INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Telefono, Correo, Contrasenia, Direccion)
                        VALUES ('$Cedula', '$Nombre', '$Apellido', '$FechaNacimiento', '$Telefono', '$Correo', '$Contrasenia', '$Direccion')";
            
            $stmtInsert = $pdo->prepare($sqlInsert);
            $resultado = $stmtInsert->execute([
                $Cedula,
                $Nombre,
                $Apellido,
                $FechaNacimiento,
                $Telefono,
                $Correo,
                $ContraseniaHash,
                $Direccion
            ]);

            // 6. Si se crea correctamente redirige
            if ($resultado) {
                // Redirigir al inicio de sesión
                header('Location: http://127.0.0.1/login');
                exit();
            } else {
                echo "<h1 class'bad'>Error: No se pudo crear tu cuenta.</h1>";
                echo "<a href='/registro'>Volver a intentar</a>";
            }

        } catch (Exception $e) {
            echo "<h1 class='bad'>Error en la base de datos: " . $e->getMessage() . "</h1>";
            echo "<a href='/registro'>Volver a intentar</a>";
        }
    }
}
?>