<?php
/**
 * ========================================================
 * WEBPC - HỆ THỐNG WEBSITE BÁN PC & LINH KIỆN MÁY TÍNH
 * Single Entry Point
 * Môn: Lập trình Web - UTH
 * ========================================================
 */

// Đường dẫn gốc ứng dụng
define('ROOT_DIR', dirname(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('VIEWS_DIR', ROOT_DIR . '/views');
define('PUBLIC_DIR', __DIR__);

// Nạp các hàm Helper toàn cục
require_once APP_DIR . '/Core/Helper.php';

// Cấu hình hiển thị lỗi an toàn theo biến môi trường APP_DEBUG (mặc định tắt ở production)
$isDebug = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);
if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Thiết lập múi giờ
date_default_timezone_set(env('TIMEZONE', 'Asia/Ho_Chi_Minh'));

// Cơ chế Autoloading chuẩn PSR-4 (Hỗ trợ cả khi có hoặc không có Composer)
if (file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    require_once ROOT_DIR . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = APP_DIR . '/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// Khởi tạo Session với thiết lập cookie bảo mật
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Bắt ngoại lệ toàn cục khi APP_DEBUG=false để log lỗi nội bộ và hiện trang 500 chuẩn
set_exception_handler(function (\Throwable $e) use ($isDebug) {
    $logDir = ROOT_DIR . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logLine = sprintf(
        "[%s] %s in %s:%d\n%s\n\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    @file_put_contents($logDir . '/app.log', $logLine, FILE_APPEND | LOCK_EX);

    $wantsJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        || strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;

    if ($isDebug) {
        http_response_code(500);
        if ($wantsJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        } else {
            echo '<pre style="padding:16px;font-family:monospace;">' . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
        exit;
    }

    http_response_code(500);
    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Hệ thống đang bận, vui lòng thử lại sau.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        view('client.errors.500', ['title' => '500 - Sự cố hệ thống']);
    } catch (\Throwable $viewErr) {
        echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>500</title></head><body><h1>500 - Đã có lỗi xảy ra trên hệ thống</h1></body></html>';
    }
    exit;
});

// Khởi chạy hệ thống Router
\App\Core\Router::dispatch();
