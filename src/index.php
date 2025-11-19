<?php
// index.php - Front Controller

// -----------------------------------------------------------------
// 1. Configuración Inicial
// -----------------------------------------------------------------
define('BASE_PATH', '');

function base_url($path = '') {
    return BASE_PATH . $path;
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------
// 2. Cargar dependencias (Modelos y Controladores)
// -----------------------------------------------------------------
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/UsuarioController.php';
require_once __DIR__ . '/Models/User.php';

// -----------------------------------------------------------------
// 3. Enrutamiento (Router)
// -----------------------------------------------------------------

// Obtener la URI limpia
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$basePath = defined('BASE_PATH') ? BASE_PATH : '';

$uri = $requestUri;
if ($basePath && strpos($requestUri, $basePath) === 0) {
    $uri = substr($requestUri, strlen($basePath));
}
if ($uri === '' || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

// Mapa de Rutas
switch ($uri) {
    
    // --- Home y Autenticación ---
    case '/':
        if ($method === 'GET') {
            require __DIR__ . '/Views/html/index.html';
        }
        break;

    case '/login':
        if ($method === 'GET') {
            $controller = new AuthController();
            $controller->showLoginForm();
        } elseif ($method === 'POST') {
            $controller = new AuthController();
            $controller->handleLogin();
        }
        break;

    case '/logout':
        if ($method === 'GET') {
            $controller = new AuthController();
            $controller->logout();
        }
        break;

    case '/registro':
        if ($method === 'GET') {
            $controller = new AuthController();
            $controller->showRegistroForm();
        } elseif ($method === 'POST') {
            $controller = new AuthController();
            $controller->handleRegistro();
        }
        break;

    // ===========================================
    // --- RUTAS DEL PANEL DE ADMINISTRADOR ---
    // ===========================================
    
    // Ver el panel (lista de pendientes)
    case '/admin/dashboard':
        if ($method === 'GET') {
            $controller = new AdminController();
            $controller->dashboard();
        }
        break;

    // Acción del botón "Habilitar"
    case '/admin/habilitar':
        if ($method === 'POST') {
            $controller = new AdminController();
            $controller->habilitarUsuario();
        }
        break;

    // ===========================================
    
    // --- Secciones Principales ---
    case '/calendario':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarCalendario();
        }
        break;
        
    case '/socios':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarSocios();
        }
        break;
        
    case '/contabilidad':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarContabilidad();
        }
        break;
        
    case '/legal':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarLegal();
        }
        break;
        
    case '/reclamos':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarReclamos();
        }
        break;

    // --- Módulo de Horas ---
    case '/mis-horas':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->showMisHorasForm();
        }
        break;

    case '/guardar-horas':
        if ($method === 'POST') {
            $controller = new UsuarioController();
            $controller->handleHorasSubmit();
        }
        break;
        
    // --- Novedades ---
    case '/novedades/1':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarNovedad1();
        }
        break;

    case '/novedades/2':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarNovedad2();
        }
        break;

    case '/novedades/3':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarNovedad3();
        }
        break;

    case '/novedades/4':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarNovedad4();
        }
        break;

    case '/novedades/5':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarNovedad5();
        }
        break;

    // --- Ruta 404 (No Encontrada) ---
    default:
        http_response_code(404);
        echo "<h1>404 No Encontrada</h1><p>La página que buscas no existe.</p>";
        break;
}
?>