<?php
require VIEWS_DIR . '/layouts/client_header.php';
$cartItems = $cart ?? $_SESSION['cart'] ?? [];
$appliedCoupon = $_SESSION['coupon'] ?? null;

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}

$discountAmount = $appliedCoupon['discount_amount'] ?? 0;
$finalTotal = max(0, $subtotal - $discountAmount);
?>

<div class="page-width" style="padding: 30px 0 60px;">
    <div style="background: #ffffff; border-radius: 10px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <h1 style="font-size: 22px; font-weight: 800; margin-bottom: 20px; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
            <i class="fa-solid fa-cart-shopping" style="color: #0284c7;"></i> GIỎ HÀNG CỦA BẠN (<span id="cart-item-count"><?= count($cartItems) ?></span> sản phẩm)
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
                                <th style="padding: 10px; text-align: center; width: 100px;">Số lượng</th>
                                <th style="padding: 10px; text-align: right;">Thành tiền</th>
                                <th style="padding: 10px; text-align: center; width: 50px;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $c): ?>
                                <?php
                                    $itemThumb = product_image_url($c['thumbnail'] ?? '');
                                    $itemSubtotal = $c['price'] * $c['quantity'];
                                ?>
                                <tr id="cart-row-<?= $c['id'] ?>" style="border-bottom: 1px solid #f1f5f9;">
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
                                    <td style="padding: 14px 10px; text-align: center;">
                                        <input type="number" 
                                               class="cart-qty-input" 
                                               data-id="<?= $c['id'] ?>" 
                                               data-old-val="<?= (int)$c['quantity'] ?>" 
                                               value="<?= (int)$c['quantity'] ?>" 
                                               min="1" 
                                               style="width: 55px; text-align: center; padding: 5px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 700;">
                                    </td>
                                    <td class="item-subtotal-<?= $c['id'] ?>" style="padding: 14px 10px; text-align: right; font-weight: 800; color: #dc2626;">
                                        <?= number_format($itemSubtotal, 0, ',', '.') ?> đ
                                    </td>
                                    <td style="padding: 14px 10px; text-align: center;">
                                        <button type="button" class="btn-remove-item" data-id="<?= $c['id'] ?>" title="Xóa sản phẩm" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 5px;">
                                            <i class="fa-solid fa-trash-can" style="pointer-events: none;"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cột tóm tắt & Mã giảm giá -->
                <div style="background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; height: fit-content;">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: #1e293b;">TỔNG ĐƠN HÀNG</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                        <span>Tạm tính:</span>
                        <strong id="cart-subtotal"><?= number_format($subtotal, 0, ',', '.') ?> VNĐ</strong>
                    </div>

                    <!-- Khung Nhập Mã Giảm Giá -->
                    <div style="margin: 16px 0; padding-top: 12px; border-top: 1px dashed #cbd5e1;">
                        <label style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">Mã giảm giá:</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" id="coupon-code-input" placeholder="Nhập mã (VD: WELCOME50K)" value="<?= htmlspecialchars($appliedCoupon['code'] ?? '') ?>" style="flex: 1; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 700; text-transform: uppercase;">
                            <button type="button" id="btn-apply-coupon" style="background: #0284c7; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 700; cursor: pointer;">Áp dụng</button>
                        </div>
                        <div id="coupon-feedback" style="margin-top: 8px; font-size: 13px; font-weight: 600; display: none;"></div>
                    </div>

                    <!-- Dòng Giảm Giá (Hiển thị khi có mã) -->
                    <div id="coupon-discount-row" style="display: <?= $appliedCoupon ? 'flex' : 'none' ?>; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #16a34a;">
                        <span>Giảm giá (<strong id="applied-coupon-code"><?= htmlspecialchars($appliedCoupon['code'] ?? '') ?></strong>): <button type="button" id="btn-remove-coupon" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:12px; text-decoration:underline;">[Hủy]</button></span>
                        <strong id="cart-discount-amount">-<?= number_format($discountAmount, 0, ',', '.') ?> VNĐ</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px;">
                        <span>Phí vận chuyển:</span>
                        <span style="color: #16a34a; font-weight: 700;">Miễn phí nội thành</span>
                    </div>

                    <div style="border-top: 2px dashed #cbd5e1; padding-top: 14px; display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 16px;">
                        <strong>Tổng thanh toán:</strong>
                        <strong id="cart-grand-total" style="color: #dc2626; font-size: 18px;"><?= number_format($finalTotal, 0, ',', '.') ?> VNĐ</strong>
                    </div>

                    <a href="<?= base_url('thanh-toan') ?>" style="display: block; width: 100%; text-align: center; background: #dc2626; color: #fff; font-weight: 800; padding: 12px; border-radius: 6px; text-decoration: none; font-size: 15px; box-shadow: 0 4px 12px rgba(220,38,38,0.3);">
                        TIẾN HÀNH ĐẶT HÀNG ↗
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= asset('js/cart.js') ?>?v=2" defer></script>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>