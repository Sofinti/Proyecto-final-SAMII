<?php
// src/Controller/UsuarioController.php

require_once __DIR__ . '/../Model/Usuario.php';

class UsuarioController {
    private $usuarioModel;
    
    public function __construct($db) {
        $this->usuarioModel = new Usuario($db);
    }
    
    /**
     * Manejar el login
     */
    public function handleLogin() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validar campos vacíos
            if(empty($email) || empty($password)) {
                header('Location: /src/Vista/html/logIn.html?error=campos_vacios');
                exit;
            }
            
            // Intentar login
            $usuario = $this->usuarioModel->login($email, $password);
            
            if($usuario) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                header('Location: /src/Vista/html/index.html');
                exit;
            } else {
                header('Location: /src/Vista/html/logIn.html?error=credenciales_invalidas');
                exit;
            }
        }
    }
    
    /**
     * Manejar el registro
     */
    public function handleRegistro() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            // Validar campos vacíos
            if(empty($nombre) || empty($email) || empty($password)) {
                header('Location: /src/Vista/html/singIn.html?error=campos_vacios');
                exit;
            }
            
            // Validar que las contraseñas coincidan
            if($password !== $password_confirm) {
                header('Location: /src/Vista/html/singIn.html?error=password_no_coincide');
                exit;
            }
            
            // Validar longitud de contraseña
            if(strlen($password) < 6) {
                header('Location: /src/Vista/html/singIn.html?error=password_corta');
                exit;
            }
            
            // Intentar registrar
            if($this->usuarioModel->registrar($nombre, $email, $password)) {
                header('Location: /src/Vista/html/logIn.html?registro=exitoso');
                exit;
            } else {
                header('Location: /src/Vista/html/singIn.html?error=email_existe');
                exit;
            }
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function handleLogout() {
        session_start();
        session_destroy();
        header('Location: /src/Vista/html/logIn.html');
        exit;
    }
}
?>
