<?php
// /src/core/Router.php - Motor de enrutamiento

class Router {
    private $routes = [];

    public function add($method, $uri, $controller) {
        $this->routes[] = [ 'method' => $method, 'uri' => $uri, 'controller' => $controller ];
    }
    public function get($uri, $controller) { $this->add('GET', $uri, $controller); }
    public function post($uri, $controller) { $this->add('POST', $uri, $controller); }

    public function resolve() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // -----------------------------------------------------------------
        // PASO 1: Obtener la URI limpia (El arreglo de XAMPP)
        // -----------------------------------------------------------------
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        
        $uri = $requestUri;
        // Si la URI de la petición empieza con el BASE_PATH, quítalo.
        if ($basePath && strpos($requestUri, $basePath) === 0) {
            $uri = substr($requestUri, strlen($basePath));
        }

        // Asegurarse de que la URI empiece con / (o sea / si está vacía)
        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        // -----------------------------------------------------------------

        foreach ($this->routes as $route) {
            // Comparamos la URI limpia
            if ($route['uri'] === $uri && $route['method'] === $method) {
                
                if (is_string($route['controller'])) {
                    list($controllerName, $methodName) = explode('@', $route['controller']);
                    
                    if (!class_exists($controllerName)) {
                        $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php'; 
                        if (file_exists($controllerFile)) {
                            require_once $controllerFile;
                        } else {
                            http_response_code(500);
                            echo "<h1>Error 500</h1><p>Controlador no encontrado: {$controllerName}</p>";
                            return;
                        }
                    }
                    $controller = new $controllerName();
                    $controller->$methodName();
                } elseif (is_array($route['controller'])) {
                    list($controller, $methodName) = $route['controller'];
                    $controller->$methodName();
                }
                return;
            }
        }
        http_response_code(404);
        echo "<h1>404 No Encontrada</h1><p>Ruta no definida en el sistema.</p>";
    }
}