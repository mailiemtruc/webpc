<?php
namespace App\Modules\Product;

use App\Core\Controller;
use App\Core\Database;
use App\Modules\Interaction\ReviewModel;
use PDO;

class ProductController extends Controller {
    private ProductModel $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function home(): void {
        $hotSaleGaming = $this->productModel->getHotSaleGaming(10);
        $hotSaleComponents = $this->productModel->getHotSaleComponents(10);
        $featuredProducts = $this->productModel->getFeaturedProducts(10);
        $gamingPCs = $this->productModel->getGamingPCs(10);
        $workstationPCs = $this->productModel->getWorkstationPCs(10);
        $amdGamingPCs = $this->productModel->getAmdGamingPCs(10);
        $officePCs = $this->productModel->getOfficePCs(10);
        $monitors = $this->productModel->getMonitors(10);
        $aiPCs = $this->productModel->getAiPCs(10);
        $components = $this->productModel->getComponents(10);

        $heroBanners = [];
        try {
            $db = Database::getInstance();
            $heroBanners = $db->query("SELECT title, image, link FROM banners WHERE position = 'home_main' AND status = 1 ORDER BY display_order ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            $heroBanners = [];
        }

        $this->view('client.home.index', [
            'title' => 'PCMarket — Siêu Thị Máy Tính Gaming, Workstation & Linh Kiện Chính Hãng',
            'heroBanners' => $heroBanners,
            'hotSaleGaming' => $hotSaleGaming,
            'hotSaleComponents' => $hotSaleComponents,
            'featuredProducts' => $featuredProducts,
            'gamingPCs' => $gamingPCs,
            'workstationPCs' => $workstationPCs,
            'amdGamingPCs' => $amdGamingPCs,
            'officePCs' => $officePCs,
            'monitors' => $monitors,
            'aiPCs' => $aiPCs,
            'components' => $components,
        ]);
    }

    public function index(): void {
        $categorySlug = trim($_GET['danh-muc'] ?? '');
        $result = $this->productModel->filterCatalog($_GET, 15);

        $titles = [
            'pc-gaming' => 'PC Gaming & Streaming',
            'pc-workstation' => 'PC Workstation, Đồ Họa & Render',
            'pc-amd-gaming' => 'PC AMD Gaming',
            'pc-van-phong' => 'PC Văn Phòng & Doanh Nghiệp',
            'man-hinh-may-tinh' => 'Màn Hình Máy Tính',
            'pc-ai-tri-tue-nhan-tao' => 'PC AI - Trí Tuệ Nhân Tạo',
            'linh-kien-may-tinh' => 'Linh Kiện Máy Tính Chính Hãng',
            'cpu-vi-xu-ly' => 'Vi Xử Lý (CPU)',
            'vga-card-man-hinh' => 'Card Màn Hình (VGA)',
            'mainboard-bo-mach-chu' => 'Bo Mạch Chủ (Mainboard)',
            'ram-bo-nho-trong' => 'Bộ Nhớ Trong (RAM)',
            'o-cung-ssd-hdd' => 'Ổ Cứng SSD/HDD',
        ];

        $pageTitle = $titles[$categorySlug] ?? 'Tất cả sản phẩm';
        if (!empty($_GET['q'])) {
            $pageTitle = 'Tìm kiếm: ' . htmlspecialchars($_GET['q']);
        }

        $this->view('client.product.list', [
            'title' => $pageTitle . ' — PCMarket',
            'categorySlug' => $categorySlug,
            'pageTitle' => $pageTitle,
            'products' => $result['products'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['perPage'],
            'totalPages' => $result['totalPages'],
            'facets' => $result['facets'],
            'params' => $_GET
        ]);
    }

    public function detail(string $slug): void {
        $product = $this->productModel->findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->view('client.errors.404', ['title' => 'Không tìm thấy sản phẩm']);
            return;
        }

        // Tăng lượt xem sản phẩm phục vụ thống kê Dashboard
        try {
            $db = Database::getInstance();
            $db->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = ?")->execute([(int)$product['id']]);
        } catch (\Throwable $e) {
            // Không chặn hiển thị sản phẩm nếu ghi nhận view_count lỗi
        }

        $reviews = [];
        try {
            $reviews = (new ReviewModel())->forProduct((int)$product['id']);
        } catch (\Throwable $e) {
            $reviews = [];
        }

        $this->view('client.product.detail', [
            'title' => $product['name'] . ' - PCMarket',
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }

    public function liveSearch(): void {
        $q = trim((string)($_GET['q'] ?? ''));
        if (mb_strlen($q, 'UTF-8') < 2) {
            $this->json([]);
            return;
        }

        $sql = "SELECT id, name, slug, price, discount_price, thumbnail FROM products WHERE status = 1 AND name LIKE ? ORDER BY id DESC LIMIT 6";
        $results = $this->productModel->query($sql, ["%{$q}%"]);

        $formatted = array_map(function($p) {
            $regPrice = (float)($p['price'] ?? 0);
            $salePrice = (float)($p['discount_price'] ?? 0);
            $hasSale = $salePrice > 0 && $salePrice < $regPrice;
            $currentPrice = $hasSale ? $salePrice : $regPrice;
            return [
                'id' => (int)$p['id'],
                'name' => $p['name'],
                'slug' => $p['slug'],
                'url' => base_url('san-pham/' . rawurlencode($p['slug'])),
                'price_formatted' => number_format($currentPrice, 0, ',', '.') . ' VNĐ',
                'old_price_formatted' => $hasSale ? number_format($regPrice, 0, ',', '.') . ' VNĐ' : null,
                'thumbnail' => product_image_url($p['thumbnail'] ?? ''),
            ];
        }, $results);

        $this->json($formatted);
    }
}
