<?php 

namespace App\Core;


class Router {

    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];
    private array $protectedRoutes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch() { 
        
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = '/prokey/public'; 
        $path = preg_replace('#^' . preg_quote($basePath) . '#', '', $path);

        $this->checkProtection($path);

        foreach ($this->routes[$method] as $route => $handler) {

            $routePattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([a-zA-Z0-9_-]+)', $route);
            $routePattern = '#^' . $routePattern . '$#';

            if (preg_match($routePattern, $path, $matches)) {
                array_shift($matches); 

                list($controllerName, $methodName) = explode('@', $handler);
                $controllerClass = 'App\\Controllers\\' . $controllerName;
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $methodName)) {
                        return call_user_func_array([$controller, $methodName], $matches);
                    }
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    public function getRoutes() {
        return $this->routes;
    }   

    public function protect($route) {
        $this->protectedRoutes[] = $route;
    }  
    
    private function checkProtection($route) {
        foreach ($this->protectedRoutes as $protectedRoute) {
            $pattern = str_replace('*', '.*', $protectedRoute);
            
            if (preg_match('#^' . $pattern . '$#', $route)) {
                Auth::check();
            }
        }
        return false;
    }   

}