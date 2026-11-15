<?php
require VIEWS_DIR . '/layouts/admin_header.php';
require VIEWS_DIR . '/layouts/admin_sidebar.php';

$isEdit = !empty($blog['id']);
$actionUrl = $isEdit ? base_url('admin/blogs/' . (int)$blog['id'] . '/update') : base_url('admin/blogs/store');
?>

<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                <?= $isEdit ? 'Chỉnh sửa bài viết' : 'Viết bài mới' ?>
            </h1>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Quản lý nội dung bài viết và hình ảnh minh họa</p>
        </div>
        <a href="<?= base_url('admin/blogs') ?>" style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600;">
            ← Quay lại danh sách
        </a>
    </div>

    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-width: 900px;">
        <form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Tiêu đề bài viết (*)</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($blog['title'] ?? '') ?>" placeholder="Ví dụ: Top 5 cấu hình PC Gaming đáng mua nhất 2026" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Đường dẫn tĩnh (Slug - Để trống để tự tạo)</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($blog['slug'] ?? '') ?>" placeholder="top-5-cau-hinh-pc-gaming-2026" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-family: monospace;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Ảnh đại diện (*)</label>
                <?php if (!empty($blog['thumbnail'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?= htmlspecialchars(product_image_url($blog['thumbnail'])) ?>" alt="Ảnh hiện tại" style="max-height: 120px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span style="display: block; font-size: 12px; color: #64748b; margin-top: 4px;">Ảnh hiện tại (Chọn tệp mới để thay thế)</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" accept="image/*" <?= $isEdit ? '' : 'required' ?> style="width: 100%; padding: 8px; border: 1px dashed #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 13.5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Tóm tắt ngắn (Summary)</label>
                <textarea name="summary" rows="3" placeholder="Đoạn văn ngắn gọn xuất hiện ở danh sách tin tức..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; resize: vertical;"><?= htmlspecialchars($blog['summary'] ?? '') ?></textarea>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Nội dung chi tiết (*)</label>
                <textarea name="content" required rows="12" placeholder="Nhập nội dung bài viết (hỗ trợ các thẻ HTML cơ bản như <p>, <h3>, <ul>, <li>, <strong>, <em>, <img>)..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-family: sans-serif; resize: vertical;"><?= htmlspecialchars($blog['content'] ?? '') ?></textarea>
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: #334155; cursor: pointer;">
                    <input type="checkbox" name="status" value="1" <?= (!isset($blog['status']) || (int)$blog['status'] === 1) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    Xuất bản ngay bài viết này
                </label>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="button button-primary" style="padding: 10px 24px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                    <?= $isEdit ? 'CẬP NHẬT BÀI VIẾT' : 'ĐĂNG BÀI VIẾT' ?>
                </button>
                <a href="<?= base_url('admin/blogs') ?>" style="padding: 10px 20px; background: #f1f5f9; color: #475569; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>
