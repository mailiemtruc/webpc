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
                ':type'             => $data['type'], // 'percentage' hoặc 'fixed'
                ':discount_value'   => $data['discount_value'],
                ':min_order_amount' => $data['min_order_amount'] ?? 0,
                ':max_discount'     => !empty($data['max_discount']) ? $data['max_discount'] : null,
                ':usage_limit'      => !empty($data['usage_limit']) ? $data['usage_limit'] : 100,
                ':start_date'       => $data['start_date'],
                ':end_date'         => $data['end_date'],
                ':status'           => $data['status'] ?? 1
            ]);
        } catch (Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) session_start();
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
                ':usage_limit'      => !empty($data['usage_limit']) ? $data['usage_limit'] : null,
                ':start_date'       => $data['start_date'],
                ':end_date'         => $data['end_date'],
                ':status'           => $data['status'] ?? 1,
                ':id'               => $id
            ]);
        } catch (Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) session_start();
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