<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Core\Middleware;
use App\Modules\Order\OrderModel;

class AdminOrderController extends Controller {

    private OrderModel $orderModel;

    public function __construct() {
        Middleware::staff();
        $this->orderModel = new OrderModel();
    }

    /**
     * Danh sách đơn hàng trong Admin (GET /admin/orders)
     */
    public function index(): void {
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
        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            set_flash('error', 'Đơn hàng không tồn tại!');
            $this->redirect('admin/orders');
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
        Middleware::csrf();

        $status        = trim((string)($_POST['status'] ?? 'pending'));
        $paymentStatus = isset($_POST['payment_status']) ? trim((string)$_POST['payment_status']) : null;

        $result = $this->orderModel->updateStatus($id, $status, $paymentStatus);

        if ($result) {
            set_flash('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } else {
            $sqlErr = $_SESSION['admin_sql_error'] ?? 'Cập nhật thất bại.';
            unset($_SESSION['admin_sql_error']);
            set_flash('error', 'Lỗi cập nhật: ' . $sqlErr);
        }

        $this->redirect('admin/orders/' . $id);
    }
}