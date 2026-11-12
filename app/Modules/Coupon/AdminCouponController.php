<?php
namespace App\Modules\Coupon;

use App\Core\Controller;
use App\Core\Middleware;
use App\Modules\Coupon\CouponModel;

class AdminCouponController extends Controller {

    private CouponModel $couponModel;

    public function __construct() {
        Middleware::staff();
        $this->couponModel = new CouponModel();
    }

    public function index(): void {
        $coupons = $this->couponModel->getAllCoupons();

        $this->view('admin.coupon.index', [
            'title'   => 'Quản lý mã giảm giá — Admin PCMarket',
            'coupons' => $coupons
        ]);
    }

    public function create(): void {
        $this->view('admin.coupon.form', [
            'title'  => 'Thêm mã giảm giá mới',
            'coupon' => null
        ]);
    }

    public function store(): void {
        Middleware::csrf();

        $parsed = $this->validateAndBuildPayload();
        if ($parsed['error'] !== null) {
            set_flash('error', $parsed['error']);
            $this->redirect('admin/coupons/create');
        }

        $data = $parsed['data'];
        if ($this->couponModel->getCouponByCode($data['code'])) {
            set_flash('error', 'Mã giảm giá này đã tồn tại trong hệ thống!');
            $this->redirect('admin/coupons/create');
        }

        if ($this->couponModel->createCoupon($data)) {
            set_flash('success', "Đã thêm mã giảm giá {$data['code']} thành công!");
            $this->redirect('admin/coupons');
        }

        $sqlErr = $_SESSION['coupon_sql_error'] ?? 'Lỗi không xác định';
        unset($_SESSION['coupon_sql_error']);
        set_flash('error', 'Lỗi SQL: ' . $sqlErr);
        $this->redirect('admin/coupons/create');
    }

    public function edit(int $id): void {
        $coupon = $this->couponModel->getCouponById($id);
        if (!$coupon) {
            set_flash('error', 'Mã giảm giá không tồn tại!');
            $this->redirect('admin/coupons');
        }

        $this->view('admin.coupon.form', [
            'title'  => 'Chỉnh sửa mã giảm giá #' . $coupon['code'],
            'coupon' => $coupon
        ]);
    }

    public function update(int $id): void {
        Middleware::csrf();

        $coupon = $this->couponModel->getCouponById($id);
        if (!$coupon) {
            set_flash('error', 'Mã giảm giá không tồn tại!');
            $this->redirect('admin/coupons');
        }

        $parsed = $this->validateAndBuildPayload();
        if ($parsed['error'] !== null) {
            set_flash('error', $parsed['error']);
            $this->redirect('admin/coupons/' . $id . '/edit');
        }

        $data     = $parsed['data'];
        $existing = $this->couponModel->getCouponByCode($data['code']);
        if ($existing && (int)$existing['id'] !== $id) {
            set_flash('error', 'Tên mã này đã được sử dụng bởi coupon khác!');
            $this->redirect('admin/coupons/' . $id . '/edit');
        }

        if ($this->couponModel->updateCoupon($id, $data)) {
            set_flash('success', "Cập nhật mã giảm giá {$data['code']} thành công!");
            $this->redirect('admin/coupons');
        }

        $sqlErr = $_SESSION['coupon_sql_error'] ?? 'Lỗi không xác định';
        unset($_SESSION['coupon_sql_error']);
        set_flash('error', 'Lỗi SQL: ' . $sqlErr);
        $this->redirect('admin/coupons/' . $id . '/edit');
    }

    public function delete(int $id): void {
        Middleware::csrf();

        if ($this->couponModel->deleteCoupon($id)) {
            set_flash('success', 'Đã xóa mã giảm giá thành công!');
        } else {
            set_flash('error', 'Không thể xóa mã giảm giá này!');
        }

        $this->redirect('admin/coupons');
    }

    /**
     * Kiểm tra hợp lệ và chuẩn hóa dữ liệu biểu mẫu mã giảm giá.
     *
     * @return array{data: ?array, error: ?string}
     */
    private function validateAndBuildPayload(): array {
        $code          = strtoupper(trim((string)($_POST['code'] ?? '')));
        $startDate     = trim((string)($_POST['start_date'] ?? ''));
        $endDate       = trim((string)($_POST['end_date'] ?? ''));
        $type          = trim((string)($_POST['type'] ?? 'fixed'));
        $discountValue = (float)($_POST['discount_value'] ?? 0);
        $minOrder      = (float)($_POST['min_order_amount'] ?? 0);
        $maxRaw        = trim((string)($_POST['max_discount'] ?? ''));
        $limitRaw      = trim((string)($_POST['usage_limit'] ?? ''));
        $status        = (int)($_POST['status'] ?? 1) === 1 ? 1 : 0;

        if ($code === '' || !preg_match('/^[A-Z0-9_-]{2,50}$/', $code)) {
            return ['data' => null, 'error' => 'Mã giảm giá chỉ gồm 2-50 ký tự chữ in hoa, số, dấu gạch dưới hoặc gạch ngang.'];
        }

        if ($startDate === '' || $endDate === '') {
            return ['data' => null, 'error' => 'Vui lòng điền đầy đủ Ngày bắt đầu và Ngày kết thúc!'];
        }

        $startObj = \DateTime::createFromFormat('Y-m-d', $startDate);
        $endObj   = \DateTime::createFromFormat('Y-m-d', $endDate);
        if (!$startObj || $startObj->format('Y-m-d') !== $startDate || !$endObj || $endObj->format('Y-m-d') !== $endDate) {
            return ['data' => null, 'error' => 'Định dạng ngày không hợp lệ (YYYY-MM-DD).'];
        }

        if ($endDate < $startDate) {
            return ['data' => null, 'error' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!'];
        }

        if (!in_array($type, ['fixed', 'percentage'], true)) {
            return ['data' => null, 'error' => 'Loại giảm giá không hợp lệ.'];
        }

        if ($discountValue <= 0) {
            return ['data' => null, 'error' => 'Giá trị giảm phải lớn hơn 0!'];
        }

        if ($type === 'percentage' && $discountValue > 100) {
            return ['data' => null, 'error' => 'Mức giảm theo phần trăm không được vượt quá 100%!'];
        }

        if ($minOrder < 0) {
            return ['data' => null, 'error' => 'Giá trị đơn tối thiểu không được âm!'];
        }

        $maxDiscount = null;
        if ($type === 'percentage' && $maxRaw !== '') {
            $maxDiscount = (float)$maxRaw;
            if ($maxDiscount <= 0) {
                return ['data' => null, 'error' => 'Mức giảm tối đa phải lớn hơn 0 hoặc để trống!'];
            }
        }

        $usageLimit = null;
        if ($limitRaw !== '') {
            $usageLimit = (int)$limitRaw;
            if ($usageLimit < 1) {
                return ['data' => null, 'error' => 'Giới hạn lượt dùng phải từ 1 trở lên hoặc để trống (không giới hạn)!'];
            }
        }

        return [
            'error' => null,
            'data'  => [
                'code'             => $code,
                'description'      => trim((string)($_POST['description'] ?? '')),
                'type'             => $type,
                'discount_value'   => $discountValue,
                'min_order_amount' => $minOrder,
                'max_discount'     => $maxDiscount,
                'usage_limit'      => $usageLimit,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'status'           => $status,
            ],
        ];
    }
}