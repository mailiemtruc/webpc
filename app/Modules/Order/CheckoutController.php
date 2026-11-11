<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Modules\Order\OrderModel;

class CheckoutController extends Controller {

    private OrderModel $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    /**
     * Giao diện trang Thanh toán (GET /thanh-toan)
     */
    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . base_url('gio-hang'));
            exit;
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $coupon = $_SESSION['coupon'] ?? null;
        $discountAmount = $coupon['discount_amount'] ?? 0;
        $finalTotal = max(0, $subtotal - $discountAmount);

        $this->view('client.order.checkout', [
            'title'          => 'Thanh toán đơn hàng — PCMarket',
            'cart'           => $cart,
            'coupon'         => $coupon,
            'subtotal'       => $subtotal,
            'discountAmount' => $discountAmount,
            'finalTotal'     => $finalTotal,
            'user'           => $_SESSION['user'] ?? null
        ]);
    }

    /**
     * Xử lý lưu đơn hàng vào DB (POST /thanh-toan/process)
     */
    public function process(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . base_url('gio-hang'));
            exit;
        }

        $name    = trim($_POST['customer_name'] ?? '');
        $phone   = trim($_POST['customer_phone'] ?? '');
        $email   = trim($_POST['customer_email'] ?? '');
        $address = trim($_POST['shipping_address'] ?? '');
        $note    = trim($_POST['note'] ?? '');
        $payment = trim($_POST['payment_method'] ?? 'cod');

        if (empty($name) || empty($phone) || empty($address)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ giao hàng!';
            header('Location: ' . base_url('thanh-toan'));
            exit;
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $coupon = $_SESSION['coupon'] ?? null;
        $discountAmount = $coupon['discount_amount'] ?? 0;
        $finalTotal = max(0, $subtotal - $discountAmount);

        $orderCode = 'PCM-' . date('Ymd') . '-' . rand(100, 999);

        $orderData = [
            'order_code'       => $orderCode,
            'user_id'          => $_SESSION['user']['id'] ?? null,
            'customer_name'    => $name,
            'customer_phone'   => $phone,
            'customer_email'   => $email,
            'shipping_address' => $address,
            'note'             => $note,
            'subtotal'         => $subtotal,
            'coupon_id'        => $coupon['id'] ?? null,
            'coupon_code'      => $coupon['code'] ?? null,
            'discount_amount'  => $discountAmount,
            'final_total'      => $finalTotal,
            'payment_method'   => $payment,
            'payment_status'   => ($payment === 'cod') ? 'unpaid' : 'paid'
        ];

        $orderId = $this->orderModel->createOrder($orderData, $cart);

        if ($orderId) {
            unset($_SESSION['cart'], $_SESSION['coupon']);
            header('Location: ' . base_url('dat-hang-thanh-cong?code=' . $orderCode));
            exit;
        } else {
            // Lấy thông báo lỗi SQL cụ thể đã lưu từ OrderModel
            $sqlErr = $_SESSION['sql_error'] ?? 'Không thể ghi đơn vào Database';
            unset($_SESSION['sql_error']);
            $_SESSION['error'] = 'Lỗi SQL: ' . $sqlErr;
            header('Location: ' . base_url('thanh-toan'));
            exit;
        }
    }

    /**
     * Trang hoàn tất đặt hàng (GET /dat-hang-thanh-cong)
     */
    public function success(): void {
        $orderCode = trim($_GET['code'] ?? '');
        $order = null;

        if (!empty($orderCode)) {
            $order = $this->orderModel->getOrderByCode($orderCode);
        }

        if (!$order) {
            header('Location: ' . base_url('/'));
            exit;
        }

        $this->view('client.order.success', [
            'title' => 'Đặt hàng thành công — PCMarket',
            'order' => $order
        ]);
    }
}