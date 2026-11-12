<?php
namespace App\Modules\Coupon;

use App\Core\Database;
use PDO;
use Throwable;

class CouponModel {

    public function getAllCoupons(): array {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM coupons ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCouponById(int $id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCouponByCode(string $code) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? LIMIT 1");
        $stmt->execute([strtoupper(trim($code))]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra tính hợp lệ và tính số tiền giảm giá cho đơn hàng từ DB.
     *
     * @return array{valid:bool, message:string, coupon:?array, discount_amount:float, final_total:float}
     */
    public function validateAndCalculate(string $code, float $subtotal): array {
        $cleanCode = strtoupper(trim($code));
        if ($cleanCode === '') {
            return [
                'valid'           => false,
                'message'         => 'Vui lòng nhập mã giảm giá.',
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => max(0.0, $subtotal),
            ];
        }

        if ($subtotal <= 0) {
            return [
                'valid'           => false,
                'message'         => 'Giỏ hàng đang trống, không thể áp dụng mã.',
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => 0.0,
            ];
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 LIMIT 1");
        $stmt->execute([$cleanCode]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            return [
                'valid'           => false,
                'message'         => 'Mã giảm giá không tồn tại hoặc đã bị khóa.',
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => $subtotal,
            ];
        }

        // So sánh theo ngày (Y-m-d) vì cột start_date / end_date là kiểu DATE
        $today = date('Y-m-d');
        if (!empty($coupon['start_date']) && substr((string)$coupon['start_date'], 0, 10) > $today) {
            return [
                'valid'           => false,
                'message'         => 'Mã giảm giá chưa đến thời gian sử dụng.',
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => $subtotal,
            ];
        }

        if (!empty($coupon['end_date']) && substr((string)$coupon['end_date'], 0, 10) < $today) {
            return [
                'valid'           => false,
                'message'         => 'Mã giảm giá đã hết hạn sử dụng.',
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => $subtotal,
            ];
        }

        $usedCount = (int)($coupon['used_count'] ?? 0);
        $usageLimit = (int)($coupon['usage_limit'] ?? 0);
        if ($usageLimit > 0 && $usedCount >= $usageLimit) {
            return [
                'valid'           => false,
                'message'         => 'Mã giảm giá đã hết lượt sử dụng.',
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => $subtotal,
            ];
        }

        $minOrderAmount = (float)($coupon['min_order_amount'] ?? 0);
        if ($subtotal < $minOrderAmount) {
            $minFormatted = number_format($minOrderAmount, 0, ',', '.');
            return [
                'valid'           => false,
                'message'         => "Đơn hàng tối thiểu phải từ {$minFormatted} VNĐ mới áp dụng được mã này.",
                'coupon'          => null,
                'discount_amount' => 0.0,
                'final_total'     => $subtotal,
            ];
        }

        $type = $coupon['type'] ?? 'fixed';
        $value = (float)($coupon['discount_value'] ?? 0);

        if ($type === 'percentage' || $type === 'percent') {
            $discountAmount = round(($subtotal * $value) / 100, 0);
            $maxDiscount = (float)($coupon['max_discount'] ?? 0);
            if ($maxDiscount > 0 && $discountAmount > $maxDiscount) {
                $discountAmount = $maxDiscount;
            }
        } else {
            $discountAmount = $value;
        }

        $discountAmount = max(0.0, min($discountAmount, $subtotal));

        return [
            'valid'           => true,
            'message'         => 'Áp dụng mã giảm giá thành công!',
            'coupon'          => $coupon,
            'discount_amount' => $discountAmount,
            'final_total'     => max(0.0, $subtotal - $discountAmount),
        ];
    }

    public function createCoupon(array $data): bool {
        $db = Database::getInstance();
        try {
            $sql = "INSERT INTO coupons (
                code, description, type, discount_value, min_order_amount, max_discount, usage_limit, used_count, start_date, end_date, status, created_at
            ) VALUES (
                :code, :description, :type, :discount_value, :min_order_amount, :max_discount, :usage_limit, 0, :start_date, :end_date, :status, NOW()
            )";

            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':code'             => strtoupper(trim($data['code'])),
                ':description'      => !empty($data['description']) ? $data['description'] : null,
                ':type'             => $data['type'],
                ':discount_value'   => $data['discount_value'],
                ':min_order_amount' => $data['min_order_amount'] ?? 0,
                ':max_discount'     => !empty($data['max_discount']) ? $data['max_discount'] : null,
                ':usage_limit'      => (!empty($data['usage_limit']) && (int)$data['usage_limit'] > 0) ? (int)$data['usage_limit'] : null,
                ':start_date'       => $data['start_date'],
                ':end_date'         => $data['end_date'],
                ':status'           => $data['status'] ?? 1
            ]);
        } catch (Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['coupon_sql_error'] = $e->getMessage();
            return false;
        }
    }

    public function updateCoupon(int $id, array $data): bool {
        $db = Database::getInstance();
        try {
            $sql = "UPDATE coupons SET 
                code = :code,
                description = :description,
                type = :type,
                discount_value = :discount_value,
                min_order_amount = :min_order_amount,
                max_discount = :max_discount,
                usage_limit = :usage_limit,
                start_date = :start_date,
                end_date = :end_date,
                status = :status
                WHERE id = :id";

            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':code'             => strtoupper(trim($data['code'])),
                ':description'      => !empty($data['description']) ? $data['description'] : null,
                ':type'             => $data['type'],
                ':discount_value'   => $data['discount_value'],
                ':min_order_amount' => $data['min_order_amount'] ?? 0,
                ':max_discount'     => !empty($data['max_discount']) ? $data['max_discount'] : null,
                ':usage_limit'      => (!empty($data['usage_limit']) && (int)$data['usage_limit'] > 0) ? (int)$data['usage_limit'] : null,
                ':start_date'       => $data['start_date'],
                ':end_date'         => $data['end_date'],
                ':status'           => $data['status'] ?? 1,
                ':id'               => $id
            ]);
        } catch (Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['coupon_sql_error'] = $e->getMessage();
            return false;
        }
    }

    public function deleteCoupon(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM coupons WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
