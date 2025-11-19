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
                echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>Por favor completa todos los campos</h1> 
                            <a href='/login' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Volver a iniciar sesión</a>
                        </div>
                    </div>
                ";
                return;
            }

            $pdo = Database::connect();
            $sql = "SELECT * FROM Usuarios WHERE Correo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                if ($usuario['Activo'] == 0) {
                    echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                            <div style='background: #003f7f;
                            background-position: center;
                            background-repeat: no-repeat;
                            background-size: cover;
                            justify-self: center;
                            padding: 30px;
                            padding-bottom: 50px;
                            width: auto;
                            height: auto;
                            max-width: 550px;
                            border-radius: 25px;'>
                                <h1 class='bad' style='color: #FBFBFF;'>Cuenta pendiente de aprobación.</h1> 
                                <p style='color: #FBFBFF;'>Un administrador aún no ha habilitado tu cuenta.</p>
                                <a href='/login' style='display: flex;
                                justify-self: center;
                                background: #FBFBFF;
                                font-size: 16px;
                                padding: 8px 12px;
                                border: 2px solid #003f7f;
                                border-radius: 20px;
                                cursor: pointer;
                                box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                                transition: .3s;
                                text-decoration: none;
                                color: #003f7f;'>Volver</a>
                            </div>
                        </div>
                    ";
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
                    echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                            <div style='background: #003f7f;
                            background-position: center;
                            background-repeat: no-repeat;
                            background-size: cover;
                            justify-self: center;
                            padding: 30px;
                            padding-bottom: 50px;
                            width: auto;
                            height: auto;
                            max-width: 550px;
                            border-radius: 25px;'>
                                <h1 class='bad' style='color: #FBFBFF;'>Contraseña incorrecta.</h1> 
                                <a href='/login' style='display: flex;
                                justify-self: center;
                                background: #FBFBFF;
                                font-size: 16px;
                                padding: 8px 12px;
                                border: 2px solid #003f7f;
                                border-radius: 20px;
                                cursor: pointer;
                                box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                                transition: .3s;
                                text-decoration: none;
                                color: #003f7f;'>Volver a intentar</a>
                            </div>
                        </div>
                    ";
                }
            } else {
                echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>El usuario no existe.</h1> 
                            <a href='/registro' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Ir a registrarme</a>
                        </div>
                    </div>
                ";
            }
        } catch (Exception $e) {
            echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>Error: " . $e->getMessage() . "</h1> 
                        </div>
                    </div>
                ";
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
                echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>Por favor completa todos los campos obligatorios.</h1> 
                            <a href='/registro' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Volver</a>
                        </div>
                    </div>
                ";
                return;
            }

            $contrasenia_hash = password_hash($contrasenia, PASSWORD_DEFAULT);
            $pdo = Database::connect();

            // Verificar duplicados
            $sql_check = "SELECT Id_Usuario FROM Usuarios WHERE Correo = ? OR Cedula = ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$correo, $cedula]);
            
            if ($stmt_check->fetch()) {
                echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>Error: El correo o la cédula ya existen.</h1> 
                            <a href='/login' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Ir a Iniciar Sesión</a>
                        </div>
                    </div>
                ";
                return;
            }

            // Lógica Admin
            $rol = 2;      
            $activo = 0;   
            $habTrabajo = 0; // Por defecto no puede trabajar hasta que lo habiliten
            $mensaje = "";

            if ($tokenIngresado === $CLAVE_MAESTRA) {
                $rol = 1; $activo = 1; $habTrabajo = 1;
                $mensaje = "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='good' style='color: #FBFBFF;'>¡Registro recibido!</h1>
                            <h3 style='color: #FBFBFF;'>✅ ¡Admin validado correctamente!</h3> 
                            <a href='/login' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Ir a Iniciar Sesión</a>
                        </div>
                    </div>";
            } else {
                $mensaje = "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='good' style='color: #FBFBFF;'>¡Registro recibido!</h1>
                            <h3 style='color: #FBFBFF;'>Estado: Pendiente de aprobación</h3> 
                            <a href='/login' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Ir a Iniciar Sesión</a>
                        </div>
                    </div>";
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
            
            echo $mensaje;

        } catch (Exception $e) {
            echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>Error: " . $e->getMessage() . "</h1> 
                            <a href='/registro' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Volver</a>
                        </div>
                    </div>
                ";
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