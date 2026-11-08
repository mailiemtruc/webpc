<?php
namespace App\Modules\Product;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;
use PDO;

class AdminProductController extends Controller {

    public function __construct() {
        Middleware::admin();
    }

    /**
     * Danh sách sản phẩm dành cho Admin
     */
    public function index(): void {
        $db = Database::getInstance();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $binds = [];

        // Lọc danh mục
        if (!empty($_GET['category_id'])) {
            $where[] = "p.category_id = ?";
            $binds[] = (int)$_GET['category_id'];
        }

        // Lọc thương hiệu
        if (!empty($_GET['brand_id'])) {
            $where[] = "p.brand_id = ?";
            $binds[] = (int)$_GET['brand_id'];
        }

        // Lọc trạng thái
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $where[] = "p.status = ?";
            $binds[] = (int)$_GET['status'];
        }

        // Tìm kiếm
        if (!empty($_GET['q'])) {
            $q = trim($_GET['q']);
            $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
            $binds[] = "%{$q}%";
            $binds[] = "%{$q}%";
        }

        $whereSql = implode(' AND ', $where);

        // Đếm tổng số
        $countStmt = $db->prepare("SELECT COUNT(*) FROM products p WHERE {$whereSql}");
        $countStmt->execute($binds);
        $total = (int)$countStmt->fetchColumn();

        // Lấy dữ liệu
        $dataStmt = $db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE {$whereSql} 
            ORDER BY p.id DESC 
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $dataStmt->execute($binds);
        $products = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy tất cả danh mục và thương hiệu cho dropdown bộ lọc
        $categories = $db->query("SELECT id, name FROM categories ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $brands = $db->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $totalPages = max(1, (int)ceil($total / $perPage));

        $this->view('admin.product.index', [
            'title' => 'Quản lý Sản phẩm — PCMarket Admin',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'params' => $_GET
        ]);
    }

