<?php
namespace App\Modules\Product;

use App\Core\Controller;

class ProductController extends Controller {
    private ProductModel $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function home(): void {
        $hotSaleGaming = $this->productModel->getHotSaleGaming(6);
        $hotSaleComponents = $this->productModel->getHotSaleComponents(6);
        $gamingPCs = $this->productModel->getGamingPCs(6);
        $workstationPCs = $this->productModel->getWorkstationPCs(6);
        $amdGamingPCs = $this->productModel->getAmdGamingPCs(6);
        $officePCs = $this->productModel->getOfficePCs(6);
        $monitors = $this->productModel->getMonitors(6);
        $aiPCs = $this->productModel->getAiPCs(6);
        $components = $this->productModel->getComponents(6);

        $this->view('client.home.index', [
            'title' => 'PCMarket — Siêu Thị Máy Tính Gaming, Workstation & Linh Kiện Chính Hãng',
            'hotSaleGaming' => $hotSaleGaming,
            'hotSaleComponents' => $hotSaleComponents,
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
        $products = $this->productModel->all();
        $this->view('client.product.list', [
            'title' => 'Danh sách sản phẩm - PCMarket',
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
            'title' => $product['name'] . ' - PCMarket',
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
