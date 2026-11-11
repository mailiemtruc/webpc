<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Modules\Order\OrderModel;

class AdminOrderController extends Controller {

    private OrderModel $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    /**
     * Danh sách đơn hàng trong Admin (GET /admin/orders)
     */
    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $orders = $this->orderModel->getAllOrders(100, 0);

        $this->view('admin.order.index', [
            'title'  => 'Quản lý đơn hàng — Admin PCMarket',
            'orders' => $orders
        ]);
    }

    /**
     * Chi tiết đơn hàng (GET /admin/orders/{id})
     */
    public function detail(int $id): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            $_SESSION['admin_error'] = 'Đơn hàng không tồn tại!';
            header('Location: ' . base_url('admin/orders'));
            exit;
        }

        $this->view('admin.order.detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['order_code'],
            'order' => $order
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng & thanh toán (POST /admin/orders/{id}/status)
     */
    public function updateStatus(int $id): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $status        = trim($_POST['status'] ?? 'pending');
        $paymentStatus = trim($_POST['payment_status'] ?? 'unpaid');

        $result = $this->orderModel->updateStatus($id, $status, $paymentStatus);

        if ($result) {
            $_SESSION['admin_success'] = 'Cập nhật trạng thái đơn hàng thành công!';
        } else {
            $sqlErr = $_SESSION['admin_sql_error'] ?? 'Cập nhật thất bại';
            unset($_SESSION['admin_sql_error']);
            $_SESSION['admin_error'] = 'Lỗi cập nhật: ' . $sqlErr;
        }

        header('Location: ' . base_url('admin/orders/' . $id));
        exit;
    }
}