<?php require VIEWS_DIR . '/layouts/admin_header.php'; ?>

<div class="content-wrapper" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0;">QUẢN LÝ MÃ GIẢM GIÁ</h1>
        <a href="<?= base_url('admin/coupons/create') ?>" style="background: #16a34a; color: #ffffff; padding: 10px 18px; border-radius: 6px; font-weight: 700; font-size: 14px; text-decoration: none;">
            + Thêm mã mới
        </a>
    </div>

    <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 700;">
                    <th style="padding: 14px 16px;">Mã Code</th>
                    <th style="padding: 14px 16px;">Mức giảm</th>
                    <th style="padding: 14px 16px;">Đơn tối thiểu</th>
                    <th style="padding: 14px 16px;">Lượt dùng</th>
                    <th style="padding: 14px 16px;">Thời gian hiệu lực</th>
                    <th style="padding: 14px 16px;">Trạng thái</th>
                    <th style="padding: 14px 16px; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($coupons)): ?>
                    <tr>
                        <td colspan="7" style="padding: 30px; text-align: center; color: #94a3b8;">Chưa có mã giảm giá nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($coupons as $c): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px;">
                                <strong style="font-size: 15px; color: #0284c7; background: #e0f2fe; padding: 4px 10px; border-radius: 4px; letter-spacing: 1px;">
                                    <?= htmlspecialchars($c['code'] ?? '') ?>
                                </strong>
                                <?php if (!empty($c['description'])): ?>
                                    <br><small style="color: #64748b;"><?= htmlspecialchars($c['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #dc2626;">
                                <?php 
                                    $val = floatval($c['discount_value'] ?? 0);
                                    $maxDisc = floatval($c['max_discount'] ?? 0);
                                    $type = $c['type'] ?? 'fixed';
                                ?>
                                <?php if ($type === 'percentage'): ?>
                                    Giảm <?= $val ?>%
                                    <?php if ($maxDisc > 0): ?>
                                        <br><small style="color: #64748b; font-weight: 400;">(Tối đa <?= number_format($maxDisc, 0, ',', '.') ?>đ)</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    Giảm <?= number_format($val, 0, ',', '.') ?> VNĐ
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; color: #334155;">
                                <?= number_format(floatval($c['min_order_amount'] ?? 0), 0, ',', '.') ?> đ
                            </td>
                            <td style="padding: 14px 16px;">
                                <strong><?= $c['used_count'] ?? 0 ?></strong> / <?= !empty($c['usage_limit']) ? $c['usage_limit'] : '∞' ?>
                            </td>
                            <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                                từ <strong><?= date('d/m/Y', strtotime($c['start_date'])) ?></strong><br>
                                đến <strong><?= date('d/m/Y', strtotime($c['end_date'])) ?></strong>
                            </td>
                            <td style="padding: 14px 16px;">
                                <?php if (($c['status'] ?? 1) == 1): ?>
                                    <span style="font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 12px; background: #dcfce7; color: #15803d;">Kích hoạt</span>
                                <?php else: ?>
                                    <span style="font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 12px; background: #fee2e2; color: #b91c1c;">Tắt</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="<?= base_url('admin/coupons/' . $c['id'] . '/edit') ?>" style="display: inline-block; background: #0284c7; color: #fff; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: 700; text-decoration: none; margin-right: 4px;">Sửa</a>
                                <form action="<?= base_url('admin/coupons/' . $c['id'] . '/delete') ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã này?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background: #dc2626; color: #fff; padding: 6px 12px; border: none; border-radius: 4px; font-size: 13px; font-weight: 700; cursor: pointer;">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>