<?php
namespace App\Core;

class Middleware {
    /**
     * Bắt buộc phải là Admin mới được truy cập
     */
    public static function admin(): void {
        if (!Auth::check() || !Auth::isAdmin()) {
            set_flash('error', 'Bạn không có quyền truy cập vào trang Quản trị!');
            redirect('/dang-nhap');
        }
    }

    /**
     * Bắt buộc phải là Nhân viên hoặc Admin
     */
    public static function staff(): void {
        if (!Auth::check() || !Auth::isStaff()) {
            set_flash('error', 'Bạn không có quyền thực hiện thao tác này!');
            redirect('/dang-nhap');
        }
    }

    /**
     * Bắt buộc phải đăng nhập (Áp dụng cho xem Profile, Lịch sử mua hàng)
     */
    public static function auth(): void {
        if (!Auth::check()) {
            set_flash('warning', 'Vui lòng đăng nhập để tiếp tục!');
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
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
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

