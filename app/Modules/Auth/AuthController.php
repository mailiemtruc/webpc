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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra cơ chế chống Brute Force
        if (isset($_SESSION['login_lockout']) && time() < $_SESSION['login_lockout']) {
            $remainingSeconds = $_SESSION['login_lockout'] - time();
            $remainingMinutes = ceil($remainingSeconds / 60);
            $this->redirectWith('/dang-nhap', 'error', "Tài khoản tạm khóa do nhập sai quá 5 lần. Vui lòng thử lại sau {$remainingMinutes} phút!");
        }

        $email = trim(Request::post('email') ?? '');
        $password = Request::post('password') ?? '';

        if (empty($email) || empty($password)) {
            $this->redirectWith('/dang-nhap', 'error', 'Vui lòng điền đầy đủ email và mật khẩu!');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            // Tăng biến đếm đăng nhập sai
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= 5) {
                $_SESSION['login_lockout'] = time() + 300; // Khóa 5 phút
                unset($_SESSION['login_attempts']);
                $this->redirectWith('/dang-nhap', 'error', 'Bạn đã nhập sai 5 lần liên tiếp! Để bảo mật, chức năng đăng nhập tạm khóa trong 5 phút.');
            }
            $remaining = 5 - $_SESSION['login_attempts'];
            $this->redirectWith('/dang-nhap', 'error', "Email hoặc mật khẩu không chính xác! (Còn {$remaining} lần thử)");
        }

        if ((int)$user['status'] !== 1) {
            $this->redirectWith('/dang-nhap', 'error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ!');
        }

        // Đăng nhập thành công -> Reset bộ đếm lỗi
        unset($_SESSION['login_attempts'], $_SESSION['login_lockout']);

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

    public function showForgotPassword(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('client.auth.forgot_password', [
            'title' => 'Quên mật khẩu - PCMarket'
        ]);
    }

    public function sendResetLink(): void {
        $email = trim(Request::post('email') ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Vui lòng nhập một địa chỉ email hợp lệ!');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            $this->redirectWith('/quen-mat-khau', 'info', 'Nếu email tồn tại trong hệ thống, yêu cầu đặt lại mật khẩu đã được khởi tạo.');
        }

        $token = bin2hex(random_bytes(32));
        $this->userModel->setResetToken($email, $token, 30); // Hiệu lực 30 phút
        $resetLink = base_url("dat-lai-mat-khau?token={$token}");

        // Để thuận tiện demo và bảo vệ đồ án không cần cấu hình SMTP mailer phức tạp,
        // hệ thống cung cấp link trực tiếp trên thông báo:
        set_flash('success', "Mã đặt lại mật khẩu đã được tạo! Để demo, click vào liên kết sau: <a href='{$resetLink}' style='text-decoration:underline; font-weight:bold;'>Đặt lại mật khẩu</a> (Hiệu lực 30 phút)");
        redirect('/quen-mat-khau');
    }

    public function showResetPassword(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Mã xác thực không hợp lệ hoặc đã thiếu!');
        }

        $user = $this->userModel->findByValidResetToken($token);
        if (!$user) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn (quá 30 phút). Vui lòng gửi lại yêu cầu!');
        }

        $this->view('client.auth.reset_password', [
            'title' => 'Đặt lại mật khẩu mới - PCMarket',
            'token' => $token,
            'email' => $user['email']
        ]);
    }

    public function resetPassword(): void {
        $token = Request::post('token') ?? '';
        $password = Request::post('password') ?? '';
        $confirm = Request::post('password_confirmation') ?? '';

        if (empty($token)) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Mã xác thực không hợp lệ!');
        }

        $user = $this->userModel->findByValidResetToken($token);
        if (!$user) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Mã xác thực không hợp lệ hoặc đã hết hạn!');
        }

        if (empty($password) || strlen($password) < 6) {
            $this->redirectWith("/dat-lai-mat-khau?token={$token}", 'error', 'Mật khẩu mới phải có ít nhất 6 ký tự!');
        }

        if ($password !== $confirm) {
            $this->redirectWith("/dat-lai-mat-khau?token={$token}", 'error', 'Xác nhận mật khẩu mới không trùng khớp!');
        }

        $this->userModel->updatePassword($user['id'], $password);
        $this->redirectWith('/dang-nhap', 'success', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay với mật khẩu mới.');
    }
}

