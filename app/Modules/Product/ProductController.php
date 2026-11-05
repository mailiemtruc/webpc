<?php
namespace App\Modules\Product;

use App\Core\Controller;

class ProductController extends Controller {
    private ProductModel $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function home(): void {
        $featuredProducts = $this->productModel->getFeaturedProducts(8);
        $gamingPCs = $this->productModel->getHotGamingPCs(4);
        $components = $this->productModel->getHotComponents(8);

        $this->view('client.home.index', [
            'title' => 'PCMART.VN - Máy Tính Gaming, PC Đồ Họa & Linh Kiện Chính Hãng',
            'featuredProducts' => $featuredProducts,
            'gamingPCs' => $gamingPCs,
            'components' => $components,
        ]);
    }

    public function index(): void {
        $products = $this->productModel->all();
        $this->view('client.product.list', [
            'title' => 'Danh sách sản phẩm - PCMART',
            'products' => $products,
        ]);
    }

    public function detail(string $slug): void {
        $product = $this->productModel->findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->view('client.errors.404', ['title' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $this->view('client.product.detail', [
            'title' => $product['name'] . ' - PCMART',
            'product' => $product,
        ]);
    }

    public function liveSearch(): void {
        $q = $_GET['q'] ?? '';
        if (strlen($q) < 2) {
            $this->json([]);
            return;
        }

        $sql = "SELECT id, name, slug, price, discount_price, thumbnail FROM products WHERE status = 1 AND name LIKE ? LIMIT 6";
        $results = $this->productModel->query($sql, ["%{$q}%"]);
        $this->json($results);
    }
}