    /**
     * Form tạo sản phẩm mới
     */
    public function create(): void {
        $db = Database::getInstance();
        $categories = $db->query("SELECT id, name FROM categories ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $brands = $db->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin.product.create', [
            'title' => 'Thêm Sản phẩm Mới — PCMarket Admin',
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    /**
     * Lưu sản phẩm mới vào database
     */
    public function store(): void {
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $brandId = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
        $sku = trim($_POST['sku'] ?? '');
        $price = (float)str_replace(['.', ','], '', $_POST['price'] ?? '0');
        $discountPrice = !empty($_POST['discount_price']) ? (float)str_replace(['.', ','], '', $_POST['discount_price']) : null;
        $quantity = max(0, (int)($_POST['quantity'] ?? 10));
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $isHot = isset($_POST['is_hot']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $categoryId <= 0 || $price <= 0) {
            set_flash('error', 'Vui lòng điền đầy đủ: Tên sản phẩm, Danh mục và Giá bán!');
            header('Location: ' . base_url('admin/products/create'));
            exit;
        }

        // Tự sinh SKU nếu chưa có
        if ($sku === '') {
            $sku = 'PCM-' . strtoupper(substr(md5(uniqid()), 0, 8));
        }

        // Tự sinh slug
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        // Xử lý ảnh thumbnail (Upload file ưu tiên hơn dán link)
        $thumbnail = trim($_POST['thumbnail_url'] ?? 'assets/images/product-placeholder.svg');
        if (!empty($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = upload_file($_FILES['thumbnail_file'], 'products');
            if ($uploadedPath) {
                $thumbnail = $uploadedPath;
            }
        }

        // Đóng gói thông số kỹ thuật chuẩn hóa để người dùng lọc được ở Client
        $specs = [
            'cpu'       => trim($_POST['spec_cpu'] ?? ''),
            'ram'       => trim($_POST['spec_ram'] ?? ''),
            'vga'       => trim($_POST['spec_vga'] ?? ''),
            'storage'   => trim($_POST['spec_storage'] ?? ''),
            'mainboard' => trim($_POST['spec_mainboard'] ?? ''),
            'psu'       => trim($_POST['spec_psu'] ?? ''),
            'case'      => trim($_POST['spec_case'] ?? ''),
            'cooling'   => trim($_POST['spec_cooling'] ?? ''),
        ];
        $specsJson = json_encode(array_filter($specs), JSON_UNESCAPED_UNICODE);

        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO products (
                category_id, brand_id, name, slug, sku, price, discount_price, 
                quantity, thumbnail, summary, description, specifications, 
                is_featured, is_hot, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        try {
            $stmt->execute([
                $categoryId, $brandId, $name, $slug, $sku, $price, $discountPrice,
                $quantity, $thumbnail, $summary, $description, $specsJson,
                $isFeatured, $isHot, $status
            ]);
            set_flash('success', "Đã thêm sản phẩm '{$name}' thành công!");
        } catch (\PDOException $e) {
            set_flash('error', 'Lỗi lưu sản phẩm: ' . $e->getMessage());
        }

        header('Location: ' . base_url('admin/products'));
        exit;
    }

    /**
     * Form chỉnh sửa sản phẩm
     */
    public function edit(int|string $id): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            set_flash('error', 'Không tìm thấy sản phẩm cần chỉnh sửa!');
            header('Location: ' . base_url('admin/products'));
            exit;
        }

        $categories = $db->query("SELECT id, name FROM categories ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $brands = $db->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $specs = json_decode($product['specifications'] ?? '{}', true) ?: [];

        $this->view('admin.product.edit', [
            'title' => 'Chỉnh sửa Sản phẩm #' . $product['id'] . ' — PCMarket Admin',
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'specs' => $specs
        ]);
    }

    /**
     * Cập nhật thông tin sản phẩm
     */
    public function update(int|string $id): void {
        $id = (int)$id;
        $db = Database::getInstance();

        $stmtExist = $db->prepare("SELECT thumbnail FROM products WHERE id = ? LIMIT 1");
        $stmtExist->execute([$id]);
        $existing = $stmtExist->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            set_flash('error', 'Sản phẩm không tồn tại!');
            header('Location: ' . base_url('admin/products'));
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $brandId = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
        $sku = trim($_POST['sku'] ?? '');
        $price = (float)str_replace(['.', ','], '', $_POST['price'] ?? '0');
        $discountPrice = !empty($_POST['discount_price']) ? (float)str_replace(['.', ','], '', $_POST['discount_price']) : null;
        $quantity = max(0, (int)($_POST['quantity'] ?? 10));
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $isHot = isset($_POST['is_hot']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Xử lý ảnh (Upload mới hoặc giữ nguyên link/ảnh cũ)
        $thumbnail = $existing['thumbnail'];
        if (!empty($_POST['thumbnail_url'])) {
            $thumbnail = trim($_POST['thumbnail_url']);
        }
        if (!empty($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = upload_file($_FILES['thumbnail_file'], 'products');
            if ($uploadedPath) {
                $thumbnail = $uploadedPath;
            }
        }

        // Đóng gói specifications JSON chuẩn
        $specs = [
            'cpu'       => trim($_POST['spec_cpu'] ?? ''),
            'ram'       => trim($_POST['spec_ram'] ?? ''),
            'vga'       => trim($_POST['spec_vga'] ?? ''),
            'storage'   => trim($_POST['spec_storage'] ?? ''),
            'mainboard' => trim($_POST['spec_mainboard'] ?? ''),
            'psu'       => trim($_POST['spec_psu'] ?? ''),
            'case'      => trim($_POST['spec_case'] ?? ''),
            'cooling'   => trim($_POST['spec_cooling'] ?? ''),
        ];
        $specsJson = json_encode(array_filter($specs), JSON_UNESCAPED_UNICODE);

        $stmt = $db->prepare("
            UPDATE products SET 
                category_id = ?, brand_id = ?, name = ?, sku = ?, price = ?, 
                discount_price = ?, quantity = ?, thumbnail = ?, summary = ?, 
                description = ?, specifications = ?, is_featured = ?, is_hot = ?, 
                status = ? 
            WHERE id = ?
        ");

        try {
            $stmt->execute([
                $categoryId, $brandId, $name, $sku, $price, $discountPrice,
                $quantity, $thumbnail, $summary, $description, $specsJson,
                $isFeatured, $isHot, $status, $id
            ]);
            set_flash('success', "Đã cập nhật sản phẩm #{$id} thành công!");
        } catch (\PDOException $e) {
            set_flash('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }

        header('Location: ' . base_url('admin/products'));
        exit;
    }

    /**
     * Xóa sản phẩm
     */
    public function delete(int|string $id): void {
        $id = (int)$id;
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);

        set_flash('success', "Đã xóa sản phẩm #{$id} khỏi hệ thống!");
        header('Location: ' . base_url('admin/products'));
        exit;
    }
}
