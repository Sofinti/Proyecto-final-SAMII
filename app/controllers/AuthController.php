<?php
// app/controllers/AuthController.php

class AuthController {
    
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm() {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login - Samantha</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
                h1 { color: #333; margin-bottom: 1.5rem; }
                input { width: 100%; padding: 0.75rem; margin: 0.5rem 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
                button { width: 100%; padding: 0.75rem; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
                button:hover { background: #0056b3; }
                .error { background: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h1>🔐 Login - Samantha</h1>
                <form method="POST" action="/login">
                    <input type="text" name="username" placeholder="Usuario" required autofocus>
                    <input type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit">Iniciar Sesión</button>
                </form>
                <p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">
                    Usuario de prueba: <strong>admin</strong> / Contraseña: <strong>123456</strong>
                </p>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Procesa el login
     */
    public function handleLogin() {
        // 1. Recoger datos
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // 2. Buscar usuario
        $user = new User();
        $userData = $user->findByUsername($username);
        
        if ($userData) {
            // 3. Verificar contraseña
            if (password_verify($password, $userData['password_hash'])) {
                // 4. Login exitoso
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['is_logged_in'] = true;
                $_SESSION['username'] = $userData['username'];
                $_SESSION['role'] = $userData['role'];
                
                header('Location: /admin/dashboard');
                exit();
            }
        }
        
        // Login Fallido
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Login</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .error-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
                h1 { color: #dc3545; }
                a { display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
                a:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>❌ Error de Autenticación</h1>
                <p>Usuario o contraseña incorrectos.</p>
                <a href="/">Volver a intentar</a>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Cierra la sesión
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /');
        exit();
    }
}
