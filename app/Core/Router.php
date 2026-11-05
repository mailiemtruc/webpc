<?php
namespace App\Core;

class Router {
    private static array $routes = [];

    public static function get(string $uri, $action, array $middlewares = []): void {
        self::addRoute('GET', $uri, $action, $middlewares);
    }

    public static function post(string $uri, $action, array $middlewares = []): void {
        self::addRoute('POST', $uri, $action, $middlewares);
    }

    private static function addRoute(string $method, string $uri, $action, array $middlewares): void {
        $uri = '/' . trim($uri, '/');
        self::$routes[] = [
            'method'      => $method,
            'uri'         => $uri,
            'action'      => $action,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Tự động quét và nạp toàn bộ file route của các thành viên trong thư mục routes/
     * Giúp 5 Dev hoàn toàn không bị xung đột Git khi thêm route mới!
     */
    public static function loadRoutes(): void {
        $routesPath = dirname(__DIR__, 2) . '/routes';
        if (is_dir($routesPath)) {
            $files = glob($routesPath . '/*.routes.php');
            foreach ($files as $file) {
                require_once $file;
            }
        }
    }

    public static function dispatch(): void {
        self::loadRoutes();

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Chuẩn hóa URI loại bỏ thư mục con (ví dụ: /webpc/public)
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }
        $requestUri = '/' . trim($requestUri, '/');

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Chuyển đổi định dạng {param} sang regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches)) {
                // Lọc bỏ các key dạng số, chỉ giữ lại named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Chạy Middlewares (nếu có)
                foreach ($route['middlewares'] as $middleware) {
                    if (is_callable($middleware)) {
                        call_user_func($middleware);
                    } elseif (class_exists($middleware)) {
                        (new $middleware())->handle();
                    }
                }

                // Thực thi Controller Action
                if (is_callable($route['action'])) {
                    call_user_func_array($route['action'], $params);
                    return;
                }

                if (is_string($route['action']) && strpos($route['action'], '@') !== false) {
                    [$controllerClass, $method] = explode('@', $route['action']);
                    
                    if (!class_exists($controllerClass)) {
                        die("Controller không tồn tại: {$controllerClass}");
                    }

                    $controller = new $controllerClass();
                    if (!method_exists($controller, $method)) {
                        die("Phương thức {$method} không tồn tại trong controller {$controllerClass}");
                    }

                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        view('client.errors.404', ['title' => '404 - Không tìm thấy trang']);
    }
}

