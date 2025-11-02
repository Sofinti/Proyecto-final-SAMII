<?php
// app/core/Router.php - Motor de enrutamiento

class Router {
    private $routes = [];

    public function add($method, $uri, $controller) {
        $this->routes[] = [
            'method' => $method, 
            'uri' => $uri, 
            'controller' => $controller
        ];
    }

    public function get($uri, $controller) {
        $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        $this->add('POST', $uri, $controller);
    }

    public function resolve() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                
                if (is_string($route['controller'])) {
                    list($controllerName, $methodName) = explode('@', $route['controller']);
                    
                    if (!class_exists($controllerName)) {
                        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
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
        echo "<h1>404 Not Found</h1><p>Ruta no definida en el sistema Samantha.</p>";
    }
}
