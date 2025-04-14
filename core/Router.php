<?php
namespace Core;

class Router {
    public function dispatch($uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // 라우트 정의
        $routes = [
            '/' => ['controller' => 'PostController', 'action' => 'index'],
            '/login' => ['controller' => 'UserController', 'action' => 'loginForm'],
            '/register' => ['controller' => 'UserController', 'action' => 'registerForm'],
            '/logout' => ['controller' => 'UserController', 'action' => 'logout'],
            '/write' => ['controller' => 'PostController', 'action' => 'writeForm'],
            '/view' => ['controller' => 'PostController', 'action' => 'view'],
        ];
        
        // POST 요청 처리
        if ($method === 'POST') {
            $postRoutes = [
                '/login' => ['controller' => 'UserController', 'action' => 'login'],
                '/register' => ['controller' => 'UserController', 'action' => 'register'],
                '/write' => ['controller' => 'PostController', 'action' => 'write'],
                '/comment' => ['controller' => 'CommentController', 'action' => 'create'],
            ];
            
            if (isset($postRoutes[$uri])) {
                $route = $postRoutes[$uri];
                $this->executeController($route['controller'], $route['action']);
                return;
            }
        }
        
        // GET 요청 처리
        if (isset($routes[$uri])) {
            $route = $routes[$uri];
            $this->executeController($route['controller'], $route['action']);
            return;
        }
        
        // 동적 라우트 처리 (예: /view/123)
        if (preg_match('/^\/view\/(\d+)$/', $uri, $matches)) {
            $_GET['id'] = $matches[1];
            $this->executeController('PostController', 'view');
            return;
        }
        
        // 404 페이지 처리
        header("HTTP/1.0 404 Not Found");
        echo "404 - 페이지를 찾을 수 없습니다.";
    }
    
    private function executeController($controller, $action) {
        $controllerClass = "\\App\\Controllers\\{$controller}";
        $controllerFile = __DIR__ . "/../app/Controllers/{$controller}.php";
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $instance = new $controllerClass();
            $instance->$action();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "404 - 컨트롤러를 찾을 수 없습니다.";
        }
    }
}
