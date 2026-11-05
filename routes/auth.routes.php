<?php
/**
 * [DEV 1 PHỤ TRÁCH] - Module Xác thực & Phân quyền (Auth)
 */
use App\Core\Router;

// Giao diện Đăng nhập & Đăng ký
Router::get('/dang-nhap', 'App\Modules\Auth\AuthController@showLogin');
Router::post('/dang-nhap', 'App\Modules\Auth\AuthController@login');

Router::get('/dang-ky', 'App\Modules\Auth\AuthController@showRegister');
Router::post('/dang-ky', 'App\Modules\Auth\AuthController@register');

Router::get('/dang-xuat', 'App\Modules\Auth\AuthController@logout');

