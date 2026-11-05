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
}

