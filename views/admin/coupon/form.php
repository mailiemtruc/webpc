<?php require VIEWS_DIR . '/layouts/admin_header.php'; ?>

<?php $isEdit = !empty($coupon); ?>

<div class="content-wrapper" style="padding: 24px; max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="<?= base_url('admin/coupons') ?>" style="color: #64748b; text-decoration: none; font-weight: 700; font-size: 14px;">← Quay lại danh sách</a>
        <h1 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 8px;"><?= $isEdit ? 'CHỈNH SỬA MÃ GIẢM GIÁ' : 'THÊM MÃ GIẢM GIÁ MỚI' ?></h1>
    </div>

    <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form action="<?= $isEdit ? base_url('admin/coupons/' . $coupon['id'] . '/update') : base_url('admin/coupons/store') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Mã Coupon: <span style="color:red;">*</span></label>
                    <input type="text" name="code" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 700; text-transform: uppercase;" placeholder="VD: WELCOME50K">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Mô tả ngắn:</label>
                    <input type="text" name="description" value="<?= htmlspecialchars($coupon['description'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" placeholder="VD: Giảm 50k cho khách hàng mới">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Loại giảm giá:</label>
                    <select name="type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600;">
                        <option value="fixed" <?= ($coupon['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Số tiền cố định (fixed)</option>
                        <option value="percentage" <?= ($coupon['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Phần trăm (percentage)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Mức giảm (discount_value): <span style="color:red;">*</span></label>
                    <input type="number" name="discount_value" step="any" min="1" value="<?= $coupon['discount_value'] ?? '' ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600;" placeholder="Nhập số tiền hoặc %">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Đơn hàng tối thiểu (min_order_amount):</label>
                    <input type="number" name="min_order_amount" min="0" value="<?= $coupon['min_order_amount'] ?? 0 ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" placeholder="0 là áp dụng mọi đơn">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Giảm tối đa (max_discount):</label>
                    <input type="number" name="max_discount" min="1" value="<?= $coupon['max_discount'] ?? '' ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;" placeholder="Áp dụng khi chọn loại % (tùy chọn)">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Giới hạn lượt dùng:</label>
                    <input type="number" name="usage_limit" min="1" value="<?= $isEdit ? htmlspecialchars((string)($coupon['usage_limit'] ?? '')) : '100' ?>" placeholder="Để trống = Không giới hạn" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Ngày bắt đầu: <span style="color:red;">*</span></label>
                    <input type="date" name="start_date" value="<?= $coupon['start_date'] ?? date('Y-m-d') ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Ngày kết thúc: <span style="color:red;">*</span></label>
                    <input type="date" name="end_date" value="<?= $coupon['end_date'] ?? date('Y-m-d', strtotime('+30 days')) ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 6px;">Trạng thái (status):</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600;">
                    <option value="1" <?= ($coupon['status'] ?? 1) == 1 ? 'selected' : '' ?>>Kích hoạt (1)</option>
                    <option value="0" <?= ($coupon['status'] ?? 1) == 0 ? 'selected' : '' ?>>Khóa (0)</option>
                </select>
            </div>

            <button type="submit" style="background: #0284c7; color: #ffffff; font-weight: 800; font-size: 15px; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; width: 100%;">
                <?= $isEdit ? 'LƯU CẬP NHẬT' : 'TẠO MÃ GIẢM GIÁ' ?>
            </button>

        </form>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>