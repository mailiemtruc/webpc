<?php
require VIEWS_DIR . '/layouts/admin_header.php';
?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">Quản lý banner & slider</h1>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Quản lý hình ảnh quảng cáo và banner sự kiện trên trang chủ</p>
        </div>
        <button type="button" onclick="document.getElementById('add-banner-card').scrollIntoView({behavior: 'smooth'})" class="button button-primary" style="padding: 10px 18px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> Thêm banner mới
        </button>
    </div>

    <!-- Danh sách banner -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-size: 12.5px; text-transform: uppercase;">
                    <th style="padding: 12px 16px;">ID</th>
                    <th style="padding: 12px 16px;">Hình ảnh</th>
                    <th style="padding: 12px 16px;">Tiêu đề banner</th>
                    <th style="padding: 12px 16px;">Vị trí</th>
                    <th style="padding: 12px 16px; text-align: center;">Thứ tự</th>
                    <th style="padding: 12px 16px; text-align: center;">Trạng thái</th>
                    <th style="padding: 12px 16px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($banners)): ?>
                    <tr>
                        <td colspan="7" style="padding: 36px; text-align: center; color: #64748b;">
                            Chưa có banner nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($banners as $b): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px; font-weight: 700; color: #64748b;"><?= (int)$b['id'] ?></td>
                            <td style="padding: 14px 16px;">
                                <img src="<?= htmlspecialchars(product_image_url($b['image'] ?? '')) ?>" alt="" height="45" style="border-radius: 4px; border: 1px solid #e2e8f0; max-width: 120px; object-fit: cover;" onerror="this.onerror=null; this.src='<?= asset('images/product-placeholder.svg') ?>';">
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #0f172a;">
                                <?= htmlspecialchars($b['title']) ?>
                                <?php if (!empty($b['link'])): ?>
                                    <br><small><a href="<?= htmlspecialchars($b['link']) ?>" target="_blank" style="color: #0284c7; font-weight: 500; font-size: 11.5px;">Link liên kết ↗</a></small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="background: #f1f5f9; color: #334155; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    <?= htmlspecialchars($b['position']) ?>
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #475569;">
                                <?= (int)$b['display_order'] ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <?php if ((int)$b['status'] === 1): ?>
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Hiển thị</span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">Đang ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; text-align: center; white-space: nowrap;">
                                <form action="<?= base_url('admin/banners/' . (int)$b['id'] . '/toggle') ?>" method="POST" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" style="background: none; border: 1px solid #cbd5e1; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer; margin-right: 4px;">
                                        <?= (int)$b['status'] === 1 ? '<i class="fa-regular fa-eye-slash"></i> Ẩn' : '<i class="fa-regular fa-eye"></i> Hiện' ?>
                                    </button>
                                </form>
                                <form action="<?= base_url('admin/banners/' . (int)$b['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa banner này?');">
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

    <!-- Form Thêm banner mới -->
    <div id="add-banner-card" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-width: 800px;">
        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
            <i class="fa-solid fa-cloud-arrow-up" style="color: #0284c7;"></i> THÊM BANNER MỚI
        </h2>

        <form action="<?= base_url('admin/banners/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Tiêu đề banner (*)</label>
                    <input type="text" name="title" required placeholder="Ví dụ: Đại tiệc PC Gaming tháng 11" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Vị trí hiển thị</label>
                    <select name="position" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        <option value="home_main">Slider Chính (home_main)</option>
                        <option value="home_sub1">Banner phụ 1 (home_sub1)</option>
                        <option value="home_sub2">Banner phụ 2 (home_sub2)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">File ảnh banner (*)</label>
                    <input type="file" name="image" required accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Thứ tự hiển thị</label>
                    <input type="number" name="display_order" value="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Liên kết khi click (Tùy chọn)</label>
                <input type="url" name="link" placeholder="https://... hoặc /san-pham" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <button type="submit" class="button button-primary" style="padding: 10px 24px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                LƯU BANNER
            </button>
        </form>
    </div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>

