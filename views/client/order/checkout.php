<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<div class="page-width" style="padding: 30px 0 60px;">
    <h1 style="font-size: 22px; font-weight: 800; margin-bottom: 24px; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
        <i class="fa-solid fa-credit-card" style="color: #0284c7;"></i> THÔNG TIN THANH TOÁN
    </h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('thanh-toan/process') ?>" method="POST">
        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 28px;">
            
            <!-- Cột trái: Thông tin nhận hàng -->
            <div style="background: #ffffff; border-radius: 8px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 18px; color: #0f172a;">1. THÔNG TIN GIAO HÀNG</h3>
                
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Họ và tên người nhận (*)</label>
                    <input type="text" name="customer_name" required value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Nguyễn Văn A" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Số điện thoại (*)</label>
                        <input type="tel" name="customer_phone" required value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0901234567" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Email (Nhận hóa đơn)</label>
                        <input type="email" name="customer_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="email@example.com" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Địa chỉ nhận hàng chi tiết (*)</label>
                    <textarea name="shipping_address" required rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; resize: vertical;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #475569;">Ghi chú đơn hàng (Tùy chọn)</label>
                    <input type="text" name="note" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>

                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 14px; color: #0f172a; padding-top: 14px; border-top: 1px solid #f1f5f9;">2. HÌNH THỨC THANH TOÁN</h3>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <div>
                            <strong style="display: block; font-size: 14px;">Thanh toán khi nhận hàng (COD)</strong>
                            <small style="color: #64748b;">Trả tiền mặt trực tiếp cho nhân viên giao hàng</small>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;">
                        <input type="radio" name="payment_method" value="banking">
                        <div>
                            <strong style="display: block; font-size: 14px;">Chuyển khoản Ngân hàng (QR Code)</strong>
                            <small style="color: #64748b;">Quét mã VietQR chuyển khoản nhanh 24/7</small>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Cột phải: Tóm tắt danh sách sản phẩm -->
            <div style="background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; height: fit-content;">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: #1e293b;">ĐƠN HÀNG CỦA BẠN</h3>

                <div style="max-height: 280px; overflow-y: auto; margin-bottom: 16px; padding-right: 4px;">
                    <?php foreach ($cart as $item): ?>
                        <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                            <img src="<?= asset($item['thumbnail']) ?>" width="48" height="48" style="object-fit: contain; border-radius: 4px; border: 1px solid #cbd5e1;">
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 700; color: #1e293b;" class="text-line-2"><?= htmlspecialchars($item['name']) ?></div>
                                <small style="color: #64748b;"><?= number_format($item['price'], 0, ',', '.') ?> đ × <?= $item['quantity'] ?></small>
                            </div>
                            <div style="font-weight: 700; font-size: 13px; color: #dc2626;">
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                    <span>Tạm tính:</span>
                    <strong><?= number_format($subtotal, 0, ',', '.') ?> VNĐ</strong>
                </div>

                <?php if (!empty($coupon)): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #16a34a;">
                        <span>Giảm giá (<?= htmlspecialchars($coupon['code']) ?>):</span>
                        <strong>-<?= number_format($discountAmount, 0, ',', '.') ?> VNĐ</strong>
                    </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 14px;">
                    <span>Phí vận chuyển:</span>
                    <span style="color: #16a34a; font-weight: 700;">Miễn phí</span>
                </div>

                <div style="border-top: 2px dashed #cbd5e1; padding-top: 14px; display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 16px;">
                    <strong>Tổng thanh toán:</strong>
                    <strong style="color: #dc2626; font-size: 18px;"><?= number_format($finalTotal, 0, ',', '.') ?> VNĐ</strong>
                </div>

                <button type="submit" style="width: 100%; background: #dc2626; color: #fff; font-weight: 800; padding: 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; box-shadow: 0 4px 12px rgba(220,38,38,0.3);">
                    XÁC NHẬN ĐẶT HÀNG ↗
                </button>
            </div>

        </div>
    </form>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>