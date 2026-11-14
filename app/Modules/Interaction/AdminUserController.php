<?php

namespace App\Modules\Interaction;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;
use App\Core\Auth;

class AdminUserController extends Controller

{
    public function __construct()
    {
        Middleware::admin();
    }

    /**
     * Hiển thị danh sách tài khoản
     */
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));

        $db = Database::getInstance();

        $sql = "
            SELECT
                id,
                full_name,
                email,
                phone,
                role,
                status,
                created_at
            FROM users
        ";

        $params = [];

        // Tìm kiếm theo tên hoặc email
        if ($q !== '') {
            $sql .= "
                WHERE full_name LIKE ?
                   OR email LIKE ?
            ";

            $params = [
                "%$q%",
                "%$q%"
            ];
        }

        $sql .= " ORDER BY created_at DESC LIMIT 200";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $this->view('admin.users.index', [
            'title' => 'Quản lý tài khoản',
            'users' => $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'q' => $q
        ]);
    }

    /**
     * Khóa hoặc mở khóa tài khoản
     */
    public function toggle(int $id): void
    {
        Middleware::csrf();

        $db = Database::getInstance();

        // Lấy thông tin tài khoản
        $sql = "
            SELECT id, role, status
            FROM users
            WHERE id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            $this->redirectWith(
                'admin/users',
                'error',
                'Không tìm thấy tài khoản.'
            );
        }

        // Không cho phép khóa tài khoản Admin
        if (
            $id === Auth::id() ||
            $user['role'] === 'admin'
        ) {
            $this->redirectWith(
                'admin/users',
                'error',
                'Không thể khóa tài khoản quản trị viên hiện tại hoặc tài khoản Admin.'
            );
        }

        // Chuyển đổi trạng thái tài khoản
        $newStatus = (int) !(bool) $user['status'];

        $updateSql = "
            UPDATE users
            SET status = ?
            WHERE id = ?
        ";

        $db->prepare($updateSql)->execute([
            $newStatus,
            $id
        ]);

        $this->redirectWith(
            'admin/users',
            'success',
            'Đã cập nhật trạng thái tài khoản.'
        );
    }
}
