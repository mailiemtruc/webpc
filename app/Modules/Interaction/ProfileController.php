<?php
namespace App\Modules\Interaction;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;

class ProfileController extends Controller

{
    public function __construct()
    {
        Middleware::auth();
    }

    // Hiển thị hồ sơ cá nhân
    public function index(): void
    {
        $db = Database::getInstance();

        $stmt = $db->prepare(
            'SELECT id, full_name, email, phone, avatar, created_at
             FROM users WHERE id = ?'
        );

        $stmt->execute([Auth::id()]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->view('client.account.profile', [
            'title' => 'Tài khoản của tôi — PCMarket',
            'profile' => $user
        ]);
    }

    // Cập nhật thông tin cá nhân
    public function update(): void
    {
        Middleware::csrf();

        $name = trim((string)($_POST['full_name'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));

        if (
            $name === '' ||
            mb_strlen($name) > 100 ||
            ($phone !== '' &&
            !preg_match('/^[0-9+() .-]{8,20}$/', $phone))
        ) {
            $this->redirectWith(
                'tai-khoan',
                'error',
                'Thông tin không hợp lệ. Vui lòng kiểm tra họ tên và số điện thoại.'
            );
            return;
        }

        $db = Database::getInstance();

        $stmt = $db->prepare(
            'UPDATE users
             SET full_name = ?, phone = ?, updated_at = NOW()
             WHERE id = ?'
        );

        $stmt->execute([
            $name,
            $phone ?: null,
            Auth::id()
        ]);

        $refresh = $db->prepare(
            'SELECT * FROM users WHERE id = ?'
        );

        $refresh->execute([Auth::id()]);

        Auth::refreshUser(
            $refresh->fetch(\PDO::FETCH_ASSOC) ?: Auth::user()
        );

        $this->redirectWith(
            'tai-khoan',
            'success',
            'Đã cập nhật thông tin cá nhân.'
        );
    }

    // Đổi mật khẩu
    public function changePassword(): void
    {
        Middleware::csrf();

        $current = (string)($_POST['current_password'] ?? '');
        $password = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        $db = Database::getInstance();

        $stmt = $db->prepare(
            'SELECT password FROM users WHERE id = ?'
        );

        $stmt->execute([Auth::id()]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($current, $hash)) {
            $this->redirectWith(
                'tai-khoan',
                'error',
                'Mật khẩu hiện tại không chính xác.'
            );
            return;
        }

        if (strlen($password) < 8 || $password !== $confirm) {
            $this->redirectWith(
                'tai-khoan',
                'error',
                'Mật khẩu mới phải có ít nhất 8 ký tự và xác nhận trùng khớp.'
            );
            return;
        }

        $stmt = $db->prepare(
            'UPDATE users
             SET password = ?, updated_at = NOW()
             WHERE id = ?'
        );

        $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            Auth::id()
        ]);

        $this->redirectWith(
            'tai-khoan',
            'success',
            'Đổi mật khẩu thành công.'
        );
    }

    // Hiển thị lịch sử đơn hàng
    public function orders(): void
    {
        $db = Database::getInstance();

        $stmt = $db->prepare(
            'SELECT id, order_code, final_total, payment_method,
                    payment_status, status, created_at
             FROM orders
             WHERE user_id = ?
             ORDER BY created_at DESC'
        );

        $stmt->execute([Auth::id()]);

        $this->view('client.account.orders', [
            'title' => 'Lịch sử đơn hàng — PCMarket',
            'orders' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
        ]);
    }
}
