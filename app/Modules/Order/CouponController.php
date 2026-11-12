<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Core\Middleware;
use App\Modules\Coupon\CouponModel;
use Throwable;

class CouponController extends Controller {

    private OrderModel $orderModel;
    private CouponModel $couponModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->couponModel = new CouponModel();
    }

    /**
     * Áp dụng mã giảm giá (POST /api/coupon/apply)
     */
    public function apply(): void {
        Middleware::csrf();

        try {
            $code = trim($_POST['code'] ?? '');
            $cart = $_SESSION['cart'] ?? [];

            if (empty($cart)) {
                $this->json(['success' => false, 'message' => 'Giỏ hàng đang trống, không thể áp dụng mã.'], 422);
                return;
            }

            $eval = $this->orderModel->evaluateCart($cart);
            if (!empty($eval['errors'])) {
                $this->json(['success' => false, 'message' => implode(' ', $eval['errors'])], 422);
                return;
            }

            $subtotal = (float)$eval['subtotal'];
            $result = $this->couponModel->validateAndCalculate($code, $subtotal);

            if (!$result['valid']) {
                $this->json(['success' => false, 'message' => $result['message']], 422);
                return;
            }

            $coupon = $result['coupon'];
            $_SESSION['coupon'] = [
                'id'              => (int)$coupon['id'],
                'code'            => $coupon['code'],
                'discount_type'   => $coupon['type'],
                'discount_value'  => (float)$coupon['discount_value'],
                'discount_amount' => (float)$result['discount_amount'],
            ];

            $this->json([
                'success'        => true,
                'message'        => $result['message'],
                'couponCode'     => $coupon['code'],
                'discountAmount' => (float)$result['discount_amount'],
                'subtotal'       => $subtotal,
                'finalTotal'     => (float)$result['final_total'],
            ]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => 'Không thể kiểm tra mã giảm giá lúc này.'], 500);
        }
    }

    /**
     * Hủy mã giảm giá (POST /api/coupon/remove)
     */
    public function remove(): void {
        Middleware::csrf();

        unset($_SESSION['coupon']);

        $cart = $_SESSION['cart'] ?? [];
        $eval = $this->orderModel->evaluateCart($cart);
        $subtotal = (float)$eval['subtotal'];

        $this->json([
            'success'    => true,
            'message'    => 'Đã hủy mã giảm giá.',
            'subtotal'   => $subtotal,
            'finalTotal' => $subtotal,
        ]);
    }
}
