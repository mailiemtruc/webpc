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

// Cấu hình hiển thị lỗi an toàn theo biến môi trường APP_DEBUG
$isDebug = filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN);
if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Khởi tạo Session an toàn
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

// Khởi chạy hệ thống Router
\App\Core\Router::dispatch();

