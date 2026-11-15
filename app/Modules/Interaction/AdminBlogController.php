<?php

namespace App\Modules\Interaction;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Middleware;

class AdminBlogController extends Controller
{
    private BlogModel $blogs;

    public function __construct()
    {
        Middleware::admin();
        $this->blogs = new BlogModel();
    }

    /**
     * Hiển thị danh sách bài viết
     */
    public function index(): void
    {
        $this->view('admin.blogs.index', [
            'title' => 'Quản lý tin tức',
            'blogs' => $this->blogs->adminList()
        ]);
    }

    /**
     * Hiển thị form thêm bài viết
     */
    public function create(): void
    {
        $this->view('admin.blogs.form', [
            'title' => 'Thêm bài viết',
            'blog' => null
        ]);
    }

    /**
     * Hiển thị form chỉnh sửa bài viết
     */
    public function edit(int $id): void
    {
        $blog = $this->blogs->find($id);

        if (!$blog) {
            $this->redirectWith(
                'admin/blogs',
                'error',
                'Bài viết không tồn tại.'
            );
        }

        $this->view('admin.blogs.form', [
            'title' => 'Sửa bài viết',
            'blog' => $blog
        ]);
    }

    /**
     * Thêm bài viết mới
     */
    public function store(): void
    {
        $this->save(null);
    }

    /**
     * Cập nhật bài viết
     */
    public function update(int $id): void
    {
        $this->save($id);
    }

    /**
     * Lưu dữ liệu bài viết
     */
    private function save(?int $id): void
    {
        Middleware::csrf();

        $title = trim((string) ($_POST['title'] ?? ''));
        $summary = trim((string) ($_POST['summary'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));

        $status = isset($_POST['status']) ? 1 : 0;

        if ($title === '' || $content === '') {
            $this->redirectWith(
                'admin/blogs',
                'error',
                'Tiêu đề và nội dung không được để trống.'
            );
        }

        $old = $id ? $this->blogs->find($id) : null;

        if ($id && !$old) {
            $this->redirectWith(
                'admin/blogs',
                'error',
                'Bài viết không tồn tại.'
            );
        }

        // Giữ ảnh cũ nếu không tải ảnh mới
        $thumbnail = $old['thumbnail'] ?? '';

        if (
            isset($_FILES['thumbnail']) &&
            ($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_NO_FILE
        ) {
            $upload = upload_file(
                $_FILES['thumbnail'],
                'blogs',
                ['jpg', 'jpeg', 'png', 'webp'],
                5242880
            );

            if (!$upload) {
                $this->redirectWith(
                    'admin/blogs',
                    'error',
                    'Ảnh không hợp lệ (JPG, PNG, WEBP tối đa 5MB).'
                );
            }

            $thumbnail = $upload;
        }

        if ($thumbnail === '') {
            $this->redirectWith(
                'admin/blogs',
                'error',
                'Vui lòng chọn ảnh đại diện.'
            );
        }

        // Tạo slug cho bài viết
        $slugInput = trim((string) ($_POST['slug'] ?? ''));

        $slug = $this->slugify(
            $slugInput !== '' ? $slugInput : $title
        );

        $slug = $this->blogs->uniqueSlug($slug, $id);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'thumbnail' => $thumbnail,
            'summary' => $summary ?: null,
            'content' => $this->sanitizeContent($content),
            'status' => $status
        ];

        $db = Database::getInstance();

        if ($id) {
            // Cập nhật bài viết
            $sets = [];
            $values = [];

            foreach ($data as $key => $value) {
                $sets[] = "`$key` = ?";
                $values[] = $value;
            }

            $values[] = $id;

            $sql = "
                UPDATE blogs
                SET " . implode(', ', $sets) . ",
                    updated_at = NOW()
                WHERE id = ?
            ";

            $db->prepare($sql)->execute($values);
        } else {
            // Thêm bài viết mới
            $data['author_id'] = Auth::id();

            $this->blogs->create($data);
        }

        $this->redirectWith(
            'admin/blogs',
            'success',
            'Đã lưu bài viết.'
        );
    }

    /**
     * Xóa bài viết
     */
    public function delete(int $id): void
    {
        Middleware::csrf();

        $this->blogs->delete($id);

        $this->redirectWith(
            'admin/blogs',
            'success',
            'Đã xóa bài viết.'
        );
    }

    /**
     * Chuyển tiêu đề thành slug
     */
    private function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');

        $value = iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $value
        ) ?: $value;

        $value = preg_replace('/[^a-z0-9]+/', '-', $value);

        return trim($value, '-') ?: 'bai-viet';
    }

    /**
     * Lọc HTML để hạn chế XSS
     */
    private function sanitizeContent(string $html): string
    {
        $allowedTags = '<p><br><h2><h3><h4><strong><b><em><i><ul><ol><li><blockquote><a>';

        // Loại bỏ các thẻ HTML không được phép
        $html = strip_tags($html, $allowedTags);

        // Chỉ giữ lại các thẻ được cho phép và xử lý thuộc tính
        return preg_replace_callback(
            '/<\s*(\/?)\s*([a-z0-9]+)\b([^>]*)>/i',
            function ($matches) {
                $closing = $matches[1] === '/';
                $tag = strtolower($matches[2]);
                $attributes = $matches[3];

                if ($closing) {
                    return '</' . $tag . '>';
                }

                if ($tag === 'br') {
                    return '<br>';
                }

                if ($tag === 'a') {
                    if (
                        preg_match(
                            '~\bhref\s*=\s*(["\'])(https?://[^"\']+)\1~i',
                            $attributes,
                            $url
                        )
                    ) {
                        $href = htmlspecialchars(
                            $url[2],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        return '<a href="' . $href . '" rel="nofollow noopener" target="_blank">';
                    }
                }

                return '<' . $tag . '>';
            },
            $html
        ) ?? '';
    }
}
