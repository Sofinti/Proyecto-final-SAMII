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
     * Procesa el login con registro automático si el usuario no existe
     */
    public function handleLogin() {
        try {
            // 1. Recoger datos
            $correo = $_POST['Email'] ?? '';
            $contrasenia = $_POST['Contrasenia'] ?? '';
            
            if (empty($correo) || empty($contrasenia)) {
                echo "<h1 class='bad'>Por favor completa todos los campos</h1>";
                echo "<a href='/login'>Volver al login</a>";
                return;
            }

            // 2. Conectar a la base de datos
            $pdo = Database::connect();
            
            // 3. Buscar si el usuario existe
            $sql = "SELECT * FROM Usuarios WHERE Correo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
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
            } else {
                // Usuario NO existe - crear uno nuevo automáticamente
                $contraseniaHash = password_hash($contrasenia, PASSWORD_DEFAULT);
                
                // Generar datos por defecto
                $cedula = substr(time(), -10);
                $nombre = explode('@', $correo)[0];
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
                    // Usuario creado - iniciar sesión
                    session_start();
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['Email'] = $correo;
                    $_SESSION['Nombre'] = $nombre;
                    
                    // Redirigir usando ruta absoluta
                    header('Location: http://127.0.0.1/');
                    exit();
                } else {
                    echo "<h1 class='bad'>Error al crear el usuario</h1>";
                    echo "<a href='/login'>Volver al login</a>";
                }
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
        session_start();
        session_unset();
        session_destroy();
        header('Location: http://127.0.0.1/login');
        exit();
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegistroForm() {
        require __DIR__ . '/../Views/registro.php'; 
    }

    /**
     * Procesa el formulario de registro
     */
    public function handleRegistro() {
        echo "Función de registro por implementar";
    }
}
?>