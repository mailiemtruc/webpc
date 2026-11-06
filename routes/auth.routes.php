<?php
/**
 * [DEV 1 PHỤ TRÁCH] - Module Xác thực & Phân quyền (Auth)
 */
use App\Core\Router;

// Giao diện Đăng nhập & Đăng ký (Kèm kiểm tra bảo mật CSRF Token)
Router::get('/dang-nhap', 'App\Modules\Auth\AuthController@showLogin');
Router::post('/dang-nhap', 'App\Modules\Auth\AuthController@login', ['App\Core\Middleware::csrf']);

Router::get('/dang-ky', 'App\Modules\Auth\AuthController@showRegister');
Router::post('/dang-ky', 'App\Modules\Auth\AuthController@register', ['App\Core\Middleware::csrf']);

Router::get('/dang-xuat', 'App\Modules\Auth\AuthController@logout');

// Phân hệ Quên & Đặt lại mật khẩu (Forgot Password)
Router::get('/quen-mat-khau', 'App\Modules\Auth\AuthController@showForgotPassword');
Router::post('/quen-mat-khau', 'App\Modules\Auth\AuthController@sendResetLink', ['App\Core\Middleware::csrf']);

Router::get('/dat-lai-mat-khau', 'App\Modules\Auth\AuthController@showResetPassword');
Router::post('/dat-lai-mat-khau', 'App\Modules\Auth\AuthController@resetPassword', ['App\Core\Middleware::csrf']);


