<?php
namespace App\Core;

class Middleware {
    /**
     * Bắt buộc phải là Admin mới được truy cập
     */
    public static function admin(): void {
        if (!Auth::check()) {
            set_flash('info', 'Vui lòng đăng nhập với tài khoản Quản trị viên!');
            redirect('/dang-nhap');
        }
        if (!Auth::isAdmin()) {
            http_response_code(403);
            view('client.errors.403', ['title' => '403 - Quyền truy cập bị từ chối']);
            exit;
        }
    }

    /**
     * Bắt buộc phải là Nhân viên hoặc Admin
     */
    public static function staff(): void {
        if (!Auth::check()) {
            set_flash('info', 'Vui lòng đăng nhập để tiếp tục!');
            redirect('/dang-nhap');
        }
        if (!Auth::isStaff()) {
            http_response_code(403);
            view('client.errors.403', ['title' => '403 - Quyền truy cập bị từ chối']);
            exit;
        }
    }

    /**
     * Bắt buộc phải đăng nhập (Áp dụng cho xem Profile, Lịch sử mua hàng)
     */
    public static function auth(): void {
        if (!Auth::check()) {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
                $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
                $scriptBase = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
                if ($scriptBase !== '/' && strpos($uri, $scriptBase) === 0) {
                    $uri = substr($uri, strlen($scriptBase));
                }
                $uri = '/' . ltrim($uri, '/');
                if ($uri !== '/' && strpos($uri, '//') !== 0) {
                    $_SESSION['_intended_url'] = $uri;
                }
            }
            set_flash('info', 'Vui lòng đăng nhập để tiếp tục!');
            redirect('/dang-nhap');
        }
    }

    /**
     * Dành cho trang Đăng nhập / Đăng ký (Đã đăng nhập thì quay về trang chủ)
     */
    public static function guest(): void {
        if (Auth::check()) {
            redirect('/');
        }
    }

    /**
     * Xác thực CSRF Token cho các Form POST / AJAX
     * Chống tấn công giả mạo yêu cầu từ trang khác (Cross-Site Request Forgery)
     */
    public static function csrf(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            $sessionToken = $_SESSION['_csrf_token'] ?? '';
            
            if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
                http_response_code(403);
                $wantsJson = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                    || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                    || strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
                if ($wantsJson) {
                    json_response([
                        'success' => false,
                        'message' => 'Lỗi bảo mật CSRF: Phiên làm việc đã hết hạn hoặc yêu cầu không hợp lệ. Vui lòng tải lại trang!'
                    ], 403);
                }
                set_flash('error', 'Phiên làm việc đã hết hạn hoặc yêu cầu không hợp lệ. Vui lòng thử lại!');
                redirect($_SERVER['HTTP_REFERER'] ?? '/dang-nhap');
            }
        }
    }
}

