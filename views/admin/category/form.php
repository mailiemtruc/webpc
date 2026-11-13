<?php
require VIEWS_DIR . '/layouts/admin_header.php';
$isEdit = !empty($category);
$action = $isEdit ? base_url('admin/categories/update/' . $category['id']) : base_url('admin/categories/store');
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">
            <i class="fa-solid fa-folder-tree" style="color: #0284c7;"></i> <?= $isEdit ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' ?>
        </h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">
            <?= $isEdit ? 'Cập nhật thông số danh mục: ' . htmlspecialchars($category['name']) : 'Tạo danh mục sản phẩm mới cho siêu thị máy tính' ?>
        </p>
    </div>

    <a href="<?= base_url('admin/categories') ?>" class="button button-outline" style="padding: 9px 18px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="admin-card" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; max-width: 700px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <form action="<?= $action ?>" method="POST">
        <?= csrf_field() ?>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Tên danh mục <span style="color: #dc2626;">*</span>
            </label>
            <input type="text" name="name" required value="<?= htmlspecialchars($category['name'] ?? '') ?>" placeholder="Ví dụ: PC Gaming, Màn hình máy tính..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Đường dẫn tĩnh (Slug) <span style="font-weight: 400; color: #64748b; font-size: 12px;">(Để trống sẽ tự động sinh)</span>
            </label>
            <input type="text" name="slug" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" placeholder="pc-gaming" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
            <div>
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Danh mục cha</label>
                <select name="parent_id" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: #fff;">
                    <option value="">-- Không có (Danh mục gốc) --</option>
                    <?php foreach ($parents as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($category['parent_id'] ?? null) == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Icon FontAwesome <span style="font-weight: 400; color: #64748b; font-size: 12px;">(vd: gamepad, desktop, tv)</span>
                </label>
                <input type="text" name="icon" value="<?= htmlspecialchars($category['icon'] ?? '') ?>" placeholder="gamepad" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Thứ tự hiển thị</label>
                <input type="number" name="display_order" value="<?= (int)($category['display_order'] ?? 0) ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Trạng thái</label>
                <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: #fff;">
                    <option value="1" <?= ($category['status'] ?? 1) == 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= ($category['status'] ?? 1) == 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="<?= base_url('admin/categories') ?>" class="button button-outline" style="padding: 10px 20px; border-radius: 6px; text-decoration: none;">Hủy</a>
            <button type="submit" class="button button-primary" style="background: #0284c7; color: #fff; padding: 10px 24px; border-radius: 6px; font-weight: 700; border: 0; cursor: pointer;">
                <i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Lưu thay đổi' : 'Tạo danh mục' ?>
            </button>
        </div>
    </form>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>

