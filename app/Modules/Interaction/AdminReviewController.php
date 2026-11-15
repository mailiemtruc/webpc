<?php

namespace App\Modules\Interaction;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;

class AdminReviewController extends Controller
{
    private ReviewModel $reviews;

    public function __construct()
    {
        Middleware::admin();

        $this->reviews = new ReviewModel();
    }

    /**
     * Hiển thị danh sách đánh giá
     */
    public function index(): void
    {
        $this->view('admin.reviews.index', [
            'title' => 'Duyệt đánh giá',
            'reviews' => $this->reviews->adminList()
        ]);
    }

    /**
     * Bật hoặc tắt trạng thái duyệt đánh giá
     */
    public function toggle(int $id): void
    {
        Middleware::csrf();

        $db = Database::getInstance();

        $sql = "
            UPDATE reviews
            SET status = IF(status = 1, 0, 1)
            WHERE id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        $this->redirectWith(
            'admin/reviews',
            'success',
            'Đã cập nhật trạng thái đánh giá.'
        );
    }

    /**
     * Xóa đánh giá
     */
    public function delete(int $id): void
    {
        Middleware::csrf();

        $db = Database::getInstance();

        $sql = "DELETE FROM reviews WHERE id = ?";

        $db->prepare($sql)->execute([$id]);

        $this->redirectWith(
            'admin/reviews',
            'success',
            'Đã xóa đánh giá.'
        );
    }
}
