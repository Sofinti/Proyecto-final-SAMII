<?php
// index.php (Refactorizado con estilo switch - Front Controller único)

// -----------------------------------------------------------------
// PASO 1: Definir la Ruta Base (¡Importante para Docker!)
// -----------------------------------------------------------------
define('BASE_PATH', '');

// -----------------------------------------------------------------
// PASO 2: Función de Ayuda
// -----------------------------------------------------------------
function base_url($path = '') {
    return BASE_PATH . $path;
}

// -----------------------------------------------------------------
// PASO 3: Iniciar Sesión
// -----------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------
// PASO 4: Cargar todas las clases (Motor, Controladores, Modelos)
// -----------------------------------------------------------------
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/UsuarioController.php';
require_once __DIR__ . '/Models/User.php';

// -----------------------------------------------------------------
// PASO 5: Obtener la URI y el Método
// -----------------------------------------------------------------
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

// -----------------------------------------------------------------
// PASO 6: El "Mapa de Rutas" estilo switch (Front Controller)
// -----------------------------------------------------------------
switch ($uri) {
    // --- Rutas de Autenticación ---
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

    case '/agregar':
        if ($method === 'POST') {
            require __DIR__ . '/Models/agregar.php';
        }
        break;

    // --- Ruta del Panel de Admin ---
    case '/admin/dashboard':
        if ($method === 'GET') {
            $controller = new AdminController();
            $controller->dashboard();
        }
        break;

    // --- Rutas de Secciones ---
    case '/calendario':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarCalendario();
        }
        break;
        
    case '/socios':
        if ($method === 'GET') {
            echo "<!-- DEBUG: Entrando al caso /socios -->\n";
            echo "<!-- DEBUG: __DIR__ = " . __DIR__ . " -->\n";
            
            $controllerFile = __DIR__ . '/Controllers/UsuarioController.php';
            echo "<!-- DEBUG: UsuarioController existe: " . (file_exists($controllerFile) ? 'SI' : 'NO') . " -->\n";
            
            $sociosFile = __DIR__ . '/Views/html/secciones/socios.html';
            echo "<!-- DEBUG: socios.html existe: " . (file_exists($sociosFile) ? 'SI' : 'NO') . " -->\n";
            echo "<!-- DEBUG: Ruta completa: $sociosFile -->\n";
            
            try {
                $controller = new UsuarioController();
                echo "<!-- DEBUG: UsuarioController creado exitosamente -->\n";
                $controller->mostrarSocios();
                echo "<!-- DEBUG: mostrarSocios() ejecutado -->\n";
            } catch (Exception $e) {
                echo "<!-- ERROR: " . $e->getMessage() . " -->\n";
                echo "<h1>Error: " . $e->getMessage() . "</h1>";
            }
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
        
    // --- Rutas de Novedades ---
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

    // --- Ruta 404 ---
    default:
        http_response_code(404);
        echo "<h1>404 No Encontrada</h1><p>Ruta no definida en el sistema.</p>";
        echo "<!-- DEBUG URI solicitada: $uri -->";
        break;
}
?>