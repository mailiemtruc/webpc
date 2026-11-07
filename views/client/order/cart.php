<?php
require VIEWS_DIR . '/layouts/client_header.php';
$cartItems = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
?>

<div class="page-width" style="padding: 30px 0 60px;">
    <div style="background: #ffffff; border-radius: 10px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <h1 style="font-size: 22px; font-weight: 800; margin-bottom: 20px; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
            <i class="fa-solid fa-cart-shopping" style="color: #0284c7;"></i> GIỎ HÀNG CỦA BẠN (<?= count($cartItems) ?> sản phẩm)
        </h1>

        <?php if (empty($cartItems)): ?>
            <div style="text-align: center; padding: 48px 0;">
                <i class="fa-solid fa-cart-plus" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px;"></i>
                <p style="font-size: 16px; color: #64748b; margin-bottom: 20px;">Giỏ hàng của bạn đang trống.</p>
                <a href="<?= base_url('san-pham') ?>" class="button button-primary" style="display: inline-block; padding: 10px 24px; border-radius: 6px; background: #0284c7; color: #fff; font-weight: 700; text-decoration: none;">
                    TIẾP TỤC MUA SẮM ↗
                </a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; font-size: 13px; color: #64748b;">
                                <th style="padding: 10px;">Sản phẩm</th>
                                <th style="padding: 10px; text-align: center;">Đơn giá</th>
                                <th style="padding: 10px; text-align: center;">Số lượng</th>
                                <th style="padding: 10px; text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $c): ?>
                                <?php 
                                    $itemThumb = !empty($c['thumbnail']) ? asset($c['thumbnail']) : asset('images/product-placeholder.svg');
                                    if (str_starts_with($c['thumbnail'] ?? '', 'http')) {
                                        $itemThumb = $c['thumbnail'];
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 10px; display: flex; align-items: center; gap: 14px;">
                                        <img src="<?= htmlspecialchars($itemThumb) ?>" alt="" width="60" height="60" style="object-fit: contain; border: 1px solid #e2e8f0; border-radius: 6px;">
                                        <div>
                                            <a href="<?= base_url('san-pham/' . rawurlencode($c['slug'])) ?>" style="font-size: 13.5px; font-weight: 700; color: #1e293b; text-decoration: none;">
                                                <?= htmlspecialchars($c['name']) ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td style="padding: 14px 10px; text-align: center; font-weight: 700; color: #dc2626;">
                                        <?= number_format($c['price'], 0, ',', '.') ?> đ
                                    </td>
                                    <td style="padding: 14px 10px; text-align: center; font-weight: 700;">
                                        <?= (int)$c['quantity'] ?>
                                    </td>
                                    <td style="padding: 14px 10px; text-align: right; font-weight: 800; color: #dc2626;">
                                        <?= number_format($c['price'] * $c['quantity'], 0, ',', '.') ?> đ
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; height: fit-content;">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: #1e293b;">TỔNG ĐƠN HÀNG</h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                        <span>Tạm tính:</span>
                        <strong><?= number_format($subtotal, 0, ',', '.') ?> VNĐ</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px;">
                        <span>Phí vận chuyển:</span>
                        <span style="color: #16a34a; font-weight: 700;">Miễn phí nội thành</span>
                    </div>
                    <div style="border-top: 2px dashed #cbd5e1; padding-top: 14px; display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 16px;">
                        <strong>Tổng thanh toán:</strong>
                        <strong style="color: #dc2626; font-size: 18px;"><?= number_format($subtotal, 0, ',', '.') ?> VNĐ</strong>
                    </div>
                    <a href="<?= base_url('thanh-toan') ?>" style="display: block; width: 100%; text-align: center; background: #dc2626; color: #fff; font-weight: 800; padding: 12px; border-radius: 6px; text-decoration: none; font-size: 15px; box-shadow: 0 4px 12px rgba(220,38,38,0.3);">
                        TIẾN HÀNH ĐẶT HÀNG ↗
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>
