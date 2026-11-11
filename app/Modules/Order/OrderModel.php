<?php
namespace App\Modules\Order;

use App\Core\Database;
use PDO;
use Throwable;

class OrderModel {

    /**
     * Tạo đơn hàng mới và lưu các chi tiết đơn hàng (Sử dụng Transaction)
     */
    public function createOrder(array $orderData, array $cartItems) {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // 1. Thêm bản ghi vào bảng orders
            $sqlOrder = "INSERT INTO orders (
                order_code, user_id, customer_name, customer_phone, customer_email,
                shipping_address, note, subtotal, coupon_id, coupon_code,
                discount_amount, final_total, payment_method, payment_status, status, created_at
            ) VALUES (
                :order_code, :user_id, :customer_name, :customer_phone, :customer_email,
                :shipping_address, :note, :subtotal, :coupon_id, :coupon_code,
                :discount_amount, :final_total, :payment_method, :payment_status, 'pending', NOW()
            )";

            $stmtOrder = $db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':order_code'       => $orderData['order_code'],
                ':user_id'          => !empty($orderData['user_id']) ? $orderData['user_id'] : null,
                ':customer_name'    => $orderData['customer_name'],
                ':customer_phone'   => $orderData['customer_phone'],
                ':customer_email'   => !empty($orderData['customer_email']) ? $orderData['customer_email'] : null,
                ':shipping_address' => $orderData['shipping_address'],
                ':note'             => !empty($orderData['note']) ? $orderData['note'] : null,
                ':subtotal'         => $orderData['subtotal'],
                ':coupon_id'        => !empty($orderData['coupon_id']) ? $orderData['coupon_id'] : null,
                ':coupon_code'      => !empty($orderData['coupon_code']) ? $orderData['coupon_code'] : null,
                ':discount_amount'  => $orderData['discount_amount'] ?? 0,
                ':final_total'      => $orderData['final_total'],
                ':payment_method'   => $orderData['payment_method'] ?? 'cod',
                ':payment_status'   => $orderData['payment_status'] ?? 'unpaid'
            ]);

            $orderId = (int)$db->lastInsertId();

            // 2. Thêm từng sản phẩm vào bảng order_items & Trừ tồn kho
            $sqlItem = "INSERT INTO order_items (
                order_id, product_id, product_name, product_sku, product_thumbnail, price, quantity, total
            ) VALUES (
                :order_id, :product_id, :product_name, :product_sku, :product_thumbnail, :price, :quantity, :total
            )";
            $stmtItem = $db->prepare($sqlItem);

            $sqlUpdateStock = "UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
            $stmtStock = $db->prepare($sqlUpdateStock);

            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];

                $stmtItem->execute([
                    ':order_id'          => $orderId,
                    ':product_id'        => $item['id'],
                    ':product_name'      => $item['name'],
                    ':product_sku'       => $item['sku'] ?? ('PCM-' . $item['id']),
                    ':product_thumbnail' => $item['thumbnail'] ?? '',
                    ':price'             => $item['price'],
                    ':quantity'          => $item['quantity'],
                    ':total'             => $itemTotal
                ]);

                $stmtStock->execute([
                    $item['quantity'],
                    $item['id'],
                    $item['quantity']
                ]);
            }

            // 3. Tăng số lượt sử dụng của coupon nếu có
            if (!empty($orderData['coupon_id'])) {
                $stmtCoupon = $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
                $stmtCoupon->execute([$orderData['coupon_id']]);
            }

            $db->commit();
            return $orderId;

        } catch (Throwable $e) {
            $db->rollBack();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['sql_error'] = $e->getMessage();
            return false;
        }
    }

    /**
     * Lấy thông tin đơn hàng theo mã đơn
     */
    public function getOrderByCode(string $orderCode) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders WHERE order_code = ? LIMIT 1");
        $stmt->execute([$orderCode]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $stmtItems = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmtItems->execute([$order['id']]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }

        return $order;
    }

    /**
     * Lấy chi tiết đơn hàng theo ID (Bao gồm danh sách sản phẩm)
     */
    public function getOrderById(int $id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $stmtItems = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmtItems->execute([$order['id']]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }

        return $order;
    }

    /**
     * Lấy danh sách đơn hàng cho Admin
     */
    public function getAllOrders(int $limit = 20, int $offset = 0): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng & thanh toán
     */
    public function updateStatus(int $orderId, string $status, ?string $paymentStatus = null): bool {
        $db = Database::getInstance();
        try {
            if ($paymentStatus !== null) {
                $stmt = $db->prepare("UPDATE orders SET status = ?, payment_status = ? WHERE id = ?");
                return $stmt->execute([$status, $paymentStatus, $orderId]);
            }

            $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $orderId]);
        } catch (Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_sql_error'] = $e->getMessage();
            return false;
        }
    }
}