<?php

namespace App\Modules\Interaction;

use App\Core\Model;

class ReviewModel extends Model

{
    protected string $table = 'reviews';

    /**
     * Lấy danh sách đánh giá đã được duyệt của sản phẩm
     */
    public function forProduct(int $productId): array
    {
        $sql = "
            SELECT 
                r.*,
                u.full_name,
                u.avatar
            FROM reviews AS r
            INNER JOIN users AS u ON u.id = r.user_id
            WHERE r.product_id = ?
                AND r.status = 1
            ORDER BY r.created_at DESC
        ";

        return $this->query($sql, [$productId]);
    }

    /**
     * Lấy toàn bộ đánh giá để quản trị viên xử lý
     */
    public function adminList(): array
    {
        $sql = "
            SELECT
                r.*,
                u.full_name,
                p.name AS product_name
            FROM reviews AS r
            INNER JOIN users AS u ON u.id = r.user_id
            INNER JOIN products AS p ON p.id = r.product_id
            ORDER BY r.created_at DESC
        ";

        return $this->query($sql);
    }
}
