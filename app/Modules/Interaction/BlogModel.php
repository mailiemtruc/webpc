<?php
namespace App\Modules\Interaction;

use App\Core\Model;

class BlogModel extends Model


{
    protected string $table = 'blogs';

    /**
     * Lấy danh sách bài viết đã xuất bản
     */
    public function published(int $limit = 30): array
    {
        // Giới hạn số lượng bài viết từ 1 đến 100
        $limit = max(1, min(100, $limit));

        $sql = "
            SELECT
                b.*,
                u.full_name AS author_name
            FROM blogs AS b
            INNER JOIN users AS u ON u.id = b.author_id
            WHERE b.status = 1
            ORDER BY b.created_at DESC
            LIMIT {$limit}
        ";

        return $this->query($sql);
    }

    /**
     * Tìm bài viết đã xuất bản theo slug
     */
    public function findPublishedBySlug(string $slug): ?array
    {
        $sql = "
            SELECT
                b.*,
                u.full_name AS author_name
            FROM blogs AS b
            INNER JOIN users AS u ON u.id = b.author_id
            WHERE b.slug = ?
                AND b.status = 1
            LIMIT 1
        ";

        return $this->queryOne($sql, [$slug]);
    }

    /**
     * Lấy toàn bộ bài viết dành cho Admin
     */
    public function adminList(): array
    {
        $sql = "
            SELECT
                b.*,
                u.full_name AS author_name
            FROM blogs AS b
            INNER JOIN users AS u ON u.id = b.author_id
            ORDER BY b.created_at DESC
        ";

        return $this->query($sql);
    }

    /**
     * Tạo slug không bị trùng lặp
     */
    public function uniqueSlug(
        string $slug,
        ?int $ignoreId = null
    ): string {
        $base = trim($slug, '-');

        if ($base === '') {
            $base = 'bai-viet';
        }

        $candidate = $base;
        $i = 2;

        while (true) {
            $sql = "
                SELECT id
                FROM blogs
                WHERE slug = ?
            ";

            $params = [$candidate];

            // Bỏ qua ID bài viết đang chỉnh sửa
            if ($ignoreId) {
                $sql .= " AND id <> ?";
                $params[] = $ignoreId;
            }

            $sql .= " LIMIT 1";

            if (!$this->queryOne($sql, $params)) {
                return $candidate;
            }

            $candidate = $base . '-' . $i;
            $i++;
        }
    }
}
