<?php
namespace App\Modules\Order;

use App\Core\Database;
use PDO;
use RuntimeException;
use Throwable;

class OrderModel {

    public const VALID_STATUSES = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
    public const VALID_PAYMENT_STATUSES = ['unpaid', 'paid'];
    public const VALID_PAYMENT_METHODS = ['cod', 'banking'];

    /**
     * Đối chiếu giỏ hàng với dữ liệu sản phẩm thực tế trong CSDL (giá hiện hành, SKU, tồn kho).
     *
     * @return array{items:array, subtotal:float, total_qty:int, errors:array}
     */
    public function evaluateCart(array $sessionCart): array {
        if (empty($sessionCart)) {
            return ['items' => [], 'subtotal' => 0.0, 'total_qty' => 0, 'errors' => []];
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', array_keys($sessionCart)), fn($v) => $v > 0)));
        if (empty($ids)) {
            return ['items' => [], 'subtotal' => 0.0, 'total_qty' => 0, 'errors' => ['Giỏ hàng không chứa sản phẩm hợp lệ.']];
        }

        $db = Database::getInstance();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("
            SELECT id, name, slug, sku, price, discount_price, thumbnail, quantity, status
            FROM products
            WHERE id IN ({$placeholders})
        ");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productsById = [];
        foreach ($rows as $row) {
            $productsById[(int)$row['id']] = $row;
        }

        $items = [];
        $subtotal = 0.0;
        $totalQty = 0;
        $errors = [];

        foreach ($sessionCart as $rawId => $cartRow) {
            $pid = (int)$rawId;
            $qty = max(1, (int)($cartRow['quantity'] ?? 1));
            $product = $productsById[$pid] ?? null;

            if (!$product || (int)$product['status'] !== 1) {
                $name = $cartRow['name'] ?? ('#' . $pid);
                $errors[] = "Sản phẩm '{$name}' hiện không còn kinh doanh.";
                continue;
            }

            $stock = (int)$product['quantity'];
            if ($stock < $qty) {
                $errors[] = "Sản phẩm '{$product['name']}' chỉ còn {$stock} sản phẩm trong kho.";
            }

            $regularPrice = (float)$product['price'];
            $discountPrice = (float)($product['discount_price'] ?? 0);
            $unitPrice = ($discountPrice > 0 && $discountPrice < $regularPrice) ? $discountPrice : $regularPrice;
            $lineTotal = $unitPrice * $qty;

            $items[$pid] = [
                'id'             => $pid,
                'name'           => $product['name'],
                'slug'           => $product['slug'],
                'sku'            => $product['sku'] ?: ('PCM-' . $pid),
                'price'          => $unitPrice,
                'original_price' => $regularPrice,
                'thumbnail'      => $product['thumbnail'] ?? '',
                'quantity'       => $qty,
                'stock'          => $stock,
                'line_total'     => $lineTotal,
            ];

            $subtotal += $lineTotal;
            $totalQty += $qty;
        }

        return [
            'items'     => $items,
            'subtotal'  => $subtotal,
            'total_qty' => $totalQty,
            'errors'    => $errors,
        ];
    }

    /**
     * Tạo đơn hàng mới và lưu các chi tiết đơn hàng (Sử dụng Transaction + Kiểm tra tồn kho/coupon chặt chẽ)
     */
    public function createOrder(array $orderData, array $cartItems): int|false {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

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
                ':user_id'          => !empty($orderData['user_id']) ? (int)$orderData['user_id'] : null,
                ':customer_name'    => $orderData['customer_name'],
                ':customer_phone'   => $orderData['customer_phone'],
                ':customer_email'   => !empty($orderData['customer_email']) ? $orderData['customer_email'] : null,
                ':shipping_address' => $orderData['shipping_address'],
                ':note'             => !empty($orderData['note']) ? $orderData['note'] : null,
                ':subtotal'         => $orderData['subtotal'],
                ':coupon_id'        => !empty($orderData['coupon_id']) ? (int)$orderData['coupon_id'] : null,
                ':coupon_code'      => !empty($orderData['coupon_code']) ? $orderData['coupon_code'] : null,
                ':discount_amount'  => $orderData['discount_amount'] ?? 0,
                ':final_total'      => $orderData['final_total'],
                ':payment_method'   => $orderData['payment_method'] ?? 'cod',
                ':payment_status'   => $orderData['payment_status'] ?? 'unpaid'
            ]);

            $orderId = (int)$db->lastInsertId();

            $sqlItem = "INSERT INTO order_items (
                order_id, product_id, product_name, product_sku, product_thumbnail, price, quantity, total
            ) VALUES (
                :order_id, :product_id, :product_name, :product_sku, :product_thumbnail, :price, :quantity, :total
            )";
            $stmtItem = $db->prepare($sqlItem);

