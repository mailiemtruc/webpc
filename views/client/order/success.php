<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<div class="page-width" style="padding: 40px 0 70px; text-align: center;">
    <div style="max-width: 650px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        
        <i class="fa-solid fa-circle-check" style="font-size: 60px; color: #16a34a; margin-bottom: 16px;"></i>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 8px;">ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p style="color: #64748b; font-size: 15px; margin-bottom: 24px;">Cảm ơn bạn đã lựa chọn mua sắm tại PCMarket. Mã đơn hàng của bạn là:</p>
        
        <div style="background: #f1f5f9; display: inline-block; padding: 10px 24px; border-radius: 8px; font-weight: 800; font-size: 20px; color: #0284c7; letter-spacing: 1px; margin-bottom: 24px;">
            <?= htmlspecialchars($order['order_code']) ?>
        </div>

        <!-- Chi tiết người nhận -->
        <div style="text-align: left; background: #f8fafc; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 24px; font-size: 14px;">
            <div style="margin-bottom: 6px;"><strong>Người nhận:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)</div>
            <div style="margin-bottom: 6px;"><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['shipping_address']) ?></div>
            <div style="margin-bottom: 6px;"><strong>Phương thức thanh toán:</strong> <?= strtoupper($order['payment_method']) ?></div>
            <div><strong>Tổng tiền thanh toán:</strong> <span style="color: #dc2626; font-weight: 800;"><?= number_format($order['final_total'], 0, ',', '.') ?> VNĐ</span></div>
        </div>

        <a href="<?= base_url('san-pham') ?>" style="display: inline-block; background: #0284c7; color: #fff; font-weight: 700; padding: 12px 28px; border-radius: 6px; text-decoration: none;">
            TIẾP TỤC MUA SẮM ↗
        </a>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>