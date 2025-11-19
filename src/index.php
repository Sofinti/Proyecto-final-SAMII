<?php
// index.php - Front Controller

// -----------------------------------------------------------------
// 1. Configuración Inicial
// -----------------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', '');

function base_url($path = '') {
    return BASE_PATH . $path;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------
// 2. Cargar dependencias
// -----------------------------------------------------------------
require_once __DIR__ . '/config/Database.php'; 
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/UsuarioController.php';

// -----------------------------------------------------------------
// 3. Enrutamiento (Router)
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

// Mapa de Rutas
switch ($uri) {

    // ===========================================
    // --- 🛠️ RUTA DE INSTALACIÓN (GENERAL) ---
    // ===========================================
    case '/setup':
        echo "<h1>🏗️ Construyendo Tablas Generales...</h1>";
        $host = 'db'; $db = 'samantha'; $user = 'root'; $pass = 'root'; 
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "
            DROP TABLE IF EXISTS Novedades; DROP TABLE IF EXISTS HorasLaborales; DROP TABLE IF EXISTS PagoInicial;
            DROP TABLE IF EXISTS PagoMensual; DROP TABLE IF EXISTS TelefonoUsuario; DROP TABLE IF EXISTS NumerosTarjeta;
            DROP TABLE IF EXISTS UnidadHabitacional; DROP TABLE IF EXISTS UsuarioComision; DROP TABLE IF EXISTS Usuarios;

            CREATE TABLE Usuarios (
                Id_Usuario INT AUTO_INCREMENT PRIMARY KEY, Cedula VARCHAR(20) NOT NULL, Nombre VARCHAR(50) NOT NULL,
                Apellido VARCHAR(50) NOT NULL, FechaNacimiento DATE NOT NULL, Telefono VARCHAR(20) NULL,
                Correo VARCHAR(100) NOT NULL UNIQUE, Contrasenia VARCHAR(255) NOT NULL, Direccion VARCHAR(100) NOT NULL,
                Id_TipoUsuario INT DEFAULT 2, Activo TINYINT DEFAULT 1, HabilitadoTrabajo TINYINT DEFAULT 0,
                FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE Novedades (
                Id_Novedad INT AUTO_INCREMENT PRIMARY KEY, Titulo VARCHAR(150) NOT NULL, Contenido TEXT NOT NULL,
                Imagen_url VARCHAR(255) NULL, FechaPublicacion DATETIME DEFAULT CURRENT_TIMESTAMP, Id_Autor INT NULL
            );
            CREATE TABLE HorasLaborales (
                Id_Horas INT AUTO_INCREMENT PRIMARY KEY, Id_Usuario INT NOT NULL, FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
                SemanaCompletada VARCHAR(50) NULL, HorasTrabajadas DECIMAL(10,2) NOT NULL, HorasFaltantes DECIMAL(10,2) DEFAULT 0,
                Motivo TEXT, SolicitaExoneracion TINYINT(1) DEFAULT 0, MontoCompensatorio DECIMAL(10,2) DEFAULT 0,
                Estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente', FechaRevision DATETIME NULL,
                Id_AdminAprobador INT NULL, FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario) ON DELETE CASCADE
            );
            CREATE TABLE PagoInicial (
                id_PagoInicial INT AUTO_INCREMENT PRIMARY KEY, Id_Usuario INT, Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                Monto DECIMAL(10,2), Comprobante_url VARCHAR(255), Estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
                FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
            );
            CREATE TABLE PagoMensual (
                Id_PagoMensual INT AUTO_INCREMENT PRIMARY KEY, Id_Usuario INT, Mes TINYINT, Ano SMALLINT,
                Monto DECIMAL(10,2), Comprobante_url VARCHAR(255), Estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
                FOREIGN KEY (Id_Usuario) REFERENCES Usuarios(Id_Usuario)
            );
            ";
            $pdo->exec($sql);
            echo "✅ ¡TABLAS CREADAS!<br><a href='/registro'>👉 IR AL REGISTRO</a>";
        } catch (PDOException $e) { echo "❌ Error: " . $e->getMessage(); }
        exit();
        break;

    // ===========================================
    // --- 📅 SETUP CALENDARIO (SOLO TABLA EVENTOS) ---
    // ===========================================
    case '/setup-calendar':
        echo "<h1>📅 Instalando Calendario...</h1>";
        $host = 'db'; $db = 'samantha'; $user = 'root'; $pass = 'root';
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "
            CREATE TABLE IF NOT EXISTS Eventos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                start_event DATETIME NOT NULL,
                end_event DATETIME NULL,
                color VARCHAR(20) DEFAULT '#3788d8',
                created_by INT NULL
            );
            ";
            $pdo->exec($sql);
            echo "✅ ¡Tabla 'Eventos' creada!<br><a href='/calendario'>👉 IR AL CALENDARIO</a>";
        } catch (PDOException $e) { echo "❌ Error: " . $e->getMessage(); }
        exit();
        break;

    // ===========================================
    // --- HOME ---
    // ===========================================
    case '/':
        if ($method === 'GET') {
            $pdo = Database::connect();
            try {
                $stmt = $pdo->query("SELECT * FROM Novedades ORDER BY FechaPublicacion DESC");
                $novedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { $novedades = []; }
            require __DIR__ . '/Views/html/home.php';
        }
        break;

    // ===========================================
    // --- AUTH (LOGIN/REGISTRO/LOGOUT) ---
    // ===========================================
    case '/login':
        if ($method === 'GET') { $controller = new AuthController(); $controller->showLoginForm(); }
        elseif ($method === 'POST') { $controller = new AuthController(); $controller->handleLogin(); }
        break;

    case '/logout':
        if ($method === 'GET') { $controller = new AuthController(); $controller->logout(); }
        break;

    case '/registro':
        if ($method === 'GET') { $controller = new AuthController(); $controller->showRegistroForm(); }
        elseif ($method === 'POST') { $controller = new AuthController(); $controller->handleRegistro(); }
        break;

    // ===========================================
    // --- 👤 MI CUENTA (PERFIL) --- 
    // ===========================================
    case '/mi-cuenta':
        if ($method === 'GET') {
            $controller = new UsuarioController();
            $controller->mostrarMiCuenta();
        }
        break;

    case '/actualizar-perfil':
        if ($method === 'POST') {
            $controller = new UsuarioController();
            $controller->actualizarPerfil();
        }
        break;

    // ===========================================
    // --- ADMIN DASHBOARD & ACCIONES ---
    // ===========================================
    case '/admin/dashboard':
        if ($method === 'GET') { $controller = new AdminController(); $controller->dashboard(); }
        break;

    case '/admin/nueva-novedad':
        if ($method === 'GET') { $controller = new AdminController(); $controller->mostrarCrearNovedad(); }
        break;

    case '/admin/guardar-novedad':
        if ($method === 'POST') { $controller = new AdminController(); $controller->handleGuardarNovedad(); }
        break;

    case '/admin/habilitar':
        if ($method === 'POST') { $controller = new AdminController(); $controller->habilitarUsuario(); }
        break;

    case '/admin/aprobar-horas':
        if ($method === 'POST') { $controller = new AdminController(); $controller->aprobarHoras(); }
        break;

    case '/admin/gestionar-pago':
        if ($method === 'POST') { $controller = new AdminController(); $controller->gestionarPago(); }
        break;

    // ===========================================
    // --- SECCIÓN NOVEDADES ---
    // ===========================================
    case '/ver-novedad':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->verDetalleNovedad(); }
        break;

    // ===========================================
    // --- 📅 CALENDARIO INTERACTIVO ---
    // ===========================================
    case '/calendario':
        if ($method === 'GET') {
            require_once __DIR__ . '/Controllers/CalendarController.php';
            $controller = new CalendarController();
            $controller->index();
        }
        break;

    case '/api/eventos':
        if ($method === 'GET') {
            require_once __DIR__ . '/Controllers/CalendarController.php';
            $controller = new CalendarController();
            $controller->getEvents();
        }
        break;

    case '/api/eventos/guardar':
        if ($method === 'POST') {
            require_once __DIR__ . '/Controllers/CalendarController.php';
            $controller = new CalendarController();
            $controller->saveEvent();
        }
        break;

    case '/api/eventos/eliminar':
        if ($method === 'POST') {
            require_once __DIR__ . '/Controllers/CalendarController.php';
            $controller = new CalendarController();
            $controller->deleteEvent();
        }
        break;

    // ===========================================
    // --- SECCIONES FIJAS ---
    // ===========================================
    case '/socios':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->mostrarSocios(); }
        break;
        
    case '/contabilidad':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->mostrarContabilidad(); }
        break;
        
    case '/legal':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->mostrarLegal(); }
        break;
        
    case '/reclamos':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->mostrarReclamos(); }
        break;

    // ===========================================
    // --- HORAS Y PAGOS ---
    // ===========================================
    case '/mis-horas':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->showMisHorasForm(); }
        break;

    case '/guardar-horas':
        if ($method === 'POST') { $controller = new UsuarioController(); $controller->handleHorasSubmit(); }
        break;

    case '/mis-pagos':
        if ($method === 'GET') { $controller = new UsuarioController(); $controller->mostrarPagos(); }
        break;

    case '/pagar-inicial':
        if ($method === 'POST') { $controller = new UsuarioController(); $controller->handlePagoInicial(); }
        break;

    case '/pagar-mensual':
        if ($method === 'POST') { $controller = new UsuarioController(); $controller->handlePagoMensual(); }
        break;
        
    // ===========================================
    // --- ERROR 404 ---
    // ===========================================
    default:
        http_response_code(404);
        echo "<h1>404 No Encontrada</h1><p>La página que buscas no existe.</p>";
        break;
}
?>