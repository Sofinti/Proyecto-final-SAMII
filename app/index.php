<?php
// app/index.php - Punto de entrada de la aplicación

// 1. Cargar clases core
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Database.php';

// 2. Cargar modelos
require_once __DIR__ . '/models/User.php';

// 3. Cargar controladores
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminController.php';

// 4. Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Crear router y definir rutas
$router = new Router();

// Rutas de autenticación
$router->get('/', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@handleLogin');
$router->get('/logout', 'AuthController@logout');

// Rutas de administrador
$router->get('/admin/dashboard', 'AdminController@dashboard');

// 6. Resolver la ruta actual
$router->resolve();
