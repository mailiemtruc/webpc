<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class CartController extends Controller {
    
    public function index(): void {
        $cart = $_SESSION['cart'] ?? [];
        $this->view('client.order.cart', [
            'title' => 'Giỏ hàng của bạn — PCMarket',
            'cart' => $cart
        ]);
    }

    public function add(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ'], 400);
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, slug, price, discount_price, thumbnail, quantity FROM products WHERE id = ? AND status = 1 LIMIT 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        //Kiểm tra tồn kho tại Server trước khi thêm vào giỏ
        
        $currentInCart = $_SESSION['cart'][$productId]['quantity'] ?? 0;
        $newQuantity = $currentInCart + $quantity;

        if ($newQuantity > (int)$product['quantity']) {
            $this->json([
                'success' => false, 
                'message' => "Số lượng tồn kho không đủ (Còn lại: {$product['quantity']} sản phẩm)"
            ], 422);
            return;
        }

        $finalPrice = !empty($product['discount_price']) ? (float)$product['discount_price'] : (float)$product['price'];

        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'price' => $finalPrice,
            'original_price' => (float)$product['price'],
            'thumbnail' => $product['thumbnail'],
            'quantity' => $newQuantity
        ];

        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['quantity'];
        }

        $this->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng',
            'cartCount' => $totalItems,
            'item' => $_SESSION['cart'][$productId]
        ]);
    }

    public function update(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($productId <= 0 || !isset($_SESSION['cart'][$productId])) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng'], 400);
            return;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            $this->json(['success' => true, 'cart' => $_SESSION['cart'] ?? []]);
            return;
        }

        // BỔ SUNG: Truy vấn DB kiểm tra tồn kho tại Server khi sửa số lượng
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT quantity FROM products WHERE id = ? AND status = 1 LIMIT 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }

        if ($quantity > (int)$product['quantity']) {
            $this->json([
                'success' => false, 
                'message' => "Số lượng vượt quá tồn kho khả dụng (Còn lại: {$product['quantity']} sản phẩm)"
            ], 422);
            return;
        }

        $_SESSION['cart'][$productId]['quantity'] = $quantity;

        $this->json(['success' => true, 'cart' => $_SESSION['cart'] ?? []]);
    }

    public function remove(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        $this->json(['success' => true, 'cart' => $_SESSION['cart'] ?? []]);
    }
}