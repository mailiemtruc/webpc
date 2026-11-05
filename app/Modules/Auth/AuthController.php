<?php
namespace App\Modules\Auth;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;

class AuthController extends Controller {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('client.auth.login', [
            'title' => 'Đăng nhập tài khoản - PCMART'
        ]);
    }

    public function login(): void {
        $email = Request::post('email');
        $password = Request::post('password');

        if (empty($email) || empty($password)) {
            $this->redirectWith('/dang-nhap', 'error', 'Vui lòng điền đầy đủ email và mật khẩu!');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->redirectWith('/dang-nhap', 'error', 'Email hoặc mật khẩu không chính xác!');
        }

        if ((int)$user['status'] !== 1) {
            $this->redirectWith('/dang-nhap', 'error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ!');
        }

        if (!password_verify($password, $user['password'])) {
            $this->redirectWith('/dang-nhap', 'error', 'Email hoặc mật khẩu không chính xác!');
        }

        // Đăng nhập thành công
        Auth::login($user);

        if ($user['role'] === 'admin' || $user['role'] === 'staff') {
            $this->redirectWith('/admin', 'success', 'Chào mừng Quản trị viên: ' . $user['full_name']);
        }

        $this->redirectWith('/', 'success', 'Đăng nhập thành công! Chào bạn ' . $user['full_name']);
    }

    public function showRegister(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('client.auth.register', [
            'title' => 'Đăng ký tài khoản - PCMART'
        ]);
    }

    public function register(): void {
        $fullName = Request::post('full_name');
        $email = Request::post('email');
        $phone = Request::post('phone');
        $password = Request::post('password');
        $passwordConfirm = Request::post('password_confirmation');

        if (empty($fullName) || empty($email) || empty($password)) {
            $this->redirectWith('/dang-ky', 'error', 'Vui lòng điền các trường bắt buộc!');
        }

        if ($password !== $passwordConfirm) {
            $this->redirectWith('/dang-ky', 'error', 'Xác nhận mật khẩu không trùng khớp!');
        }

        if (strlen($password) < 6) {
            $this->redirectWith('/dang-ky', 'error', 'Mật khẩu phải có ít nhất 6 ký tự!');
        }

        if ($this->userModel->findByEmail($email)) {
            $this->redirectWith('/dang-ky', 'error', 'Email này đã được sử dụng. Vui lòng chọn email khác!');
        }

        $userId = $this->userModel->registerUser([
            'full_name' => $fullName,
            'email'     => $email,
            'phone'     => $phone,
            'password'  => $password,
        ]);

        $newUser = $this->userModel->find($userId);
        Auth::login($newUser);

        $this->redirectWith('/', 'success', 'Đăng ký tài khoản thành công!');
    }

    public function logout(): void {
        Auth::logout();
        $this->redirectWith('/dang-nhap', 'info', 'Bạn đã đăng xuất thành công.');
    }
}

