<?php require VIEWS_DIR . '/layouts/admin_header.php'; ?>

<div class="content-wrapper" style="padding: 24px;">
    <div style="margin-bottom: 20px;">
        <a href="<?= base_url('admin/orders') ?>" style="color: #64748b; text-decoration: none; font-weight: 700; font-size: 14px;">← Quay lại danh sách đơn hàng</a>
        <h1 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 8px;">CHI TIẾT ĐƠN HÀNG #<?= htmlspecialchars($order['order_code']) ?></h1>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        
        <!-- Cột trái: Chi tiết sản phẩm & Địa chỉ giao hàng -->
        <div>
            <!-- Thông tin khách hàng -->
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 14px; color: #0f172a;">Thông tin nhận hàng</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 14px;">
                    <div><strong>Họ tên:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
                    <div><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_phone']) ?></div>
                    <div><strong>Email:</strong> <?= htmlspecialchars($order['customer_email'] ?? 'N/A') ?></div>
                    <div><strong>Phương thức:</strong> <?= strtoupper($order['payment_method']) ?></div>
                    <div style="grid-column: span 2;"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></div>
                    <?php if (!empty($order['note'])): ?>
                        <div style="grid-column: span 2; color: #b45309;"><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 14px; color: #0f172a;">Sản phẩm đã đặt</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b;">
                            <th style="padding: 8px;">Sản phẩm</th>
                            <th style="padding: 8px; text-align: center;">Đơn giá</th>
                            <th style="padding: 8px; text-align: center;">Số lượng</th>
                            <th style="padding: 8px; text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; display: flex; align-items: center; gap: 10px;">
                                    <img src="<?= htmlspecialchars(product_image_url($item['product_thumbnail'] ?? '')) ?>" width="40" height="40" style="object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px;">
                                    <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                </td>
                                <td style="padding: 12px 8px; text-align: center; font-weight: 700; color: #dc2626;">
                                    <?= number_format($item['price'], 0, ',', '.') ?> đ
                                </td>
                                <td style="padding: 12px 8px; text-align: center; font-weight: 700;">
                                    <?= $item['quantity'] ?>
                                </td>
                                <td style="padding: 12px 8px; text-align: right; font-weight: 800; color: #dc2626;">
                                    <?= number_format($item['total'], 0, ',', '.') ?> đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 14px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span>Tạm tính:</span>
                        <strong><?= number_format($order['subtotal'], 0, ',', '.') ?> VNĐ</strong>
                    </div>
                    <?php if (!empty($order['coupon_code'])): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #16a34a;">
                            <span>Mã giảm giá (<?= htmlspecialchars($order['coupon_code']) ?>):</span>
                            <strong>-<?= number_format($order['discount_amount'], 0, ',', '.') ?> VNĐ</strong>
                        </div>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; font-size: 16px; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
                        <strong>Tổng cộng:</strong>
                        <strong style="color: #dc2626; font-size: 18px;"><?= number_format($order['final_total'], 0, ',', '.') ?> VNĐ</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Form cập nhật trạng thái -->
        <div>
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: #0f172a;">Cập nhật đơn hàng</h3>

                <form action="<?= base_url('admin/orders/' . $order['id'] . '/status') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Trạng thái đơn hàng:</label>
                        <select name="status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: 700;">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xác nhận (pending)</option>
                            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận (confirmed)</option>
                            <option value="shipping" <?= $order['status'] === 'shipping' ? 'selected' : '' ?>>Đang giao hàng (shipping)</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Hoàn tất (completed)</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy (cancelled)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Trạng thái thanh toán:</label>
                        <select name="payment_status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: 700;">
                            <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Chưa thanh toán (Unpaid)</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Đã thanh toán (Paid)</option>
                        </select>
                    </div>

                    <button type="submit" style="width: 100%; background: #0284c7; color: #fff; font-weight: 800; padding: 12px; border: none; border-radius: 6px; cursor: pointer;">
                        LƯU CẬP NHẬT
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>