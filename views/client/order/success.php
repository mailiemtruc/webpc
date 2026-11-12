<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<?php
$isBankingPending = ($order['payment_method'] ?? '') === 'banking' && ($order['payment_status'] ?? 'unpaid') === 'unpaid';
$transferAmount   = (int)round((float)($order['final_total'] ?? 0));
$transferNote     = $order['order_code'] ?? '';
$vietQrUrl        = 'https://img.vietqr.io/image/MB-0987654321-compact2.png'
    . '?amount=' . $transferAmount
    . '&addInfo=' . rawurlencode($transferNote)
    . '&accountName=' . rawurlencode('CONG TY PCMARKET');
?>

<div class="page-width" style="padding: 40px 0 70px; text-align: center;">
    <div style="max-width: 720px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        
        <i class="fa-solid fa-circle-check" style="font-size: 60px; color: #16a34a; margin-bottom: 16px;"></i>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 8px;">ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p style="color: #64748b; font-size: 15px; margin-bottom: 24px;">Cảm ơn bạn đã lựa chọn mua sắm tại PCMarket. Mã đơn hàng của bạn là:</p>
        
        <div style="background: #f1f5f9; display: inline-block; padding: 10px 24px; border-radius: 8px; font-weight: 800; font-size: 20px; color: #0284c7; letter-spacing: 1px; margin-bottom: 24px;">
            <?= htmlspecialchars($order['order_code']) ?>
        </div>

        <!-- Chi tiết người nhận -->
        <div style="text-align: left; background: #f8fafc; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; font-size: 14px; line-height: 1.7;">
            <div><strong>Người nhận:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)</div>
            <div><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['shipping_address']) ?></div>
            <div><strong>Phương thức thanh toán:</strong> <?= ($order['payment_method'] ?? '') === 'banking' ? 'Chuyển khoản Ngân hàng (VietQR)' : 'Thanh toán khi nhận hàng (COD)' ?></div>
            <div>
                <strong>Trạng thái thanh toán:</strong>
                <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; <?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? 'background:#dcfce7;color:#15803d;' : 'background:#fef3c7;color:#b45309;' ?>">
                    <?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán (Chờ xác nhận)' ?>
                </span>
            </div>
            <div><strong>Tổng tiền thanh toán:</strong> <span style="color: #dc2626; font-weight: 800;"><?= number_format((float)$order['final_total'], 0, ',', '.') ?> VNĐ</span></div>
        </div>

        <?php if ($isBankingPending): ?>
            <div style="text-align: left; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 20px; margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
                <div style="flex: 0 0 180px; text-align: center; background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #dbeafe;">
                    <img src="<?= htmlspecialchars($vietQrUrl) ?>" alt="Mã QR thanh toán VietQR" width="160" height="160" style="display: block; margin: 0 auto; object-fit: contain;">
                    <small style="display: block; margin-top: 6px; color: #475569; font-weight: 600;">Quét bằng app Ngân hàng</small>
                </div>
                <div style="flex: 1; min-width: 240px; font-size: 14px; color: #1e3a8a; line-height: 1.7;">
                    <h2 style="font-size: 15px; font-weight: 800; margin: 0 0 8px; color: #1d4ed8;">HƯỚNG DẪN CHUYỂN KHOẢN</h2>
                    <div><strong>Ngân hàng:</strong> MB Bank (Quân Đội)</div>
                    <div><strong>Số tài khoản:</strong> <code>0987654321</code></div>
                    <div><strong>Chủ tài khoản:</strong> CONG TY PCMARKET</div>
                    <div><strong>Số tiền:</strong> <strong style="color: #dc2626;"><?= number_format((float)$order['final_total'], 0, ',', '.') ?> VNĐ</strong></div>
                    <div><strong>Nội dung CK:</strong> <code><?= htmlspecialchars($transferNote) ?></code></div>
                    <p style="margin: 8px 0 0; font-size: 12.5px; color: #475569;">Đơn hàng đang ở trạng thái chờ đối soát và sẽ được xác nhận ngay sau khi kế toán nhận được tiền.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($order['items'])): ?>
            <div style="text-align: left; margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                <div style="background: #f8fafc; padding: 10px 14px; font-weight: 800; font-size: 13px; color: #334155; border-bottom: 1px solid #e2e8f0;">
                    CHI TIẾT SẢN PHẨM ĐÃ ĐẶT
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 14px; color: #1e293b; font-weight: 600;">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                    <span style="color: #64748b; font-weight: 400;">× <?= (int)$item['quantity'] ?></span>
                                </td>
                                <td style="padding: 10px 14px; text-align: right; font-weight: 700; color: #dc2626;">
                                    <?= number_format((float)$item['total'], 0, ',', '.') ?> VNĐ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!empty($order['coupon_code'])): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; color: #15803d;">
                                <td style="padding: 8px 14px;">Mã giảm giá (<?= htmlspecialchars($order['coupon_code']) ?>)</td>
                                <td style="padding: 8px 14px; text-align: right; font-weight: 700;">-<?= number_format((float)$order['discount_amount'], 0, ',', '.') ?> VNĐ</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a href="<?= base_url('san-pham') ?>" style="display: inline-block; background: #0284c7; color: #fff; font-weight: 700; padding: 12px 28px; border-radius: 6px; text-decoration: none;">
            TIẾP TỤC MUA SẮM ↗
        </a>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
