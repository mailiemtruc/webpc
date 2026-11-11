<?php require VIEWS_DIR . '/layouts/admin_header.php'; ?>

<div class="content-wrapper" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0;">QUẢN LÝ ĐƠN HÀNG</h1>
    </div>

    <?php if (!empty($_SESSION['admin_success'])): ?>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-weight: 600;">
            <?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['admin_error'])): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-weight: 600;">
            <?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>

    <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 700;">
                    <th style="padding: 14px 16px;">Mã đơn</th>
                    <th style="padding: 14px 16px;">Khách hàng</th>
                    <th style="padding: 14px 16px;">Tổng tiền</th>
                    <th style="padding: 14px 16px;">Thanh toán</th>
                    <th style="padding: 14px 16px;">Trạng thái</th>
                    <th style="padding: 14px 16px;">Ngày đặt</th>
                    <th style="padding: 14px 16px; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" style="padding: 30px; text-align: center; color: #94a3b8;">Chưa có đơn hàng nào trong hệ thống.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px; font-weight: 800; color: #0284c7;">
                                <?= htmlspecialchars($o['order_code']) ?>
                            </td>
                            <td style="padding: 14px 16px;">
                                <strong><?= htmlspecialchars($o['customer_name']) ?></strong>
                                <br><small style="color: #64748b;"><?= htmlspecialchars($o['customer_phone']) ?></small>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #dc2626;">
                                <?= number_format($o['final_total'], 0, ',', '.') ?> đ
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 4px; <?= $o['payment_status'] === 'paid' ? 'background:#dcfce7; color:#15803d;' : 'background:#fef3c7; color:#b45309;' ?>">
                                    <?= $o['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                                </span>
                                <br><small style="color: #64748b;"><?= strtoupper($o['payment_method']) ?></small>
                            </td>
                            <td style="padding: 14px 16px;">
                                <?php
                                    $statusMap = [
                                        'pending'    => ['label' => 'Chờ xử lý', 'bg' => '#fef3c7', 'color' => '#b45309'],
                                        'processing' => ['label' => 'Đang xử lý', 'bg' => '#e0f2fe', 'color' => '#0369a1'],
                                        'completed'  => ['label' => 'Hoàn tất', 'bg' => '#dcfce7', 'color' => '#15803d'],
                                        'cancelled'  => ['label' => 'Đã hủy', 'bg' => '#fee2e2', 'color' => '#b91c1c']
                                    ];
                                    $st = $statusMap[$o['status']] ?? ['label' => $o['status'], 'bg' => '#f1f5f9', 'color' => '#475569'];
                                ?>
                                <span style="font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 12px; background: <?= $st['bg'] ?>; color: <?= $st['color'] ?>;">
                                    <?= $st['label'] ?>
                                </span>
                            </td>
                            <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                                <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="<?= base_url('admin/orders/' . $o['id']) ?>" style="display: inline-block; background: #0284c7; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none;">
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>