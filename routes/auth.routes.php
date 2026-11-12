<?php
/**
 * [DEV 1 PHỤ TRÁCH] - Module Xác thực, Xác nhận Email, 2FA & Bảo mật tài khoản
 */
use App\Core\Router;

// Đăng nhập, Đăng ký, Đăng xuất (bắt buộc POST + CSRF)
Router::get('/dang-nhap', 'App\Modules\Auth\AuthController@showLogin');
Router::post('/dang-nhap', 'App\Modules\Auth\AuthController@login', ['App\Core\Middleware::csrf']);

Router::get('/dang-ky', 'App\Modules\Auth\AuthController@showRegister');
Router::post('/dang-ky', 'App\Modules\Auth\AuthController@register', ['App\Core\Middleware::csrf']);

Router::post('/dang-xuat', 'App\Modules\Auth\AuthController@logout', ['App\Core\Middleware::csrf']);

// Xác nhận tài khoản qua Email & Gửi lại liên kết xác nhận
Router::get('/xac-nhan-email', 'App\Modules\Auth\AuthController@verifyEmail');
Router::get('/gui-lai-xac-nhan', 'App\Modules\Auth\AuthController@showResendVerification');
Router::post('/gui-lai-xac-nhan', 'App\Modules\Auth\AuthController@resendVerification', ['App\Core\Middleware::csrf']);

// Xác thực 2 bước (2FA TOTP Authenticator / Mã dự phòng kiểu Discord)
Router::get('/xac-thuc-2fa', 'App\Modules\Auth\AuthController@showTwoFactorChallenge');
Router::post('/xac-thuc-2fa', 'App\Modules\Auth\AuthController@verifyTwoFactorChallenge', ['App\Core\Middleware::csrf']);
Router::post('/xac-thuc-2fa/huy', 'App\Modules\Auth\AuthController@cancelTwoFactorChallenge', ['App\Core\Middleware::csrf']);

// Quên & Đặt lại mật khẩu
Router::get('/quen-mat-khau', 'App\Modules\Auth\AuthController@showForgotPassword');
Router::post('/quen-mat-khau', 'App\Modules\Auth\AuthController@sendResetLink', ['App\Core\Middleware::csrf']);

Router::get('/dat-lai-mat-khau', 'App\Modules\Auth\AuthController@showResetPassword');
Router::post('/dat-lai-mat-khau', 'App\Modules\Auth\AuthController@resetPassword', ['App\Core\Middleware::csrf']);

// Trung tâm Bảo mật tài khoản (Đổi mật khẩu, Bật/Tắt 2FA, Mã dự phòng)
Router::get('/tai-khoan/bao-mat', 'App\Modules\Auth\AuthController@showSecurity');
Router::post('/tai-khoan/bao-mat/doi-mat-khau', 'App\Modules\Auth\AuthController@changePassword', ['App\Core\Middleware::csrf']);
Router::post('/tai-khoan/bao-mat/2fa/bat', 'App\Modules\Auth\AuthController@enableTwoFactor', ['App\Core\Middleware::csrf']);
Router::post('/tai-khoan/bao-mat/2fa/ma-du-phong', 'App\Modules\Auth\AuthController@regenerateBackupCodes', ['App\Core\Middleware::csrf']);
Router::post('/tai-khoan/bao-mat/2fa/tat', 'App\Modules\Auth\AuthController@disableTwoFactor', ['App\Core\Middleware::csrf']);
