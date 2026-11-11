<?php
namespace App\Modules\Coupon;

use App\Core\Controller;
use App\Modules\Coupon\CouponModel;

class AdminCouponController extends Controller {

    private CouponModel $couponModel;

    public function __construct() {
        $this->couponModel = new CouponModel();
    }

    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $coupons = $this->couponModel->getAllCoupons();

        $this->view('admin.coupon.index', [
            'title'   => 'Quản lý mã giảm giá — Admin PCMarket',
            'coupons' => $coupons
        ]);
    }

    public function create(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $this->view('admin.coupon.form', [
            'title'  => 'Thêm mã giảm giá mới',
            'coupon' => null
        ]);
    }

    public function store(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $code = strtoupper(trim($_POST['code'] ?? ''));
        if (empty($code) || empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $_SESSION['admin_error'] = 'Vui lòng điền đầy đủ Mã Code, Ngày bắt đầu và Ngày kết thúc!';
            header('Location: ' . base_url('admin/coupons/create'));
            exit;
        }

        $existing = $this->couponModel->getCouponByCode($code);
        if ($existing) {
            $_SESSION['admin_error'] = 'Mã giảm giá này đã tồn tại trong hệ thống!';
            header('Location: ' . base_url('admin/coupons/create'));
            exit;
        }

        $data = [
            'code'             => $code,
            'description'      => trim($_POST['description'] ?? ''),
            'type'             => $_POST['type'] ?? 'fixed',
            'discount_value'   => floatval($_POST['discount_value'] ?? 0),
            'min_order_amount' => floatval($_POST['min_order_amount'] ?? 0),
            'max_discount'     => !empty($_POST['max_discount']) ? floatval($_POST['max_discount']) : null,
            'usage_limit'      => !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 100,
            'start_date'       => $_POST['start_date'],
            'end_date'         => $_POST['end_date'],
            'status'           => intval($_POST['status'] ?? 1)
        ];

        $result = $this->couponModel->createCoupon($data);

        if ($result) {
            $_SESSION['admin_success'] = "Đã thêm mã giảm giá {$code} thành công!";
            header('Location: ' . base_url('admin/coupons'));
        } else {
            $sqlErr = $_SESSION['coupon_sql_error'] ?? 'Lỗi không xác định';
            unset($_SESSION['coupon_sql_error']);
            $_SESSION['admin_error'] = 'Lỗi SQL: ' . $sqlErr;
            header('Location: ' . base_url('admin/coupons/create'));
        }
        exit;
    }

    public function edit(int $id): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $coupon = $this->couponModel->getCouponById($id);
        if (!$coupon) {
            $_SESSION['admin_error'] = 'Mã giảm giá không tồn tại!';
            header('Location: ' . base_url('admin/coupons'));
            exit;
        }

        $this->view('admin.coupon.form', [
            'title'  => 'Chỉnh sửa mã giảm giá #' . $coupon['code'],
            'coupon' => $coupon
        ]);
    }

    public function update(int $id): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $coupon = $this->couponModel->getCouponById($id);
        if (!$coupon) {
            $_SESSION['admin_error'] = 'Mã giảm giá không tồn tại!';
            header('Location: ' . base_url('admin/coupons'));
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $existing = $this->couponModel->getCouponByCode($code);
        if ($existing && $existing['id'] != $id) {
            $_SESSION['admin_error'] = 'Tên mã này đã được sử dụng bởi coupon khác!';
            header('Location: ' . base_url('admin/coupons/' . $id . '/edit'));
            exit;
        }

        $data = [
            'code'             => $code,
            'description'      => trim($_POST['description'] ?? ''),
            'type'             => $_POST['type'] ?? 'fixed',
            'discount_value'   => floatval($_POST['discount_value'] ?? 0),
            'min_order_amount' => floatval($_POST['min_order_amount'] ?? 0),
            'max_discount'     => !empty($_POST['max_discount']) ? floatval($_POST['max_discount']) : null,
            'usage_limit'      => !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null,
            'start_date'       => $_POST['start_date'],
            'end_date'         => $_POST['end_date'],
            'status'           => intval($_POST['status'] ?? 1)
        ];

        $result = $this->couponModel->updateCoupon($id, $data);

        if ($result) {
            $_SESSION['admin_success'] = "Cập nhật mã giảm giá {$code} thành công!";
            header('Location: ' . base_url('admin/coupons'));
        } else {
            $sqlErr = $_SESSION['coupon_sql_error'] ?? 'Lỗi không xác định';
            unset($_SESSION['coupon_sql_error']);
            $_SESSION['admin_error'] = 'Lỗi SQL: ' . $sqlErr;
            header('Location: ' . base_url('admin/coupons/' . $id . '/edit'));
        }
        exit;
    }

    public function delete(int $id): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $result = $this->couponModel->deleteCoupon($id);
        if ($result) {
            $_SESSION['admin_success'] = 'Đã xóa mã giảm giá thành công!';
        } else {
            $_SESSION['admin_error'] = 'Không thể xóa mã giảm giá này!';
        }

        header('Location: ' . base_url('admin/coupons'));
        exit;
    }
}