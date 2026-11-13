<?php
namespace App\Modules\Auth;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\GoogleAuth;
use App\Core\Mailer;
use App\Core\Middleware;
use App\Core\QrCode;
use App\Core\Request;
use App\Core\Totp;

class AuthController extends Controller {
    private const MAX_LOGIN_ATTEMPTS  = 5;
    private const LOCKOUT_SECONDS     = 300;
    private const MAX_2FA_ATTEMPTS    = 5;
    private const RESEND_COOLDOWN_SEC = 60;

    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void {
        if (Auth::check()) {
            redirect('/');
        }
        unset($_SESSION['2fa_pending']);
        $this->view('client.auth.login', [
            'title' => 'Đăng nhập tài khoản - PCMarket',
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

        // Bắt buộc xác nhận email trước khi được đăng nhập
        if (empty($user['email_verified_at'])) {
            $this->clearFailedLogins($email);
            $resendUrl = htmlspecialchars(base_url('gui-lai-xac-nhan?email=' . rawurlencode($email)), ENT_QUOTES, 'UTF-8');
            $this->redirectWith(
                '/dang-nhap',
                'error',
                "Tài khoản chưa được xác nhận qua Gmail. Vui lòng kiểm tra hộp thư hoặc <a href=\"{$resendUrl}\">gửi lại email xác nhận</a>."
            );
        }

        $this->clearFailedLogins($email);

        // Nếu tài khoản đang bật 2FA -> chuyển sang bước xác thực 2FA (TOTP / Mã dự phòng)
        if ((int)($user['two_factor_enabled'] ?? 0) === 1 && !empty($user['two_factor_secret'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['2fa_pending'] = [
                'user_id'    => (int)$user['id'],
                'email'      => $user['email'],
                'expires_at' => time() + self::LOCKOUT_SECONDS,
                'attempts'   => 0,
            ];
            $this->redirectWith('/xac-thuc-2fa', 'info', 'Vui lòng nhập mã xác thực 2 bước (Authenticator) hoặc mã dự phòng để hoàn tất đăng nhập.');
        }

        $this->completeLogin($user, 'Tài khoản vừa đăng nhập thành công bằng email và mật khẩu.');
    }

    public function redirectToGoogle(): void {
        if (Auth::check()) {
            redirect('/');
        }

        if (!GoogleAuth::isConfigured()) {
            $this->redirectWith(
                '/dang-nhap',
                'info',
                'Chức năng Đăng nhập Google đang chờ thiết lập Client ID. Quản trị viên vui lòng cấu hình <code>GOOGLE_CLIENT_ID</code> và <code>GOOGLE_CLIENT_SECRET</code> trong file <code>.env</code> theo hướng dẫn tại Google Cloud Console.'
            );
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2_google_state'] = $state;

        $authUrl = GoogleAuth::buildAuthUrl($state);
        header('Location: ' . $authUrl);
        exit;
    }

    public function handleGoogleCallback(): void {
        if (Auth::check()) {
            redirect('/');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = trim((string)($_GET['error'] ?? ''));
        if ($error !== '') {
            $this->redirectWith('/dang-nhap', 'error', 'Bạn đã hủy quá trình đăng nhập bằng Google.');
        }

        $state = trim((string)($_GET['state'] ?? ''));
        $savedState = (string)($_SESSION['oauth2_google_state'] ?? '');
        unset($_SESSION['oauth2_google_state']);

        if ($state === '' || $savedState === '' || !hash_equals($savedState, $state)) {
            $this->redirectWith('/dang-nhap', 'error', 'Phiên xác thực Google không hợp lệ hoặc đã hết hạn (CSRF state mismatch). Vui lòng thử lại!');
        }

        $code = trim((string)($_GET['code'] ?? ''));
        if ($code === '') {
            $this->redirectWith('/dang-nhap', 'error', 'Không nhận được mã xác thực (authorization code) từ Google.');
        }

        $tokenData = GoogleAuth::exchangeCodeForToken($code);
        if (!$tokenData || empty($tokenData['access_token'])) {
            $this->redirectWith('/dang-nhap', 'error', 'Không thể kết nối lấy access token từ máy chủ Google. Vui lòng thử lại sau ít phút.');
        }

        $userInfo = GoogleAuth::fetchUserInfo((string)$tokenData['access_token']);
        if (!$userInfo || empty($userInfo['email'])) {
            $this->redirectWith('/dang-nhap', 'error', 'Không thể lấy thông tin tài khoản từ Google.');
        }

        $googleId = (string)($userInfo['sub'] ?? '');
        $email    = strtolower(trim((string)$userInfo['email']));
        $fullName = trim((string)($userInfo['name'] ?? '')) ?: 'Khách hàng Google';
        $picture  = trim((string)($userInfo['picture'] ?? '')) ?: 'assets/images/default-avatar.png';

        // 1. Kiểm tra tài khoản theo google_id
        $user = $this->userModel->findByGoogleId($googleId);

        // 2. Nếu chưa liên kết google_id, tìm theo email để liên kết tự động
        if (!$user) {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $this->userModel->linkGoogleAccount((int)$user['id'], $googleId);
                $user = $this->userModel->find((int)$user['id']);
            }
        }

        // 3. Nếu chưa từng tồn tại tài khoản -> Tự động đăng ký mới!
        if (!$user) {
            $newUserId = $this->userModel->createGoogleUser([
                'full_name' => $fullName,
                'email'     => $email,
                'google_id' => $googleId,
                'avatar'    => $picture,
            ]);
            $user = $this->userModel->find($newUserId);
            $this->clearFailedLogins($email);
            $this->completeLogin(
                $user,
                'Tài khoản vừa được tạo mới và đăng nhập thành công qua Google OAuth 2.0.'
            );
            return;
        }

        // Kiểm tra trạng thái tài khoản
        if ((int)($user['status'] ?? 1) !== 1) {
            $this->redirectWith('/dang-nhap', 'error', 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ quản trị viên!');
        }

        $this->clearFailedLogins($email);

        // Nếu tài khoản có bật 2FA -> chuyển sang bước 2FA
        if ((int)($user['two_factor_enabled'] ?? 0) === 1 && !empty($user['two_factor_secret'])) {
            session_regenerate_id(true);
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['2fa_pending'] = [
                'user_id'    => (int)$user['id'],
                'email'      => $user['email'],
                'expires_at' => time() + self::LOCKOUT_SECONDS,
                'attempts'   => 0,
            ];
            $this->redirectWith('/xac-thuc-2fa', 'info', 'Vui lòng nhập mã xác thực 2 bước (Authenticator) hoặc mã dự phòng để hoàn tất đăng nhập Google.');
        }

        $this->completeLogin(
            $user,
            'Tài khoản vừa đăng nhập thành công qua Google OAuth 2.0.'
        );
    }

    public function showTwoFactorChallenge(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $pending = $this->getValidTwoFactorPending();
        if ($pending === null) {
            $this->redirectWith('/dang-nhap', 'error', 'Phiên xác thực 2 bước đã hết hạn. Vui lòng đăng nhập lại!');
        }

        $this->view('client.auth.two_factor_challenge', [
            'title' => 'Xác thực 2 bước (2FA) - PCMarket',
            'email' => $pending['email'] ?? '',
        ]);
    }

    public function verifyTwoFactorChallenge(): void {
        $pending = $this->getValidTwoFactorPending();
        if ($pending === null) {
            $this->redirectWith('/dang-nhap', 'error', 'Phiên xác thực 2 bước đã hết hạn. Vui lòng đăng nhập lại!');
        }

        $user = $this->userModel->find((int)$pending['user_id']);
        if (!$user || (int)$user['status'] !== 1 || (int)($user['two_factor_enabled'] ?? 0) !== 1) {
            unset($_SESSION['2fa_pending']);
            $this->redirectWith('/dang-nhap', 'error', 'Không thể xác thực tài khoản lúc này. Vui lòng đăng nhập lại!');
        }

        $mode       = (string)Request::post('auth_mode', 'totp');
        $totpCode   = trim((string)Request::post('totp_code', ''));
        $backupCode = trim((string)Request::post('backup_code', ''));

        if ($mode === 'backup' || ($totpCode === '' && $backupCode !== '')) {
            $remaining = $this->userModel->consumeBackupCode((int)$user['id'], $backupCode);
            if ($remaining !== null) {
                unset($_SESSION['2fa_pending']);
                if ($remaining <= 2) {
                    set_flash('info', "Bạn đã dùng 1 mã dự phòng. Hiện chỉ còn {$remaining}/10 mã chưa sử dụng — nên tạo lại mã mới trong phần Bảo mật.");
                }
                $this->completeLogin(
                    $user,
                    "Tài khoản vừa đăng nhập thành công bằng mã dự phòng 2FA (còn lại {$remaining}/10 mã dự phòng chưa dùng)."
                );
            }
        } else {
            $plainSecret = Totp::decryptSecret($user['two_factor_secret'] ?? null);
            $lastSlice   = isset($user['last_totp_slice']) ? (int)$user['last_totp_slice'] : null;
            $matchedSlice = null;

            if ($plainSecret !== null && Totp::verifyCode($plainSecret, $totpCode, 1, $lastSlice, $matchedSlice)) {
                if ($matchedSlice !== null) {
                    $this->userModel->updateLastTotpSlice((int)$user['id'], $matchedSlice);
                }
                unset($_SESSION['2fa_pending']);
                $this->completeLogin($user, 'Tài khoản vừa đăng nhập thành công qua xác thực 2 bước (Authenticator TOTP).');
            }
        }

        // Tăng bộ đếm thử sai 2FA
        $attempts = (int)($pending['attempts'] ?? 0) + 1;
        $_SESSION['2fa_pending']['attempts'] = $attempts;

        if ($attempts >= self::MAX_2FA_ATTEMPTS) {
            unset($_SESSION['2fa_pending']);
            $this->recordLockout((string)$user['email']);
            $this->redirectWith('/dang-nhap', 'error', 'Bạn đã nhập sai mã 2FA quá 5 lần. Tài khoản tạm khóa trong 5 phút để bảo vệ an toàn!');
        }

        $left = self::MAX_2FA_ATTEMPTS - $attempts;
        $this->redirectWith('/xac-thuc-2fa', 'error', "Mã xác thực 2FA hoặc mã dự phòng không đúng! (Còn {$left} lần thử)");
    }

    public function cancelTwoFactorChallenge(): void {
        unset($_SESSION['2fa_pending']);
        $this->redirectWith('/dang-nhap', 'info', 'Đã hủy phiên đăng nhập 2FA.');
    }

    public function showRegister(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('client.auth.register', [
            'title' => 'Đăng ký tài khoản - PCMarket',
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

        $token = bin2hex(random_bytes(32));
        $this->userModel->setVerificationToken($userId, $token, 24);
        $verifyLink = base_url('xac-nhan-email?token=' . rawurlencode($token));
        Mailer::sendAccountVerification($email, $fullName, $verifyLink);

        $resendUrl = htmlspecialchars(base_url('gui-lai-xac-nhan?email=' . rawurlencode($email)), ENT_QUOTES, 'UTF-8');
        $this->redirectWith(
            '/dang-nhap',
            'success',
            "Đăng ký thành công! Chúng tôi đã gửi liên kết xác nhận tài khoản tới Gmail của bạn (hiệu lực 24 giờ). Chưa nhận được? <a href=\"{$resendUrl}\">Gửi lại email xác nhận</a>."
        );
    }

    public function verifyEmail(): void {
        $token = trim((string)($_GET['token'] ?? ''));
        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            $this->redirectWith('/gui-lai-xac-nhan', 'error', 'Liên kết xác nhận email không hợp lệ hoặc đã thiếu!');
        }

        $user = $this->userModel->findByValidVerificationToken($token);
        if (!$user) {
            $this->redirectWith('/gui-lai-xac-nhan', 'error', 'Liên kết xác nhận đã hết hạn hoặc đã được sử dụng trước đó.');
        }

        $this->userModel->markEmailVerified((int)$user['id']);
        $this->redirectWith('/dang-nhap', 'success', 'Xác nhận tài khoản thành công! Bây giờ bạn có thể đăng nhập vào PCMarket.');
    }

    public function showResendVerification(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $prefillEmail = strtolower(trim((string)($_GET['email'] ?? '')));
        if (!filter_var($prefillEmail, FILTER_VALIDATE_EMAIL)) {
            $prefillEmail = '';
        }
        $this->view('client.auth.resend_verification', [
            'title'        => 'Gửi lại email xác nhận - PCMarket',
            'prefillEmail' => $prefillEmail,
        ]);
    }

    public function resendVerification(): void {
        $email = strtolower(trim((string)Request::post('email', '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWith('/gui-lai-xac-nhan', 'error', 'Vui lòng nhập một địa chỉ email hợp lệ!');
        }

        $cooldownRemaining = $this->getResendCooldownSeconds($email);
        if ($cooldownRemaining > 0) {
            $this->redirectWith(
                '/gui-lai-xac-nhan?email=' . rawurlencode($email),
                'error',
                "Vui lòng chờ {$cooldownRemaining} giây trước khi gửi lại email xác nhận."
            );
        }

        $this->markResendCooldown($email);
        $user = $this->userModel->findByEmail($email);
        if ($user && (int)$user['status'] === 1 && empty($user['email_verified_at'])) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->setVerificationToken((int)$user['id'], $token, 24);
            $verifyLink = base_url('xac-nhan-email?token=' . rawurlencode($token));
            Mailer::sendAccountVerification($user['email'], (string)$user['full_name'], $verifyLink);
        }

        $this->redirectWith(
            '/dang-nhap',
            'info',
            'Nếu email đang chờ xác nhận trong hệ thống, liên kết kích hoạt mới đã được gửi tới hộp thư của bạn (hiệu lực 24 giờ).'
        );
    }

    public function logout(): void {
        Auth::logout();
        $this->redirectWith('/dang-nhap', 'info', 'Bạn đã đăng xuất an toàn.');
    }

    public function showForgotPassword(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $this->view('client.auth.forgot_password', [
            'title' => 'Quên mật khẩu - PCMarket',
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
            'email' => $user['email'],
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
        Mailer::sendSecurityAlert(
            (string)$user['email'],
            (string)$user['full_name'],
            'Mật khẩu đã được đặt lại',
            'Mật khẩu tài khoản PCMarket của bạn vừa được cập nhật thông qua liên kết khôi phục email.'
        );

        $this->redirectWith('/dang-nhap', 'success', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay với mật khẩu mới.');
    }

    public function showSecurity(): void {
        Middleware::auth();
        $user = $this->userModel->find((int)Auth::id());
        if (!$user) {
            Auth::logout();
            $this->redirectWith('/dang-nhap', 'error', 'Tài khoản không tồn tại.');
        }

        Auth::refreshUser($user);

        $twoFactorEnabled = (int)($user['two_factor_enabled'] ?? 0) === 1;
        $setupSecret = null;
        $qrSvg = null;
        $otpUri = null;

        if (!$twoFactorEnabled) {
            $sessionSecret = $_SESSION['2fa_setup_secret'] ?? '';
            if (!is_string($sessionSecret) || strlen($sessionSecret) < 16) {
                $sessionSecret = Totp::generateSecret();
                $_SESSION['2fa_setup_secret'] = $sessionSecret;
            }
            $setupSecret = $sessionSecret;
            $otpUri = Totp::getOtpAuthUri('PCMarket', (string)$user['email'], $setupSecret);
            $qrSvg = QrCode::svg($otpUri, 196);
        } else {
            unset($_SESSION['2fa_setup_secret']);
        }

        $freshBackupCodes = $_SESSION['2fa_backup_codes_once'] ?? null;
        unset($_SESSION['2fa_backup_codes_once']);

        $this->view('client.auth.security', [
            'title'              => 'Trung tâm bảo mật & 2FA - PCMarket',
            'user'               => $user,
            'twoFactorEnabled'   => $twoFactorEnabled,
            'unusedBackupCount'  => $twoFactorEnabled ? $this->userModel->countUnusedBackupCodes((int)$user['id']) : 0,
            'setupSecret'        => $setupSecret,
            'qrSvg'              => $qrSvg,
            'otpUri'             => $otpUri,
            'freshBackupCodes'   => is_array($freshBackupCodes) ? $freshBackupCodes : null,
        ]);
    }

    public function changePassword(): void {
        Middleware::auth();
        $user = $this->userModel->find((int)Auth::id());
        if (!$user) {
            $this->redirectWith('/dang-nhap', 'error', 'Vui lòng đăng nhập lại!');
        }

        $currentPassword = (string)Request::post('current_password', '');
        $newPassword     = (string)Request::post('password', '');
        $confirm         = (string)Request::post('password_confirmation', '');

        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mật khẩu hiện tại không chính xác!');
        }

        if (strlen($newPassword) < 6 || trim($newPassword) === '') {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mật khẩu mới phải có ít nhất 6 ký tự!');
        }

        if ($newPassword === $currentPassword) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mật khẩu mới phải khác mật khẩu hiện tại!');
        }

        if ($newPassword !== $confirm) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Xác nhận mật khẩu mới không trùng khớp!');
        }

        $this->userModel->updatePassword((int)$user['id'], $newPassword);
        $updated = $this->userModel->find((int)$user['id']);
        if ($updated) {
            Auth::login($updated);
        }

        Mailer::sendSecurityAlert(
            (string)$user['email'],
            (string)$user['full_name'],
            'Mật khẩu tài khoản đã được thay đổi',
            'Mật khẩu đăng nhập tài khoản PCMarket của bạn vừa được đổi trong Trung tâm bảo mật.'
        );

        $this->redirectWith('/tai-khoan/bao-mat', 'success', 'Đổi mật khẩu thành công!');
    }

    public function enableTwoFactor(): void {
        Middleware::auth();
        $user = $this->userModel->find((int)Auth::id());
        if (!$user) {
            $this->redirectWith('/dang-nhap', 'error', 'Vui lòng đăng nhập lại!');
        }

        if ((int)($user['two_factor_enabled'] ?? 0) === 1) {
            $this->redirectWith('/tai-khoan/bao-mat', 'info', 'Tài khoản đã bật xác thực 2 bước từ trước.');
        }

        $currentPassword = (string)Request::post('current_password', '');
        $totpCode        = trim((string)Request::post('totp_code', ''));
        $setupSecret     = (string)($_SESSION['2fa_setup_secret'] ?? '');

        if ($setupSecret === '') {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Phiên thiết lập 2FA đã hết hạn, vui lòng quét lại mã QR.');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mật khẩu hiện tại không chính xác!');
        }

        if (!Totp::verifyCode($setupSecret, $totpCode)) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mã 6 số từ ứng dụng Authenticator chưa chính xác hoặc đã hết hạn.');
        }

        $backupCodes = Totp::generateBackupCodes(10);
        $this->userModel->enableTwoFactor((int)$user['id'], $setupSecret, $backupCodes);
        unset($_SESSION['2fa_setup_secret']);
        $_SESSION['2fa_backup_codes_once'] = $backupCodes;
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

        $fresh = $this->userModel->find((int)$user['id']);
        if ($fresh) {
            Auth::refreshUser($fresh);
        }

        Mailer::sendSecurityAlert(
            (string)$user['email'],
            (string)$user['full_name'],
            'Đã bật xác thực 2 bước (2FA)',
            'Tài khoản của bạn vừa bật bảo vệ 2FA (Authenticator TOTP) và sinh 10 mã dự phòng dùng 1 lần.'
        );

        $this->redirectWith('/tai-khoan/bao-mat', 'success', 'Bật xác thực 2 bước (2FA) thành công! Hãy lưu ngay 10 mã dự phòng bên dưới.');
    }

    public function regenerateBackupCodes(): void {
        Middleware::auth();
        $user = $this->userModel->find((int)Auth::id());
        if (!$user || (int)($user['two_factor_enabled'] ?? 0) !== 1) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Tài khoản chưa bật xác thực 2 bước.');
        }

        $currentPassword = (string)Request::post('current_password', '');
        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mật khẩu hiện tại không chính xác!');
        }

        $newCodes = Totp::generateBackupCodes(10);
        $this->userModel->replaceBackupCodes((int)$user['id'], $newCodes);
        $_SESSION['2fa_backup_codes_once'] = $newCodes;

        Mailer::sendSecurityAlert(
            (string)$user['email'],
            (string)$user['full_name'],
            'Đã tạo lại bộ mã dự phòng 2FA',
            '10 mã dự phòng mới vừa được cấp cho tài khoản của bạn. Toàn bộ các mã dự phòng cũ đã bị vô hiệu hóa.'
        );

        $this->redirectWith('/tai-khoan/bao-mat', 'success', 'Đã tạo mới 10 mã dự phòng và vô hiệu hóa bộ mã cũ!');
    }

    public function disableTwoFactor(): void {
        Middleware::auth();
        $user = $this->userModel->find((int)Auth::id());
        if (!$user || (int)($user['two_factor_enabled'] ?? 0) !== 1) {
            $this->redirectWith('/tai-khoan/bao-mat', 'info', 'Xác thực 2 bước hiện đang tắt.');
        }

        $currentPassword = (string)Request::post('current_password', '');
        $verifyCode      = trim((string)Request::post('verification_code', ''));

        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mật khẩu hiện tại không chính xác!');
        }

        $verified = false;
        $plainSecret = Totp::decryptSecret($user['two_factor_secret'] ?? null);
        $cleanDigits = preg_replace('/\s+/', '', $verifyCode) ?? '';

        if ($plainSecret !== null && preg_match('/^\d{6}$/', $cleanDigits) === 1) {
            $lastSlice = isset($user['last_totp_slice']) ? (int)$user['last_totp_slice'] : null;
            $verified = Totp::verifyCode($plainSecret, $cleanDigits, 1, $lastSlice);
        } elseif ($this->userModel->consumeBackupCode((int)$user['id'], $verifyCode) !== null) {
            $verified = true;
        }

        if (!$verified) {
            $this->redirectWith('/tai-khoan/bao-mat', 'error', 'Mã OTP 6 số hoặc mã dự phòng để tắt 2FA không hợp lệ!');
        }

        $this->userModel->disableTwoFactor((int)$user['id']);
        $fresh = $this->userModel->find((int)$user['id']);
        if ($fresh) {
            Auth::refreshUser($fresh);
        }

        Mailer::sendSecurityAlert(
            (string)$user['email'],
            (string)$user['full_name'],
            'Đã tắt xác thực 2 bước (2FA)',
            'Tính năng bảo vệ 2FA và toàn bộ mã dự phòng trên tài khoản PCMarket của bạn vừa được tắt.'
        );

        $this->redirectWith('/tai-khoan/bao-mat', 'info', 'Đã tắt xác thực 2 bước (2FA) trên tài khoản.');
    }

    private function completeLogin(array $user, string $alertSummary): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $this->userModel->recordLoginMeta((int)$user['id'], $ip);
        $user['last_login_at'] = date('Y-m-d H:i:s');
        $user['last_login_ip'] = $ip;

        $intendedUrl = $_SESSION['_intended_url'] ?? null;
        unset($_SESSION['_intended_url']);

        Auth::login($user);

        Mailer::sendSecurityAlert(
            (string)$user['email'],
            (string)$user['full_name'],
            'Đăng nhập tài khoản thành công',
            $alertSummary
        );

        if (
            is_string($intendedUrl) &&
            $intendedUrl !== '' &&
            strpos($intendedUrl, '/') === 0 &&
            strpos($intendedUrl, '//') !== 0 &&
            strpos($intendedUrl, '/dang-') !== 0 &&
            strpos($intendedUrl, '/xac-thuc-2fa') !== 0
        ) {
            $this->redirectWith($intendedUrl, 'success', 'Đăng nhập thành công! Chào bạn ' . $user['full_name']);
        }

        if ($user['role'] === 'admin' || $user['role'] === 'staff') {
            $this->redirectWith('/admin', 'success', 'Chào mừng Quản trị viên: ' . $user['full_name']);
        }

        $this->redirectWith('/', 'success', 'Đăng nhập thành công! Chào bạn ' . $user['full_name']);
    }

    private function getValidTwoFactorPending(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $pending = $_SESSION['2fa_pending'] ?? null;
        if (!is_array($pending) || empty($pending['user_id']) || (int)($pending['expires_at'] ?? 0) < time()) {
            unset($_SESSION['2fa_pending']);
            return null;
        }
        return $pending;
    }

    private function getLockFilePath(string $email): string {
        $dir = dirname(__DIR__, 3) . '/storage/cache/auth_locks';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return $dir . '/' . hash('sha256', strtolower($email) . '|' . $ip) . '.json';
    }

    private function getResendFilePath(string $email): string {
        $dir = dirname(__DIR__, 3) . '/storage/cache/auth_locks';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return $dir . '/resend_' . hash('sha256', strtolower($email) . '|' . $ip) . '.txt';
    }

    private function getResendCooldownSeconds(string $email): int {
        $file = $this->getResendFilePath($email);
        if (!is_file($file)) {
            return 0;
        }
        $until = (int)trim((string)@file_get_contents($file));
        return max(0, $until - time());
    }

    private function markResendCooldown(string $email): void {
        @file_put_contents($this->getResendFilePath($email), (string)(time() + self::RESEND_COOLDOWN_SEC), LOCK_EX);
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

    private function recordLockout(string $email): void {
        $lockUntil = time() + self::LOCKOUT_SECONDS;
        $_SESSION['login_lockout'] = $lockUntil;
        unset($_SESSION['login_attempts']);
        @file_put_contents(
            $this->getLockFilePath($email),
            json_encode(['attempts' => self::MAX_LOGIN_ATTEMPTS, 'lock_until' => $lockUntil]),
            LOCK_EX
        );
    }

    private function clearFailedLogins(string $email): void {
        unset($_SESSION['login_attempts'], $_SESSION['login_lockout']);
        $file = $this->getLockFilePath($email);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}
