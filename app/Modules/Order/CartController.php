<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Middleware;
use App\Modules\Coupon\CouponModel;
use PDO;

class CartController extends Controller {

    private OrderModel $orderModel;
    private CouponModel $couponModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->couponModel = new CouponModel();
    }

    public function index(): void {
        $sessionCart = $_SESSION['cart'] ?? [];
        $eval = $this->orderModel->evaluateCart($sessionCart);

        // Đồng bộ lại session cart theo dữ liệu sản phẩm hiện hành từ DB
        if (!empty($sessionCart)) {
            $_SESSION['cart'] = array_map(fn($item) => [
                'id'             => $item['id'],
                'name'           => $item['name'],
                'slug'           => $item['slug'],
                'sku'            => $item['sku'],
                'price'          => $item['price'],
                'original_price' => $item['original_price'],
                'thumbnail'      => $item['thumbnail'],
                'quantity'       => $item['quantity'],
            ], $eval['items']);
        }

        if (!empty($eval['errors'])) {
            set_flash('error', implode(' ', $eval['errors']));
        }

        [$coupon, $couponNotice] = $this->syncSessionCoupon((float)$eval['subtotal'], empty($_SESSION['cart']));
        if ($couponNotice !== null) {
            set_flash('info', $couponNotice);
        }

        $this->view('client.order.cart', [
            'title'          => 'Giỏ hàng của bạn — PCMarket',
            'cart'           => $_SESSION['cart'] ?? [],
            'subtotal'       => (float)$eval['subtotal'],
            'appliedCoupon'  => $coupon,
        ]);
    }

    public function add(): void {
        Middleware::csrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ'], 400);
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, slug, sku, price, discount_price, thumbnail, quantity FROM products WHERE id = ? AND status = 1 LIMIT 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $currentInCart = (int)($_SESSION['cart'][$productId]['quantity'] ?? 0);
        $newQuantity = $currentInCart + $quantity;

        if ($newQuantity > (int)$product['quantity']) {
            $this->json([
                'success' => false,
                'message' => "Số lượng tồn kho không đủ (Còn lại: {$product['quantity']} sản phẩm)"
            ], 422);
            return;
        }

        $regularPrice = (float)$product['price'];
        $discountPrice = (float)($product['discount_price'] ?? 0);
        $finalPrice = ($discountPrice > 0 && $discountPrice < $regularPrice) ? $discountPrice : $regularPrice;

        $_SESSION['cart'][$productId] = [
            'id'             => (int)$product['id'],
            'name'           => $product['name'],
            'slug'           => $product['slug'],
            'sku'            => $product['sku'] ?: ('PCM-' . $product['id']),
            'price'          => $finalPrice,
            'original_price' => $regularPrice,
            'thumbnail'      => $product['thumbnail'],
            'quantity'       => $newQuantity
        ];

        $eval = $this->orderModel->evaluateCart($_SESSION['cart']);
        [$coupon] = $this->syncSessionCoupon((float)$eval['subtotal'], false);

        $this->json([
            'success'   => true,
            'message'   => 'Đã thêm sản phẩm vào giỏ hàng',
            'cartCount' => $eval['total_qty'],
            'lineCount' => count($_SESSION['cart']),
            'subtotal'  => $eval['subtotal'],
            'coupon'    => $coupon,
            'item'      => $_SESSION['cart'][$productId]
        ]);
    }

    public function update(): void {
        Middleware::csrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($productId <= 0 || !isset($_SESSION['cart'][$productId])) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng'], 400);
            return;
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
            $this->respondCartMutation('Đã xóa sản phẩm khỏi giỏ hàng');
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, slug, sku, price, discount_price, thumbnail, quantity FROM products WHERE id = ? AND status = 1 LIMIT 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            unset($_SESSION['cart'][$productId]);
            $this->json(['success' => false, 'message' => 'Sản phẩm không còn kinh doanh'], 404);
            return;
        }

        if ($quantity > (int)$product['quantity']) {
            $this->json([
                'success' => false,
                'message' => "Số lượng vượt quá tồn kho khả dụng (Còn lại: {$product['quantity']} sản phẩm)"
            ], 422);
            return;
        }

        $regularPrice = (float)$product['price'];
        $discountPrice = (float)($product['discount_price'] ?? 0);
        $finalPrice = ($discountPrice > 0 && $discountPrice < $regularPrice) ? $discountPrice : $regularPrice;

        $_SESSION['cart'][$productId]['quantity'] = $quantity;
        $_SESSION['cart'][$productId]['price'] = $finalPrice;
        $_SESSION['cart'][$productId]['original_price'] = $regularPrice;
        $_SESSION['cart'][$productId]['sku'] = $product['sku'] ?: ('PCM-' . $product['id']);

        $this->respondCartMutation('Đã cập nhật số lượng sản phẩm');
    }

    public function remove(): void {
        Middleware::csrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        $this->respondCartMutation('Đã xóa sản phẩm khỏi giỏ hàng');
    }

    /**
     * Đồng bộ lại trạng thái mã giảm giá theo tổng tiền giỏ hàng hiện tại.
     *
     * @return array{0:?array, 1:?string}
     */
    private function syncSessionCoupon(float $subtotal, bool $isCartEmpty): array {
        if ($isCartEmpty || empty($_SESSION['coupon']['code'])) {
            unset($_SESSION['coupon']);
            return [null, null];
        }

        $res = $this->couponModel->validateAndCalculate((string)$_SESSION['coupon']['code'], $subtotal);
        if (!$res['valid']) {
            $oldCode = $_SESSION['coupon']['code'];
            unset($_SESSION['coupon']);
            return [null, "Đã gỡ mã giảm giá {$oldCode}: " . $res['message']];
        }

        $c = $res['coupon'];
        $_SESSION['coupon'] = [
            'id'              => (int)$c['id'],
            'code'            => $c['code'],
            'discount_type'   => $c['type'],
            'discount_value'  => (float)$c['discount_value'],
            'discount_amount' => (float)$res['discount_amount'],
        ];

        return [$_SESSION['coupon'], null];
    }

    private function respondCartMutation(string $message): void {
        $cart = $_SESSION['cart'] ?? [];
        $eval = $this->orderModel->evaluateCart($cart);
        [$coupon, $couponNotice] = $this->syncSessionCoupon((float)$eval['subtotal'], empty($cart));

        $discountAmount = (float)($coupon['discount_amount'] ?? 0);
        $finalTotal = max(0.0, (float)$eval['subtotal'] - $discountAmount);

        $this->json([
            'success'        => true,
            'message'        => $message,
            'cart'           => $cart,
            'cartCount'      => $eval['total_qty'],
            'lineCount'      => count($cart),
            'subtotal'       => (float)$eval['subtotal'],
            'coupon'         => $coupon,
            'discountAmount' => $discountAmount,
            'finalTotal'     => $finalTotal,
            'couponNotice'   => $couponNotice,
        ]);
    }
}
