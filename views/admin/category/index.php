<?php
require VIEWS_DIR . '/layouts/admin_header.php';
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">
            <i class="fa-solid fa-folder-tree" style="color: #0284c7;"></i> Quản lý Danh mục sản phẩm
        </h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">
            Tổng cộng <strong><?= count($categories) ?></strong> danh mục trên hệ thống
        </p>
    </div>

    <a href="<?= base_url('admin/categories/create') ?>" class="button button-primary" style="background: #0284c7; color: #fff; padding: 10px 20px; border-radius: 6px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Thêm danh mục mới
    </a>
</div>

<div class="admin-card" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 700;">
                <th style="padding: 12px 16px; width: 60px;">ID</th>
                <th style="padding: 12px 16px;">Tên danh mục</th>
                <th style="padding: 12px 16px;">Đường dẫn (Slug)</th>
                <th style="padding: 12px 16px;">Cấp cha</th>
                <th style="padding: 12px 16px; text-align: center; width: 100px;">Thứ tự</th>
                <th style="padding: 12px 16px; text-align: center; width: 120px;">Sản phẩm</th>
                <th style="padding: 12px 16px; text-align: center; width: 120px;">Trạng thái</th>
                <th style="padding: 12px 16px; text-align: right; width: 130px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="8" style="padding: 30px; text-align: center; color: #64748b;">Chưa có danh mục nào trên hệ thống.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <td style="padding: 12px 16px; color: #64748b; font-weight: 600;"><?= (int)$cat['id'] ?></td>
                        <td style="padding: 12px 16px; font-weight: 700; color: #0f172a;">
                            <?php if (!empty($cat['icon'])): ?>
                                <i class="fa-solid fa-<?= htmlspecialchars($cat['icon']) ?>" style="color: #0284c7; width: 18px; margin-right: 6px;"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($cat['name']) ?>
                        </td>
                        <td style="padding: 12px 16px; color: #64748b; font-family: monospace; font-size: 12.5px;">
                            <?= htmlspecialchars($cat['slug']) ?>
                        </td>
                        <td style="padding: 12px 16px; color: #475569;">
                            <?= $cat['parent_name'] ? htmlspecialchars($cat['parent_name']) : '<span style="color:#94a3b8; font-style:italic;">(Cấp cao nhất)</span>' ?>
                        </td>
                        <td style="padding: 12px 16px; text-align: center; color: #475569;">
                            <?= (int)$cat['display_order'] ?>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <span style="background: #eff6ff; color: #1d4ed8; font-weight: 700; padding: 2px 8px; border-radius: 999px; font-size: 12px;">
                                <?= (int)$cat['product_count'] ?> máy
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <?php if ((int)$cat['status'] === 1): ?>
                                <span style="background: #dcfce7; color: #15803d; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700;">Hiển thị</span>
                            <?php else: ?>
                                <span style="background: #fee2e2; color: #b91c1c; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700;">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px; align-items: center;">
                                <a href="<?= base_url('admin/categories/edit/' . $cat['id']) ?>" title="Sửa" style="color: #0284c7; padding: 4px 8px; border-radius: 4px; background: #f0f9ff; text-decoration: none;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="<?= base_url('admin/categories/delete/' . $cat['id']) ?>" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" title="Xóa" style="border: 0; background: #fee2e2; color: #dc2626; padding: 5px 8px; border-radius: 4px; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>

