<?php
namespace App\Modules\Interaction;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;
use PDO;

class ReviewController extends Controller {

    private ReviewModel $reviewModel;

    public function __construct() {
        $this->reviewModel = new ReviewModel();
    }

    /**
     * API gửi đánh giá sản phẩm (POST /api/reviews/store)
     */
    public function store(): void {
        Middleware::csrf();

        if (!Auth::check()) {
            $this->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để gửi đánh giá sản phẩm.'
            ], 401);
            return;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $rating    = (int)($_POST['rating'] ?? 5);
        $comment   = trim((string)($_POST['comment'] ?? ''));

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ.'], 400);
            return;
        }

        if ($rating < 1 || $rating > 5) {
            $this->json(['success' => false, 'message' => 'Số sao đánh giá phải từ 1 đến 5.'], 422);
            return;
        }

        if ($comment === '' || mb_strlen($comment) < 3) {
            $this->json(['success' => false, 'message' => 'Nội dung nhận xét tối thiểu 3 ký tự.'], 422);
            return;
        }

        if (mb_strlen($comment) > 1000) {
            $this->json(['success' => false, 'message' => 'Nội dung nhận xét tối đa 1.000 ký tự.'], 422);
            return;
        }

        $db = Database::getInstance();

        // Kiểm tra sản phẩm tồn tại
        $stmt = $db->prepare("SELECT id FROM products WHERE id = ? AND status = 1 LIMIT 1");
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm.'], 404);
            return;
        }

        // Lưu đánh giá mới vào bảng reviews
        $insert = $db->prepare("
            INSERT INTO reviews (user_id, product_id, rating, comment, status, created_at)
            VALUES (?, ?, ?, ?, 1, NOW())
        ");
        $success = $insert->execute([
            Auth::id(),
            $productId,
            $rating,
            htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')
        ]);

        if ($success) {
            $user = Auth::user();
            $this->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã gửi đánh giá sản phẩm thành công!',
                'review'  => [
                    'full_name'  => $user['full_name'] ?? 'Khách hàng',
                    'rating'     => $rating,
                    'comment'    => htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'),
                    'created_at' => date('d/m/Y H:i')
                ]
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể lưu đánh giá. Vui lòng thử lại.'], 500);
        }
    }
}
