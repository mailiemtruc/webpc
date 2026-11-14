<?php

namespace App\Modules\Interaction;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;

class AdminBannerController extends Controller
{
    public function __construct()
    {
        Middleware::admin();
    }

    /**
     * Hiển thị danh sách banner
     */
    public function index(): void
    {
        $db = Database::getInstance();

        $sql = "
            SELECT *
            FROM banners
            ORDER BY position, display_order, id DESC
        ";

        $rows = $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin.banners.index', [
            'title' => 'Quản lý banner',
            'banners' => $rows
        ]);
    }

    /**
     * Thêm banner mới
     */
    public function store(): void
    {
        Middleware::csrf();

        $title = trim((string) ($_POST['title'] ?? ''));
        $position = (string) ($_POST['position'] ?? 'home_main');
        $link = trim((string) ($_POST['link'] ?? ''));
        $order = (int) ($_POST['display_order'] ?? 0);

        if ($title === '') {
            $this->redirectWith(
                'admin/banners',
                'error',
                'Vui lòng nhập tiêu đề.'
            );
        }

        $image = upload_file(
            $_FILES['image'] ?? [],
            'banners',
            ['jpg', 'jpeg', 'png', 'webp'],
            5242880
        );

        if (!$image) {
            $this->redirectWith(
                'admin/banners',
                'error',
                'Ảnh banner không hợp lệ hoặc chưa được chọn.'
            );
        }

        $db = Database::getInstance();

        $sql = "
            INSERT INTO banners
                (title, image, link, position, display_order, status)
            VALUES (?, ?, ?, ?, ?, 1)
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $title,
            $image,
            $link ?: null,
            $position,
            $order
        ]);

        $this->redirectWith(
            'admin/banners',
            'success',
            'Đã thêm banner.'
        );
    }

    /**
     * Cập nhật banner
     */
    public function update(int $id): void
    {
        Middleware::csrf();

        $title = trim((string) ($_POST['title'] ?? ''));
        $link = trim((string) ($_POST['link'] ?? ''));
        $position = (string) ($_POST['position'] ?? 'home_main');
        $order = (int) ($_POST['display_order'] ?? 0);

        if ($title === '') {
            $this->redirectWith(
                'admin/banners',
                'error',
                'Tiêu đề không được để trống.'
            );
        }

        $db = Database::getInstance();
        $image = null;

        if (
            isset($_FILES['image']) &&
            ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
        ) {
            $image = upload_file(
                $_FILES['image'],
                'banners',
                ['jpg', 'jpeg', 'png', 'webp'],
                5242880
            );

            if (!$image) {
                $this->redirectWith(
                    'admin/banners',
                    'error',
                    'Ảnh không hợp lệ.'
                );
            }
        }

        if ($image) {
            $sql = "
                UPDATE banners
                SET title = ?, link = ?, position = ?,
                    display_order = ?, image = ?
                WHERE id = ?
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title,
                $link ?: null,
                $position,
                $order,
                $image,
                $id
            ]);
        } else {
            $sql = "
                UPDATE banners
                SET title = ?, link = ?, position = ?,
                    display_order = ?
                WHERE id = ?
            ";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title,
                $link ?: null,
                $position,
                $order,
                $id
            ]);
        }

        $this->redirectWith(
            'admin/banners',
            'success',
            'Đã cập nhật banner.'
        );
    }

    /**
     * Bật hoặc tắt banner
     */
    public function toggle(int $id): void
    {
        Middleware::csrf();

        $db = Database::getInstance();

        $sql = "
            UPDATE banners
            SET status = IF(status = 1, 0, 1)
            WHERE id = ?
        ";

        $db->prepare($sql)->execute([$id]);

        $this->redirectWith(
            'admin/banners',
            'success',
            'Đã cập nhật banner.'
        );
    }

    /**
     * Xóa banner
     */
    public function delete(int $id): void
    {
        Middleware::csrf();

        $db = Database::getInstance();

        $sql = "DELETE FROM banners WHERE id = ?";

        $db->prepare($sql)->execute([$id]);

        $this->redirectWith(
            'admin/banners',
            'success',
            'Đã xóa banner.'
        );
    }
}
