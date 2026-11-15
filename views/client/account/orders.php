<?php require VIEWS_DIR . '/layouts/client_header.php'; ?>

<div class="page-width" style="padding: 30px 0 60px;">
    <div style="display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
        <!-- Sidebar tài khoản -->
        <div style="flex: 1; min-width: 260px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
            <div style="text-align: center; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; margin-bottom: 16px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 12px; font-weight: 700;">
                    <i class="fa-regular fa-user"></i>
                </div>
                <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 4px;"><?= htmlspecialchars(auth_user()['full_name'] ?? 'Khách hàng') ?></h2>
                <span style="font-size: 13px; color: #64748b;"><?= htmlspecialchars(auth_user()['email'] ?? '') ?></span>
            </div>
            <nav style="display: flex; flex-direction: column; gap: 6px;">
                <a href="<?= base_url('tai-khoan') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 6px; font-weight: 600; color: #334155; text-decoration: none;">
                    <i class="fa-regular fa-id-card"></i> Thông tin tài khoản
                </a>
                <a href="<?= base_url('tai-khoan/don-hang') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 6px; font-weight: 700; color: #0284c7; background: #f0f9ff; text-decoration: none;">
                    <i class="fa-solid fa-box-archive"></i> Đơn hàng đã mua
                </a>
                <a href="<?= base_url('tai-khoan/bao-mat') ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 6px; font-weight: 600; color: #334155; text-decoration: none;">
                    <i class="fa-solid fa-shield-halved"></i> Bảo mật &amp; 2FA
                </a>
            </nav>
        </div>

        <!-- Nội dung danh sách đơn hàng -->
        <div style="flex: 3; min-width: 320px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
            <h1 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                <i class="fa-solid fa-box-archive" style="color: #0284c7;"></i> ĐƠN HÀNG CỦA TÔI (<?= count($orders) ?> đơn)
            </h1>

            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 48px 0;">
                    <i class="fa-solid fa-bag-shopping" style="font-size: 44px; color: #cbd5e1; margin-bottom: 14px;"></i>
                    <p style="font-size: 15px; color: #64748b; margin-bottom: 16px;">Bạn chưa có đơn đặt hàng nào.</p>
                    <a href="<?= base_url('san-pham') ?>" class="button button-primary" style="display: inline-block; padding: 10px 20px; background: #0284c7; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 700;">
                        KHÁM PHÁ SẢN PHẨM ↗
                    </a>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b; font-size: 13px;">
                                <th style="padding: 10px;">Mã đơn</th>
                                <th style="padding: 10px;">Ngày đặt</th>
                                <th style="padding: 10px;">Tổng tiền</th>
                                <th style="padding: 10px;">Thanh toán</th>
                                <th style="padding: 10px;">Trạng thái</th>
                                <th style="padding: 10px; text-align: center;">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $statusBadge = match ($order['status']) {
                                    'completed'  => '<span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Hoàn thành</span>',
                                    'shipping'   => '<span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Đang giao</span>',
                                    'confirmed'  => '<span style="background: #fef9c3; color: #a16207; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Đã xác nhận</span>',
                                    'cancelled'  => '<span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Đã hủy</span>',
                                    default      => '<span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">Chờ xử lý</span>',
                                };
                                ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 10px; font-weight: 700; color: #0284c7;">
                                        <?= htmlspecialchars($order['order_code']) ?>
                                    </td>
                                    <td style="padding: 14px 10px; color: #64748b; font-size: 13px;">
                                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td style="padding: 14px 10px; font-weight: 800; color: #dc2626;">
                                        <?= number_format((float)$order['final_total'], 0, ',', '.') ?> VNĐ
                                    </td>
                                    <td style="padding: 14px 10px; font-size: 13px;">
                                        <?= strtoupper($order['payment_method']) ?>
                                        (<?= $order['payment_status'] === 'paid' ? '<span style="color:#16a34a;font-weight:700;">Đã trả</span>' : '<span style="color:#eab308;font-weight:700;">Chưa trả</span>' ?>)
                                    </td>
                                    <td style="padding: 14px 10px;">
                                        <?= $statusBadge ?>
                                    </td>
                                    <td style="padding: 14px 10px; text-align: center;">
                                        <a href="<?= base_url('dat-hang-thanh-cong?code=' . rawurlencode($order['order_code'])) ?>" style="color: #0284c7; text-decoration: none; font-weight: 700;" title="Xem chi tiết đơn">
                                            Xem <span aria-hidden="true">→</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/client_footer.php'; ?>

