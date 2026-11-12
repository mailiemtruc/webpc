<?php
namespace App\Modules\Auth;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Request;

class AuthController extends Controller {
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS    = 300;

    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('client.auth.login', [
            'title' => 'Đăng nhập tài khoản - PCMarket'
        ]);
    }

    public function login(): void {
        $email    = strtolower(trim((string)Request::post('email', '')));
        $password = (string)Request::post('password', '');

        if ($email === '' || $password === '') {
            $this->redirectWith('/dang-nhap', 'error', 'Vui lòng điền đầy đủ email và mật khẩu!');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWith('/dang-nhap', 'error', 'Địa chỉ email không hợp lệ!');
        }

        // Kiểm tra khóa brute-force theo cả server cache (IP + email) và session
        $lockRemaining = $this->getLockoutRemainingSeconds($email);
        if ($lockRemaining > 0) {
            $remainingMinutes = (int)ceil($lockRemaining / 60);
            $this->redirectWith('/dang-nhap', 'error', "Tài khoản tạm khóa do nhập sai quá nhiều lần. Vui lòng thử lại sau {$remainingMinutes} phút!");
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $attempts = $this->recordFailedLogin($email);
            if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
                $this->redirectWith('/dang-nhap', 'error', 'Bạn đã nhập sai 5 lần liên tiếp! Để bảo mật, chức năng đăng nhập tạm khóa trong 5 phút.');
            }
            $remaining = self::MAX_LOGIN_ATTEMPTS - $attempts;
            $this->redirectWith('/dang-nhap', 'error', "Email hoặc mật khẩu không chính xác! (Còn {$remaining} lần thử)");
        }

        if ((int)$user['status'] !== 1) {
            $this->redirectWith('/dang-nhap', 'error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ!');
        }

        $this->clearFailedLogins($email);
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
            'title' => 'Đăng ký tài khoản - PCMarket'
        ]);
    }

    public function register(): void {
        $fullName        = trim((string)($_POST['full_name'] ?? ''));
        $email           = strtolower(trim((string)($_POST['email'] ?? '')));
        $phone           = trim((string)($_POST['phone'] ?? ''));
        $password        = (string)Request::post('password', '');
        $passwordConfirm = (string)Request::post('password_confirmation', '');

        if ($fullName === '' || $email === '' || $password === '') {
            $this->redirectWith('/dang-ky', 'error', 'Vui lòng điền các trường bắt buộc!');
        }

        if (mb_strlen($fullName, 'UTF-8') < 2 || mb_strlen($fullName, 'UTF-8') > 100) {
            $this->redirectWith('/dang-ky', 'error', 'Họ và tên phải từ 2 đến 100 ký tự.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWith('/dang-ky', 'error', 'Địa chỉ email không hợp lệ!');
        }

        if ($phone !== '' && !preg_match('/^[0-9\+\s\.\-]{8,15}$/', $phone)) {
            $this->redirectWith('/dang-ky', 'error', 'Số điện thoại không hợp lệ (cần 8-15 ký tự số).');
        }

        if (strlen($password) < 6 || trim($password) === '') {
            $this->redirectWith('/dang-ky', 'error', 'Mật khẩu phải có ít nhất 6 ký tự!');
        }

        if ($password !== $passwordConfirm) {
            $this->redirectWith('/dang-ky', 'error', 'Xác nhận mật khẩu không trùng khớp!');
        }

        if ($this->userModel->findByEmail($email)) {
            $this->redirectWith('/dang-ky', 'error', 'Email này đã được sử dụng. Vui lòng chọn email khác!');
        }

        try {
            $userId = $this->userModel->registerUser([
                'full_name' => $fullName,
                'email'     => $email,
                'phone'     => $phone,
                'password'  => $password,
            ]);
        } catch (\PDOException $e) {
            $this->redirectWith('/dang-ky', 'error', 'Email này đã được sử dụng hoặc không thể đăng ký lúc này.');
        }

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
        $email = strtolower(trim((string)Request::post('email', '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Vui lòng nhập một địa chỉ email hợp lệ!');
        }

        $user = $this->userModel->findByEmail($email);
        if ($user && (int)$user['status'] === 1) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->setResetToken($email, $token, 30);
            $resetLink = base_url('dat-lai-mat-khau?token=' . rawurlencode($token));
            Mailer::sendPasswordReset($user['email'], (string)$user['full_name'], $resetLink);
        }

        // Luôn trả cùng một thông báo chuẩn bảo mật để không làm lộ email tồn tại trong hệ thống
        $this->redirectWith(
            '/quen-mat-khau',
            'info',
            'Nếu địa chỉ email tồn tại trong hệ thống, hướng dẫn đặt lại mật khẩu đã được gửi đến hộp thư của bạn (hiệu lực 30 phút).'
        );
    }

    public function showResetPassword(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $token = trim((string)($_GET['token'] ?? ''));
        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã thiếu!');
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
        $token    = trim((string)Request::raw('token', ''));
        $password = (string)Request::post('password', '');
        $confirm  = (string)Request::post('password_confirmation', '');

        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Mã xác thực không hợp lệ!');
        }

        $user = $this->userModel->findByValidResetToken($token);
        if (!$user) {
            $this->redirectWith('/quen-mat-khau', 'error', 'Mã xác thực không hợp lệ hoặc đã hết hạn!');
        }

        if (strlen($password) < 6 || trim($password) === '') {
            $this->redirectWith('/dat-lai-mat-khau?token=' . rawurlencode($token), 'error', 'Mật khẩu mới phải có ít nhất 6 ký tự!');
        }

        if ($password !== $confirm) {
            $this->redirectWith('/dat-lai-mat-khau?token=' . rawurlencode($token), 'error', 'Xác nhận mật khẩu mới không trùng khớp!');
        }

        $this->userModel->updatePassword((int)$user['id'], $password);
        $this->clearFailedLogins((string)$user['email']);
        $this->redirectWith('/dang-nhap', 'success', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay với mật khẩu mới.');
    }

    private function getLockFilePath(string $email): string {
        $dir = dirname(__DIR__, 3) . '/storage/cache/auth_locks';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return $dir . '/' . hash('sha256', strtolower($email) . '|' . $ip) . '.json';
    }

    private function readServerLockState(string $email): array {
        $file = $this->getLockFilePath($email);
        if (!is_file($file)) {
            return ['attempts' => 0, 'lock_until' => 0];
        }
        $decoded = json_decode((string)@file_get_contents($file), true);
        if (!is_array($decoded)) {
            return ['attempts' => 0, 'lock_until' => 0];
        }
        return [
            'attempts'   => (int)($decoded['attempts'] ?? 0),
            'lock_until' => (int)($decoded['lock_until'] ?? 0),
        ];
    }

    private function getLockoutRemainingSeconds(string $email): int {
        $now = time();
        $sessionLockUntil = (int)($_SESSION['login_lockout'] ?? 0);
        $serverState      = $this->readServerLockState($email);
        $lockUntil        = max($sessionLockUntil, $serverState['lock_until']);

        if ($lockUntil > $now) {
            return $lockUntil - $now;
        }
        return 0;
    }

    private function recordFailedLogin(string $email): int {
        $now = time();
        $state = $this->readServerLockState($email);
        if ($state['lock_until'] > 0 && $state['lock_until'] <= $now) {
            $state = ['attempts' => 0, 'lock_until' => 0];
        }

        $attempts = max((int)($_SESSION['login_attempts'] ?? 0), $state['attempts']) + 1;
        $lockUntil = 0;

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $lockUntil = $now + self::LOCKOUT_SECONDS;
            $_SESSION['login_lockout'] = $lockUntil;
            unset($_SESSION['login_attempts']);
        } else {
            $_SESSION['login_attempts'] = $attempts;
        }

        @file_put_contents(
            $this->getLockFilePath($email),
            json_encode(['attempts' => $attempts, 'lock_until' => $lockUntil]),
            LOCK_EX
        );

        return $attempts;
    }

    private function clearFailedLogins(string $email): void {
        unset($_SESSION['login_attempts'], $_SESSION['login_lockout']);
        $file = $this->getLockFilePath($email);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
