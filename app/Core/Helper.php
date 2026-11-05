<?php
/**
 * Global Helper Functions for WebPC
 * Môn: Lập trình Web - UTH
 */

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        static $envData = null;
        if ($envData === null) {
            $envData = [];
            $envPath = dirname(__DIR__, 2) . '/.env';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                        continue;
                    }
                    [$k, $v] = explode('=', $line, 2);
                    $envData[trim($k)] = trim(trim($v), '"\'');
                }
            }
        }
        return $envData[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        $appUrl = rtrim(env('APP_URL', 'http://localhost/webpc/public'), '/');
        $path = ltrim($path, '/');
        return $path !== '' ? "{$appUrl}/{$path}" : $appUrl;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path = ''): string {
        if (empty($path)) {
            return asset('images/no-image.png');
        }
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        return base_url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('format_money')) {
    function format_money($amount): string {
        if ($amount === null || $amount === '') {
            return 'Liên hệ';
        }
        return number_format((float)$amount, 0, ',', '.') . ' ₫';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $str): string {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        return trim($str, '-');
    }
}

if (!function_exists('view')) {
    function view(string $viewPath, array $data = []): void {
        extract($data);
        $viewFile = dirname(__DIR__, 2) . '/views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) {
            die("View not found: {$viewPath} (File: {$viewFile})");
        }
        require $viewFile;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        $url = (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) 
            ? $path 
            : base_url($path);
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }
}

if (!function_exists('get_flash')) {
    function get_flash(string $type): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }
}

if (!function_exists('auth_user')) {
    function auth_user() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return auth_user() !== null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool {
        $user = auth_user();
        return $user && ($user['role'] === 'admin');
    }
}

if (!function_exists('is_staff')) {
    function is_staff(): bool {
        $user = auth_user();
        return $user && ($user['role'] === 'admin' || $user['role'] === 'staff');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
    }
}

