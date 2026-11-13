<?php
namespace App\Modules\Product;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;
use App\Core\Request;
use PDO;

class CategoryController extends Controller {

    public function __construct() {
        Middleware::admin();
    }

    /**
     * Danh sách danh mục sản phẩm
     */
    public function index(): void {
        $db = Database::getInstance();

        $sql = "SELECT c.*, p.name AS parent_name, COUNT(pr.id) AS product_count 
                FROM categories c 
                LEFT JOIN categories p ON c.parent_id = p.id 
                LEFT JOIN products pr ON pr.category_id = c.id 
                GROUP BY c.id 
                ORDER BY c.display_order ASC, c.id ASC";
        $stmt = $db->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.category.index', [
            'title' => 'Quản lý Danh mục sản phẩm - PCMarket Admin',
            'categories' => $categories
        ]);
    }

    /**
     * Form tạo mới danh mục
     */
    public function create(): void {
        $db = Database::getInstance();
        $parentStmt = $db->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
        $parents = $parentStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.category.form', [
            'title' => 'Thêm danh mục mới - PCMarket Admin',
            'category' => null,
            'parents' => $parents
        ]);
    }

    /**
     * Lưu danh mục mới
     */
    public function store(): void {
        $name = trim((string)Request::post('name', ''));
        $slug = trim((string)Request::post('slug', ''));
        $parentId = Request::post('parent_id') ? (int)Request::post('parent_id') : null;
        $icon = trim((string)Request::post('icon', ''));
        $displayOrder = (int)Request::post('display_order', 0);
        $status = (int)Request::post('status', 1);

        if ($name === '') {
            $this->redirectWith('/admin/categories/create', 'error', 'Tên danh mục không được để trống!');
        }

        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }

        $db = Database::getInstance();

        // Kiểm tra trùng slug
        $chk = $db->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
        $chk->execute([$slug]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare(
            "INSERT INTO categories (name, slug, parent_id, icon, display_order, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$name, $slug, $parentId, $icon, $displayOrder, $status]);

        $this->redirectWith('/admin/categories', 'success', 'Thêm danh mục "' . htmlspecialchars($name) . '" thành công!');
    }

    /**
     * Form chỉnh sửa danh mục
     */
    public function edit(int $id): void {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            $this->redirectWith('/admin/categories', 'error', 'Không tìm thấy danh mục!');
        }

        $parentStmt = $db->prepare("SELECT id, name FROM categories WHERE parent_id IS NULL AND id != ? ORDER BY name ASC");
        $parentStmt->execute([$id]);
        $parents = $parentStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.category.form', [
            'title' => 'Chỉnh sửa danh mục: ' . $category['name'],
            'category' => $category,
            'parents' => $parents
        ]);
    }

    /**
     * Cập nhật danh mục
     */
    public function update(int $id): void {
        $name = trim((string)Request::post('name', ''));
        $slug = trim((string)Request::post('slug', ''));
        $parentId = Request::post('parent_id') ? (int)Request::post('parent_id') : null;
        $icon = trim((string)Request::post('icon', ''));
        $displayOrder = (int)Request::post('display_order', 0);
        $status = (int)Request::post('status', 1);

        if ($name === '') {
            $this->redirectWith("/admin/categories/edit/{$id}", 'error', 'Tên danh mục không được để trống!');
        }

        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }

        $db = Database::getInstance();

        // Kiểm tra trùng slug với danh mục khác
        $chk = $db->prepare("SELECT id FROM categories WHERE slug = ? AND id != ? LIMIT 1");
        $chk->execute([$slug, $id]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare(
            "UPDATE categories SET name = ?, slug = ?, parent_id = ?, icon = ?, display_order = ?, status = ? WHERE id = ?"
        );
        $stmt->execute([$name, $slug, $parentId, $icon, $displayOrder, $status, $id]);

        $this->redirectWith('/admin/categories', 'success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Xóa danh mục
     */
    public function delete(int $id): void {
        $db = Database::getInstance();

        // Kiểm tra nếu danh mục đang có sản phẩm
        $chkProd = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $chkProd->execute([$id]);
        if ($chkProd->fetchColumn() > 0) {
            $this->redirectWith('/admin/categories', 'error', 'Không thể xóa danh mục đang có sản phẩm gán vào!');
        }

        // Kiểm tra nếu danh mục có danh mục con
        $chkChild = $db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $chkChild->execute([$id]);
        if ($chkChild->fetchColumn() > 0) {
            $this->redirectWith('/admin/categories', 'error', 'Không thể xóa danh mục đang có danh mục con trực thuộc!');
        }

        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        $this->redirectWith('/admin/categories', 'success', 'Xóa danh mục thành công!');
    }
}

