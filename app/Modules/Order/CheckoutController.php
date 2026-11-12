<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Core\Middleware;
use App\Modules\Coupon\CouponModel;

class CheckoutController extends Controller {

    private OrderModel $orderModel;
    private CouponModel $couponModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->couponModel = new CouponModel();
    }

    /**
     * Giao diện trang Thanh toán (GET /thanh-toan)
     */
    public function index(): void {
        $sessionCart = $_SESSION['cart'] ?? [];
        if (empty($sessionCart)) {
            header('Location: ' . base_url('gio-hang'));
            exit;
        }

        $eval = $this->orderModel->evaluateCart($sessionCart);
        if (empty($eval['items']) || !empty($eval['errors'])) {
            set_flash('error', !empty($eval['errors']) ? implode(' ', $eval['errors']) : 'Giỏ hàng của bạn không có sản phẩm hợp lệ.');
            header('Location: ' . base_url('gio-hang'));
            exit;
        }

        $subtotal = (float)$eval['subtotal'];
        $coupon = null;
        $discountAmount = 0.0;

        if (!empty($_SESSION['coupon']['code'])) {
            $res = $this->couponModel->validateAndCalculate((string)$_SESSION['coupon']['code'], $subtotal);
            if ($res['valid']) {
                $c = $res['coupon'];
                $coupon = [
                    'id'              => (int)$c['id'],
                    'code'            => $c['code'],
                    'discount_type'   => $c['type'],
                    'discount_value'  => (float)$c['discount_value'],
                    'discount_amount' => (float)$res['discount_amount'],
                ];
                $_SESSION['coupon'] = $coupon;
                $discountAmount = (float)$res['discount_amount'];
            } else {
                $oldCode = $_SESSION['coupon']['code'];
                unset($_SESSION['coupon']);
                set_flash('info', "Mã giảm giá {$oldCode} không còn áp dụng được: " . $res['message']);
            }
        }

        $finalTotal = max(0.0, $subtotal - $discountAmount);

        if (empty($_SESSION['checkout_nonce'])) {
            $_SESSION['checkout_nonce'] = bin2hex(random_bytes(16));
        }

        $this->view('client.order.checkout', [
            'title'          => 'Thanh toán đơn hàng — PCMarket',
            'cart'           => $eval['items'],
            'coupon'         => $coupon,
            'subtotal'       => $subtotal,
            'discountAmount' => $discountAmount,
            'finalTotal'     => $finalTotal,
            'user'           => auth_user(),
            'checkoutNonce'  => $_SESSION['checkout_nonce'],
        ]);
    }

    /**
     * Xử lý lưu đơn hàng vào DB (POST /thanh-toan/process)
     */
    public function process(): void {
        Middleware::csrf();

        // Chống gửi lặp biểu mẫu (double-submit)
        $submittedNonce = trim($_POST['checkout_nonce'] ?? '');
        $sessionNonce = $_SESSION['checkout_nonce'] ?? '';
        if ($submittedNonce === '' || $sessionNonce === '' || !hash_equals($sessionNonce, $submittedNonce)) {
            if (!empty($_SESSION['last_order_code']) && empty($_SESSION['cart'])) {
                header('Location: ' . base_url('dat-hang-thanh-cong?code=' . rawurlencode($_SESSION['last_order_code'])));
                exit;
            }
            set_flash('error', 'Phiên thanh toán đã được xử lý hoặc hết hạn. Vui lòng kiểm tra lại giỏ hàng.');
            header('Location: ' . base_url('thanh-toan'));
            exit;
        }

        $sessionCart = $_SESSION['cart'] ?? [];
        if (empty($sessionCart)) {
            header('Location: ' . base_url('gio-hang'));
            exit;
        }

        $name    = trim($_POST['customer_name'] ?? '');
        $phone   = trim($_POST['customer_phone'] ?? '');
        $email   = trim($_POST['customer_email'] ?? '');
        $address = trim($_POST['shipping_address'] ?? '');
        $note    = trim($_POST['note'] ?? '');
        $payment = trim($_POST['payment_method'] ?? 'cod');

        if ($name === '' || $phone === '' || $address === '') {
            set_flash('error', 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ giao hàng!');
            header('Location: ' . base_url('thanh-toan'));
            exit;
        }

        if (!preg_match('/^[0-9\+\s\.\-]{8,15}$/', $phone)) {
            set_flash('error', 'Số điện thoại không hợp lệ (cần 8-15 ký tự số).');
            header('Location: ' . base_url('thanh-toan'));
            exit;
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Địa chỉ email không hợp lệ.');
            header('Location: ' . base_url('thanh-toan'));
            exit;
        }

        if (!in_array($payment, OrderModel::VALID_PAYMENT_METHODS, true)) {
            $payment = 'cod';
        }

        // Luôn tính lại toàn bộ giá & tồn kho trực tiếp từ Database tại thời điểm đặt hàng
        $eval = $this->orderModel->evaluateCart($sessionCart);
        if (empty($eval['items']) || !empty($eval['errors'])) {
            set_flash('error', !empty($eval['errors']) ? implode(' ', $eval['errors']) : 'Không có sản phẩm hợp lệ trong giỏ hàng.');
            header('Location: ' . base_url('gio-hang'));
            exit;
        }

        $subtotal = (float)$eval['subtotal'];
        $couponId = null;
        $couponCode = null;
        $discountAmount = 0.0;

        if (!empty($_SESSION['coupon']['code'])) {
            $couponResult = $this->couponModel->validateAndCalculate((string)$_SESSION['coupon']['code'], $subtotal);
            if (!$couponResult['valid']) {
                unset($_SESSION['coupon']);
                set_flash('error', 'Mã giảm giá đã áp dụng không còn hợp lệ (' . $couponResult['message'] . '). Vui lòng kiểm tra lại đơn hàng trước khi xác nhận.');
                header('Location: ' . base_url('thanh-toan'));
                exit;
            }
            $couponId = (int)$couponResult['coupon']['id'];
            $couponCode = $couponResult['coupon']['code'];
            $discountAmount = (float)$couponResult['discount_amount'];
        }

        $finalTotal = max(0.0, $subtotal - $discountAmount);
        $orderCode = 'PCM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

        $user = auth_user();
        $orderData = [
            'order_code'       => $orderCode,
            'user_id'          => $user['id'] ?? null,
            'customer_name'    => $name,
            'customer_phone'   => $phone,
            'customer_email'   => $email,
            'shipping_address' => $address,
            'note'             => $note,
            'subtotal'         => $subtotal,
            'coupon_id'        => $couponId,
            'coupon_code'      => $couponCode,
            'discount_amount'  => $discountAmount,
            'final_total'      => $finalTotal,
            'payment_method'   => $payment,
            // Dù chọn COD hay chuyển khoản QR, mặc định vẫn là 'unpaid' cho tới khi đối soát
            'payment_status'   => 'unpaid',
        ];

        $orderId = $this->orderModel->createOrder($orderData, $eval['items']);

        if ($orderId) {
            unset($_SESSION['cart'], $_SESSION['coupon'], $_SESSION['checkout_nonce'], $_SESSION['order_error_message']);
            $_SESSION['last_order_code'] = $orderCode;
            header('Location: ' . base_url('dat-hang-thanh-cong?code=' . rawurlencode($orderCode)));
            exit;
        }

        $orderErr = $_SESSION['order_error_message'] ?? 'Không thể lưu đơn hàng. Vui lòng thử lại.';
        unset($_SESSION['order_error_message']);
        set_flash('error', $orderErr);
        header('Location: ' . base_url('thanh-toan'));
        exit;
    }

    /**
     * Trang hoàn tất đặt hàng (GET /dat-hang-thanh-cong)
     */
    public function success(): void {
        $orderCode = trim($_GET['code'] ?? '');
        $order = null;

        if ($orderCode !== '') {
            $order = $this->orderModel->getOrderByCode($orderCode);
        }

        if (!$order) {
            header('Location: ' . base_url());
            exit;
        }

        $user = auth_user();
        $isSessionOrder = !empty($_SESSION['last_order_code']) && hash_equals((string)$_SESSION['last_order_code'], (string)$order['order_code']);
        $isOwner = $user && !empty($order['user_id']) && (int)$order['user_id'] === (int)$user['id'];

        if (!$isSessionOrder && !$isOwner && !is_staff()) {
            set_flash('error', 'Bạn không có quyền xem thông tin đơn hàng này.');
            header('Location: ' . base_url());
            exit;
        }

        $this->view('client.order.success', [
            'title' => 'Đặt hàng thành công — PCMarket',
            'order' => $order,
        ]);
    }
}
