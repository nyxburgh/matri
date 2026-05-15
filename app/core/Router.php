<?php
// app/core/Router.php

class Router {
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void {
        $this->addRoute('GET', $path, $controller, $method);
    }

    public function post(string $path, string $controller, string $method): void {
        $this->addRoute('POST', $path, $controller, $method);
    }

    private function addRoute(string $verb, string $path, string $controller, string $method): void {
        // Convert :param to named regex
        $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'verb'       => $verb,
            'pattern'    => '#^' . $pattern . '$#',
            'controller' => $controller,
            'method'     => $method,
        ];
    }

    public function dispatch(string $uri, string $requestMethod): void {
        // Strip query string
        $uri = strtok($uri, '?');
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['verb'] !== strtoupper($requestMethod)) continue;
            if (!preg_match($route['pattern'], $uri, $matches)) continue;

            // Extract named params
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            $controllerClass = $route['controller'];
            $method          = $route['method'];

            if (!class_exists($controllerClass)) {
                $this->abort(500, "Controller $controllerClass not found.");
                return;
            }

            $obj = new $controllerClass();
            if (!method_exists($obj, $method)) {
                $this->abort(500, "Method $method not found in $controllerClass.");
                return;
            }

            $obj->$method($params);
            return;
        }

        $this->abort(404, 'Page not found.');
    }

    private function abort(int $code, string $msg): void {
        http_response_code($code);
        echo "<h2>$code — $msg</h2>";
    }
}
