<?php
require VIEWS_DIR . '/layouts/admin_header.php';
?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Quản lý tin tức & bài viết</h1>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Đăng tải tin tức công nghệ, bài viết tư vấn cấu hình và thủ thuật máy tính</p>
        </div>
        <a href="<?= base_url('admin/blogs/create') ?>" class="button button-primary" style="padding: 10px 18px; background: #0284c7; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> Viết bài mới
        </a>
    </div>

    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-size: 12.5px; text-transform: uppercase;">
                    <th style="padding: 12px 16px;">ID</th>
                    <th style="padding: 12px 16px;">Ảnh</th>
                    <th style="padding: 12px 16px;">Tiêu đề bài viết</th>
                    <th style="padding: 12px 16px;">Tác giả</th>
                    <th style="padding: 12px 16px; text-align: center;">Lượt xem</th>
                    <th style="padding: 12px 16px; text-align: center;">Trạng thái</th>
                    <th style="padding: 12px 16px;">Ngày đăng</th>
                    <th style="padding: 12px 16px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($blogs)): ?>
                    <tr>
                        <td colspan="8" style="padding: 36px; text-align: center; color: #64748b;">
                            Chưa có bài viết nào. Hãy bấm "Viết bài mới" để tạo bài đầu tiên.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($blogs as $b): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px; font-weight: 700; color: #64748b;"><?= (int)$b['id'] ?></td>
                            <td style="padding: 14px 16px;">
                                <img src="<?= htmlspecialchars(product_image_url($b['thumbnail'] ?? '')) ?>" alt="" width="56" height="38" style="object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;" onerror="this.onerror=null; this.src='<?= asset('images/product-placeholder.svg') ?>';">
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #0f172a; max-width: 320px;">
                                <a href="<?= base_url('tin-tuc/' . rawurlencode($b['slug'])) ?>" target="_blank" style="color: inherit; text-decoration: none;" title="Xem bài viết trên web">
                                    <?= htmlspecialchars($b['title']) ?> ↗
                                </a>
                            </td>
                            <td style="padding: 14px 16px; color: #334155;"><?= htmlspecialchars($b['author_name'] ?? 'Admin') ?></td>
                            <td style="padding: 14px 16px; text-align: center; color: #64748b;"><?= number_format((int)$b['view_count']) ?></td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <?php if ((int)$b['status'] === 1): ?>
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Xuất bản</span>
                                <?php else: ?>
                                    <span style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Bản nháp</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; color: #64748b; font-size: 12.5px;">
                                <?= date('d/m/Y', strtotime($b['created_at'])) ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center; white-space: nowrap;">
                                <a href="<?= base_url('admin/blogs/' . (int)$b['id'] . '/edit') ?>" style="display: inline-block; padding: 4px 10px; border: 1px solid #cbd5e1; border-radius: 4px; color: #0284c7; text-decoration: none; font-size: 12.5px; font-weight: 600; margin-right: 6px;">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <form action="<?= base_url('admin/blogs/' . (int)$b['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
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

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>

