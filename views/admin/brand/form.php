<?php
require VIEWS_DIR . '/layouts/admin_header.php';
$isEdit = !empty($brand);
$action = $isEdit ? base_url('admin/brands/update/' . $brand['id']) : base_url('admin/brands/store');
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">
            <i class="fa-solid fa-copyright" style="color: #0284c7;"></i> <?= $isEdit ? 'Chỉnh sửa thương hiệu' : 'Thêm thương hiệu mới' ?>
        </h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">
            <?= $isEdit ? 'Cập nhật thương hiệu: ' . htmlspecialchars($brand['name']) : 'Thêm hãng sản xuất/đối tác phần cứng linh kiện máy tính' ?>
        </p>
    </div>

    <a href="<?= base_url('admin/brands') ?>" class="button button-outline" style="padding: 9px 18px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="admin-card" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; max-width: 600px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <form action="<?= $action ?>" method="POST">
        <?= csrf_field() ?>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Tên thương hiệu <span style="color: #dc2626;">*</span>
            </label>
            <input type="text" name="name" required value="<?= htmlspecialchars($brand['name'] ?? '') ?>" placeholder="Ví dụ: ASUS, MSI, GIGABYTE, Intel..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Đường dẫn tĩnh (Slug) <span style="font-weight: 400; color: #64748b; font-size: 12px;">(Để trống sẽ tự động sinh)</span>
            </label>
            <input type="text" name="slug" value="<?= htmlspecialchars($brand['slug'] ?? '') ?>" placeholder="asus" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Trạng thái</label>
            <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: #fff;">
                <option value="1" <?= ($brand['status'] ?? 1) == 1 ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= ($brand['status'] ?? 1) == 0 ? 'selected' : '' ?>>Tạm ẩn</option>
            </select>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="<?= base_url('admin/brands') ?>" class="button button-outline" style="padding: 10px 20px; border-radius: 6px; text-decoration: none;">Hủy</a>
            <button type="submit" class="button button-primary" style="background: #0284c7; color: #fff; padding: 10px 24px; border-radius: 6px; font-weight: 700; border: 0; cursor: pointer;">
                <i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Lưu thay đổi' : 'Tạo thương hiệu' ?>
            </button>
        </div>
    </form>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>