            $sqlUpdateStock = "UPDATE products SET quantity = quantity - ? WHERE id = ? AND status = 1 AND quantity >= ?";
            $stmtStock = $db->prepare($sqlUpdateStock);

            foreach ($cartItems as $item) {
                $qty = max(1, (int)$item['quantity']);
                $itemTotal = (float)$item['price'] * $qty;

                $stmtStock->execute([$qty, (int)$item['id'], $qty]);
                if ($stmtStock->rowCount() === 0) {
                    throw new RuntimeException("Sản phẩm '{$item['name']}' không đủ số lượng tồn kho hoặc đã ngừng kinh doanh.");
                }

                $stmtItem->execute([
                    ':order_id'          => $orderId,
                    ':product_id'        => (int)$item['id'],
                    ':product_name'      => $item['name'],
                    ':product_sku'       => !empty($item['sku']) ? $item['sku'] : ('PCM-' . $item['id']),
                    ':product_thumbnail' => $item['thumbnail'] ?? '',
                    ':price'             => $item['price'],
                    ':quantity'          => $qty,
                    ':total'             => $itemTotal
                ]);
            }

            if (!empty($orderData['coupon_id'])) {
                $stmtCoupon = $db->prepare("
                    UPDATE coupons
                    SET used_count = used_count + 1
                    WHERE id = ? AND status = 1 AND (usage_limit IS NULL OR usage_limit <= 0 OR used_count < usage_limit)
                ");
                $stmtCoupon->execute([(int)$orderData['coupon_id']]);
                if ($stmtCoupon->rowCount() === 0) {
                    throw new RuntimeException('Mã giảm giá đã hết lượt sử dụng hoặc không còn hiệu lực.');
                }
            }

            $db->commit();
            return $orderId;

        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['order_error_message'] = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Không thể tạo đơn hàng do lỗi hệ thống. Vui lòng thử lại.';
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
    public function getAllOrders(int $limit = 100, int $offset = 0): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng & thanh toán (Đồng bộ hoàn tồn kho & coupon khi hủy đơn)
     */
    public function updateStatus(int $orderId, string $status, ?string $paymentStatus = null): bool {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            $_SESSION['admin_sql_error'] = 'Trạng thái đơn hàng không hợp lệ.';
            return false;
        }
        if ($paymentStatus !== null && !in_array($paymentStatus, self::VALID_PAYMENT_STATUSES, true)) {
            $_SESSION['admin_sql_error'] = 'Trạng thái thanh toán không hợp lệ.';
            return false;
        }

        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $stmtOrder = $db->prepare("SELECT id, status, coupon_id FROM orders WHERE id = ? FOR UPDATE");
            $stmtOrder->execute([$orderId]);
            $current = $stmtOrder->fetch(PDO::FETCH_ASSOC);

            if (!$current) {
                throw new RuntimeException('Đơn hàng không tồn tại.');
            }

            $oldStatus = $current['status'];

            // Hoàn tồn kho & lượt dùng coupon khi chuyển sang 'cancelled'
            if ($oldStatus !== 'cancelled' && $status === 'cancelled') {
                $stmtItems = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ? AND product_id IS NOT NULL");
                $stmtItems->execute([$orderId]);
                $restoreStmt = $db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                foreach ($stmtItems->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    $restoreStmt->execute([(int)$item['quantity'], (int)$item['product_id']]);
                }

                if (!empty($current['coupon_id'])) {
                    $db->prepare("UPDATE coupons SET used_count = GREATEST(0, used_count - 1) WHERE id = ?")
                       ->execute([(int)$current['coupon_id']]);
                }
            }

            // Trừ lại tồn kho nếu mở lại đơn đã hủy
            if ($oldStatus === 'cancelled' && $status !== 'cancelled') {
                $stmtItems = $db->prepare("SELECT product_id, product_name, quantity FROM order_items WHERE order_id = ? AND product_id IS NOT NULL");
                $stmtItems->execute([$orderId]);
                $deductStmt = $db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
                foreach ($stmtItems->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    $deductStmt->execute([(int)$item['quantity'], (int)$item['product_id'], (int)$item['quantity']]);
                    if ($deductStmt->rowCount() === 0) {
                        throw new RuntimeException("Không đủ tồn kho để khôi phục sản phẩm '{$item['product_name']}'.");
                    }
                }

                if (!empty($current['coupon_id'])) {
                    $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?")
                       ->execute([(int)$current['coupon_id']]);
                }
            }

            if ($paymentStatus !== null) {
                $stmt = $db->prepare("UPDATE orders SET status = ?, payment_status = ? WHERE id = ?");
                $stmt->execute([$status, $paymentStatus, $orderId]);
            } else {
                $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->execute([$status, $orderId]);
            }

            $db->commit();
            return true;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_sql_error'] = $e instanceof RuntimeException ? $e->getMessage() : $e->getMessage();
            return false;
        }
    }
}
