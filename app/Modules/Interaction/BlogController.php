<?php

namespace App\Modules\Interaction;

use App\Core\Controller;
use App\Core\Database;

class BlogController extends Controller

{
    private BlogModel $blogs;

    public function __construct()
    {
        $this->blogs = new BlogModel();
    }

    /**
     * Hiển thị danh sách bài viết công khai
     */
    public function index(): void
    {
        $this->view('client.blog.index', [
            'title' => 'Tin tức công nghệ — PCMarket',
            'blogs' => $this->blogs->published()
        ]);
    }

    /**
     * Hiển thị chi tiết bài viết theo slug
     */
    public function detail(string $slug): void
    {
        $blog = $this->blogs->findPublishedBySlug($slug);

        // Kiểm tra bài viết có tồn tại và được xuất bản không
        if (!$blog) {
            http_response_code(404);

            $this->view('client.errors.404', [
                'title' => 'Không tìm thấy bài viết'
            ]);

            return;
        }

        // Tăng lượt xem bài viết
        $db = Database::getInstance();

        $sql = "
            UPDATE blogs
            SET view_count = view_count + 1
            WHERE id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$blog['id']]);

        // Hiển thị nội dung bài viết
        $this->view('client.blog.detail', [
            'title' => $blog['title'] . ' — PCMarket',
            'blog' => $blog
        ]);
    }
}
