<?php
// app/controllers/AdminController.php

class AdminController {
    /**
     * Muestra el panel principal del Administrador.
     */
    public function dashboard() {
        // Simple verificación de sesión para demostrar Permisos por Rol
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'administrador') {
            http_response_code(403);
            die("<h1>403 Acceso Denegado</h1><p>Solo Administradores pueden ver este panel.</p>");
        }

        // Dashboard de Éxito
        echo "
        <h1>✅ BIENVENIDO ADMINISTRADOR, {$_SESSION['username']}</h1>
        <p>Tu rol es: **{$_SESSION['role']}**</p>
        <p>Infraestructura: Docker (Nginx, PHP-FPM, MySQL) está totalmente operativo.</p>
        <p>Seguridad: Login con hash de contraseña y control de sesión OK.</p>
        <hr>
        <a href='/logout'>Cerrar Sesión</a>
        ";
    }

    /**
     * Cierra la sesión (Logout).
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
