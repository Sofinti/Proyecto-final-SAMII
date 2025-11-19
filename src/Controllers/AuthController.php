<?php
// /src/Controllers/AuthController.php

class AuthController {
    
    // 1. LOGIN (Igual que antes)
    public function showLoginForm() {
        require __DIR__ . '/../Views/html/logIn.html'; 
    }

    public function handleLogin() {
        try {
            $correo = $_POST['Email'] ?? '';
            $contrasenia = $_POST['Contrasenia'] ?? '';
            
            if (empty($correo) || empty($contrasenia)) {
                echo "<h1 class='bad'>Por favor completa todos los campos</h1>";
                echo "<a href='/login'>Volver al login</a>";
                return;
            }

            $pdo = Database::connect();
            $sql = "SELECT * FROM Usuarios WHERE Correo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                if ($usuario['Activo'] == 0) {
                    echo "<h1 class='bad'>Cuenta pendiente de aprobación</h1>";
                    echo "<p>Un administrador aún no ha habilitado tu cuenta.</p>";
                    echo "<a href='/login'>Volver</a>";
                    return; 
                }
                if (password_verify($contrasenia, $usuario['Contrasenia'])) {
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['user_id'] = $usuario['Id_Usuario'];
                    $_SESSION['Email'] = $usuario['Correo'];
                    $_SESSION['Nombre'] = $usuario['Nombre'];
                    $_SESSION['Rol'] = $usuario['Id_TipoUsuario']; 
                    header('Location: /');
                    exit();
                } else {
                    echo "<h1 class='bad'>Contraseña incorrecta</h1>";
                    echo "<a href='/login'>Volver a intentar</a>";
                }
            } else {
                echo "<h1 class='bad'>El usuario no existe. Regístrate.</h1>";
                echo "<a href='/registro'>Ir a Registrarme</a>";
            }
        } catch (Exception $e) {
            echo "<h1 class='bad'>Error: " . $e->getMessage() . "</h1>";
        }
    }

    // 2. REGISTRO
    public function showRegistroForm() {
        require __DIR__ . '/../Views/html/signIn.html'; 
    }

    public function handleRegistro() {
        try {
            // --- 1. Recoge los datos
            $cedula = $_POST['CI'] ?? '';
            $nombre = $_POST['Nombres'] ?? '';
            $apellido = $_POST['Apellidos'] ?? '';
            $fechaNacimiento = $_POST['FechaN'] ?? '';
            $telefono = $_POST['Tel'] ?? '';
            $correo = $_POST['Email'] ?? '';
            $contrasenia = $_POST['Contrasenia'] ?? '';
            $direccion = $_POST['Direccion'] ?? '';
            
            // Token de admin
            $tokenIngresado = $_POST['admin_token'] ?? '';
            $CLAVE_MAESTRA = "SAMANTHA_GOD_MODE";

            // --- 2. Validación ---
            if (empty($cedula) || empty($nombre) || empty($apellido) || empty($correo) || empty($contrasenia)) {
                echo "<h1 class='bad'>Por favor completa todos los campos obligatorios.</h1>";
                echo "<a href='/registro'>Volver</a>";
                return;
            }

            $contrasenia_hash = password_hash($contrasenia, PASSWORD_DEFAULT);
            $pdo = Database::connect();

            // Verificar duplicados
            $sql_check = "SELECT Id_Usuario FROM Usuarios WHERE Correo = ? OR Cedula = ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$correo, $cedula]);
            
            if ($stmt_check->fetch()) {
                echo "<h1 class='bad'>Error: El correo o la cédula ya existen.</h1>";
                echo "<a href='/login'>Ir a Iniciar Sesión</a>";
                return;
            }

            // Lógica Admin
            $rol = 2;      
            $activo = 0;   
            $habTrabajo = 0; // Por defecto no puede trabajar hasta que lo habiliten
            $mensaje = "";

            if ($tokenIngresado === $CLAVE_MAESTRA) {
                $rol = 1; $activo = 1; $habTrabajo = 1;
                $mensaje = "<h3 style='color:green'>✅ ¡Admin Validado!</h3>";
            } else {
                $mensaje = "<h3>Estado: Pendiente de Aprobación</h3>";
            }

            // --- 3. Insertar en BD ---
            $sql_insert = "INSERT INTO Usuarios (Cedula, Nombre, Apellido, FechaNacimiento, Telefono, Correo, Contrasenia, Direccion, Id_TipoUsuario, Activo, HabilitadoTrabajo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert = $pdo->prepare($sql_insert);
            
            $stmt_insert->execute([
                $cedula, $nombre, $apellido, $fechaNacimiento, $telefono,
                $correo, $contrasenia_hash, $direccion,
                $rol, $activo, $habTrabajo
            ]); 
            
            echo "<h1 class='good'>¡Registro recibido!</h1>";
            echo $mensaje;
            echo "<a href='/login'>Ir a Iniciar Sesión</a>";

        } catch (Exception $e) {
            echo "<h1 class='bad'>Error: " . $e->getMessage() . "</h1>";
            echo "<a href='/registro'>Volver</a>";
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit();
    }
}
?>