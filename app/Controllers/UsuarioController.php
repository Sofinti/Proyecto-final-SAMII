<?php
// app/Controllers/UsuarioController.php

require_once __DIR__ . '/../Models/Usuario.php';

class UsuarioController {
    
    public function mostrarLogin() {
        include __DIR__ . '/../Views/login.php';
    }

    public function mostrarRegistro() {
        include __DIR__ . '/../Views/registro.php';
    }

    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $usuario = new Usuario();
            $resultado = $usuario->buscarPorEmail($email);

            if($resultado->rowCount() > 0) {
                $fila = $resultado->fetch(PDO::FETCH_ASSOC);
                
                if(password_verify($password, $fila['password'])) {
                    session_start();
                    $_SESSION['usuario_id'] = $fila['id'];
                    $_SESSION['usuario_nombre'] = $fila['nombre'];
                    
                    header('Location: /inicio');
                    exit();
                } else {
                    $error = "Contraseña incorrecta";
                    include __DIR__ . '/../Views/login.php';
                }
            } else {
                $error = "Usuario no encontrado";
                include __DIR__ . '/../Views/login.php';
            }
        }
    }

    public function registrar() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = new Usuario();
            $usuario->nombre = $_POST['nombre'];
            $usuario->email = $_POST['email'];
            $usuario->password = $_POST['password'];

            if($usuario->crear()) {
                header('Location: /login?registro=exitoso');
                exit();
            } else {
                $error = "Error al crear usuario";
                include __DIR__ . '/../Views/registro.php';
            }
        }
    }

    public function listar() {
        $usuario = new Usuario();
        $resultado = $usuario->obtenerTodos();
        $usuarios = $resultado->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../Views/usuarios_lista.php';
    }
}
?>
