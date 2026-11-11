<?php
namespace App\Modules\Order;

use App\Core\Controller;
use App\Core\Database;
use PDO;
use Exception;

class CouponController extends Controller {

    /**
     * Áp dụng mã giảm giá (API Ajax)
     */
    public function apply(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        ob_start(); // Bắt đầu đệm bộ nhớ để xóa sạch các cảnh báo HTML nếu có

        try {
            $code = trim($_POST['code'] ?? '');
            $cart = $_SESSION['cart'] ?? [];

            if (empty($cart)) {
                $this->sendJson(['success' => false, 'message' => 'Giỏ hàng đang trống, không thể áp dụng mã.']);
            }

            if (empty($code)) {
                $this->sendJson(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            }

            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 LIMIT 1");
            $stmt->execute([strtoupper($code)]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$coupon) {
                $this->sendJson(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã bị khóa.']);
            }

            $now = date('Y-m-d H:i:s');
            if (!empty($coupon['start_date']) && $coupon['start_date'] > $now) {
                $this->sendJson(['success' => false, 'message' => 'Mã giảm giá chưa đến thời gian sử dụng.']);
            }

            if (!empty($coupon['end_date']) && $coupon['end_date'] < $now) {
                $this->sendJson(['success' => false, 'message' => 'Mã giảm giá đã hết hạn sử dụng.']);
            }

            $usedCount = (int)($coupon['used_count'] ?? 0);
            $usageLimit = (int)($coupon['usage_limit'] ?? 0);
            if ($usageLimit > 0 && $usedCount >= $usageLimit) {
                $this->sendJson(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
            }

            $minOrderAmount = (float)($coupon['min_order_amount'] ?? $coupon['min_order_value'] ?? 0);
            if ($subtotal < $minOrderAmount) {
                $minAmountFormatted = number_format($minOrderAmount, 0, ',', '.');
                $this->sendJson([
                    'success' => false, 
                    'message' => "Đơn hàng tối thiểu phải từ {$minAmountFormatted} VNĐ mới áp dụng được mã này."
                ]);
            }

            // Đọc linh hoạt tên cột discount_type / type trong DB
            $discountType = $coupon['discount_type'] ?? $coupon['type'] ?? 'fixed';
            $discountValue = (float)($coupon['discount_value'] ?? $coupon['value'] ?? $coupon['discount'] ?? 0);

            $discountAmount = 0;
            if ($discountType === 'percent' || $discountType === 'percentage') {
                $discountAmount = ($subtotal * $discountValue) / 100;
                $maxDiscount = (float)($coupon['max_discount_amount'] ?? $coupon['max_discount'] ?? 0);
                if ($maxDiscount > 0 && $discountAmount > $maxDiscount) {
                    $discountAmount = $maxDiscount;
                }
            } else {
                $discountAmount = $discountValue;
            }

            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }

            $_SESSION['coupon'] = [
                'id'              => $coupon['id'],
                'code'            => $coupon['code'],
                'discount_type'   => $discountType,
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount
            ];

            $finalTotal = max(0, $subtotal - $discountAmount);

            $this->sendJson([
                'success'        => true,
                'message'        => 'Áp dụng mã giảm giá thành công!',
                'couponCode'     => $coupon['code'],
                'discountAmount' => $discountAmount,
                'subtotal'       => $subtotal,
                'finalTotal'     => $finalTotal
            ]);

        } catch (Exception $e) {
            $this->sendJson(['success' => false, 'message' => 'Lỗi Server: ' . $e->getMessage()]);
        }
    }

    /**
     * Hủy mã giảm giá (API Ajax)
     */
    public function remove(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['coupon']);

        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $this->sendJson([
            'success'    => true,
            'message'    => 'Đã hủy mã giảm giá.',
            'subtotal'   => $subtotal,
            'finalTotal' => $subtotal
        ]);
    }

    /**
     * Dọn dẹp bộ đệm và xuất dữ liệu JSON chuẩn
     */
    private function sendJson(array $data): void {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}