<?php
require VIEWS_DIR . '/layouts/admin_header.php';
require VIEWS_DIR . '/layouts/admin_sidebar.php';
?>

<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Duyệt đánh giá & bình luận</h1>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Quản lý đánh giá sao và nhận xét của khách hàng trên sản phẩm</p>
        </div>
        <span style="font-size: 13px; color: #64748b; background: #f8fafc; padding: 6px 14px; border-radius: 20px; border: 1px solid #e2e8f0; font-weight: 700;">
            Tổng: <?= count($reviews) ?> đánh giá
        </span>
    </div>

    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-size: 12.5px; text-transform: uppercase;">
                    <th style="padding: 12px 16px;">ID</th>
                    <th style="padding: 12px 16px;">Sản phẩm</th>
                    <th style="padding: 12px 16px;">Người đánh giá</th>
                    <th style="padding: 12px 16px; text-align: center;">Số sao</th>
                    <th style="padding: 12px 16px;">Nội dung nhận xét</th>
                    <th style="padding: 12px 16px; text-align: center;">Trạng thái</th>
                    <th style="padding: 12px 16px;">Ngày gửi</th>
                    <th style="padding: 12px 16px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" style="padding: 36px; text-align: center; color: #64748b;">
                            Chưa có đánh giá nào từ khách hàng.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px; font-weight: 700; color: #64748b;"><?= (int)$r['id'] ?></td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #0284c7; max-width: 200px;">
                                <?= htmlspecialchars($r['product_name'] ?? 'Sản phẩm #' . $r['product_id']) ?>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 600; color: #0f172a;">
                                <?= htmlspecialchars($r['full_name'] ?? 'Khách hàng') ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center; color: #f59e0b; font-size: 14px;">
                                <?= str_repeat('★', (int)$r['rating']) ?><?= str_repeat('☆', 5 - (int)$r['rating']) ?>
                            </td>
                            <td style="padding: 14px 16px; color: #334155; max-width: 300px;">
                                <?= htmlspecialchars($r['comment']) ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <?php if ((int)$r['status'] === 1): ?>
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Hiển thị</span>
                                <?php else: ?>
                                    <span style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Đang ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; color: #64748b; font-size: 12.5px;">
                                <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center; white-space: nowrap;">
                                <form action="<?= base_url('admin/reviews/' . (int)$r['id'] . '/toggle') ?>" method="POST" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background: none; border: 1px solid #cbd5e1; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer; margin-right: 4px;">
                                        <?= (int)$r['status'] === 1 ? '<i class="fa-regular fa-eye-slash"></i> Ẩn' : '<i class="fa-regular fa-eye"></i> Hiện' ?>
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/reviews/' . (int)$r['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background: none; border: 1px solid #fee2e2; color: #ef4444; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
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
