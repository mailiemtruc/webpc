<?php

namespace App\Modules\Interaction;

use App\Core\Model;

class ReviewModel extends Model

{
    protected string $table = 'reviews';

    // Lấy đánh giá đã được duyệt của một sản phẩm
    public function forProduct(int $productId): array
    {
        return $this->query(
            "SELECT r.*, u.full_name, u.avatar
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = ?
             AND r.status = 1
             ORDER BY r.created_at DESC",
            [$productId]
        );
    }

    // Lấy toàn bộ đánh giá cho Admin
    public function adminList(): array
    {
        return $this->query(
            "SELECT r.*, u.full_name, p.name AS product_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             JOIN products p ON p.id = r.product_id
             ORDER BY r.created_at DESC"
        );
    }
}
