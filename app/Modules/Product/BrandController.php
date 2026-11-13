<?php
namespace App\Modules\Product;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;
use App\Core\Request;
use PDO;

class BrandController extends Controller {

    public function __construct() {
        Middleware::admin();
    }

    /**
     * Danh sách thương hiệu
     */
    public function index(): void {
        $db = Database::getInstance();

        $sql = "SELECT b.*, COUNT(p.id) AS product_count 
                FROM brands b 
                LEFT JOIN products p ON p.brand_id = b.id 
                GROUP BY b.id 
                ORDER BY b.name ASC";
        $stmt = $db->query($sql);
        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.brand.index', [
            'title' => 'Quản lý Thương hiệu linh kiện - PCMarket Admin',
            'brands' => $brands
        ]);
    }

    /**
     * Form thêm thương hiệu
     */
    public function create(): void {
        $this->view('admin.brand.form', [
            'title' => 'Thêm thương hiệu mới - PCMarket Admin',
            'brand' => null
        ]);
    }

    /**
     * Lưu thương hiệu mới
     */
    public function store(): void {
        $name = trim((string)Request::post('name', ''));
        $slug = trim((string)Request::post('slug', ''));
        $logo = trim((string)Request::post('logo', ''));
        $status = (int)Request::post('status', 1);

        if ($name === '') {
            $this->redirectWith('/admin/brands/create', 'error', 'Tên thương hiệu không được để trống!');
        }

        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }

        $db = Database::getInstance();

        // Kiểm tra trùng slug
        $chk = $db->prepare("SELECT id FROM brands WHERE slug = ? LIMIT 1");
        $chk->execute([$slug]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare(
            "INSERT INTO brands (name, slug, logo, status, created_at) VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$name, $slug, $logo, $status]);

        $this->redirectWith('/admin/brands', 'success', 'Thêm thương hiệu "' . htmlspecialchars($name) . '" thành công!');
    }

    /**
     * Form chỉnh sửa thương hiệu
     */
    public function edit(int $id): void {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM brands WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $brand = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$brand) {
            $this->redirectWith('/admin/brands', 'error', 'Không tìm thấy thương hiệu!');
        }

        $this->view('admin.brand.form', [
            'title' => 'Chỉnh sửa thương hiệu: ' . $brand['name'],
            'brand' => $brand
        ]);
    }

    /**
     * Cập nhật thương hiệu
     */
    public function update(int $id): void {
        $name = trim((string)Request::post('name', ''));
        $slug = trim((string)Request::post('slug', ''));
        $logo = trim((string)Request::post('logo', ''));
        $status = (int)Request::post('status', 1);

        if ($name === '') {
            $this->redirectWith("/admin/brands/edit/{$id}", 'error', 'Tên thương hiệu không được để trống!');
        }

        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }

        $db = Database::getInstance();

        $chk = $db->prepare("SELECT id FROM brands WHERE slug = ? AND id != ? LIMIT 1");
        $chk->execute([$slug, $id]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare("UPDATE brands SET name = ?, slug = ?, logo = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $logo, $status, $id]);

        $this->redirectWith('/admin/brands', 'success', 'Cập nhật thương hiệu thành công!');
    }

    /**
     * Xóa thương hiệu
     */
    public function delete(int $id): void {
        $db = Database::getInstance();

        $chkProd = $db->prepare("SELECT COUNT(*) FROM products WHERE brand_id = ?");
        $chkProd->execute([$id]);
        if ($chkProd->fetchColumn() > 0) {
            $this->redirectWith('/admin/brands', 'error', 'Không thể xóa thương hiệu đang có sản phẩm gán vào!');
        }

        $stmt = $db->prepare("DELETE FROM brands WHERE id = ?");
        $stmt->execute([$id]);

        $this->redirectWith('/admin/brands', 'success', 'Xóa thương hiệu thành công!');
    }
}
