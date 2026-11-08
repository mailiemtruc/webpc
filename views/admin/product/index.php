<?php
require VIEWS_DIR . '/layouts/admin_header.php';
$selCat = (int)($_GET['category_id'] ?? 0);
$selBrand = (int)($_GET['brand_id'] ?? 0);
$selStatus = $_GET['status'] ?? '';
$searchQ = $_GET['q'] ?? '';
?>

<div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">
            <i class="fa-solid fa-boxes-stacked" style="color: #0284c7;"></i> Quản lý Sản phẩm & Kho hàng
        </h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">
            Tổng cộng <strong><?= number_format($total) ?></strong> sản phẩm trên hệ thống
        </p>
    </div>

    <a href="<?= base_url('admin/products/create') ?>" class="button button-primary" style="background: #0284c7; color: #fff; padding: 10px 20px; border-radius: 6px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
    </a>
</div>

<!-- BỘ LỌC TÌM KIẾM DÀNH CHO ADMIN -->
<div class="admin-card" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 18px; margin-bottom: 20px;">
    <form action="<?= base_url('admin/products') ?>" method="GET" style="display: grid; grid-template-columns: 2fr 1.5fr 1.5fr 1fr auto; gap: 12px; align-items: end;">
        <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; color: #475569;">Tìm tên hoặc mã SKU</label>
            <input type="text" name="q" value="<?= htmlspecialchars($searchQ) ?>" placeholder="Nhập tên sản phẩm, mã SKU..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
        </div>

        <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; color: #475569;">Danh mục</label>
            <select name="category_id" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                <option value="">-- Tất cả danh mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $selCat === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; color: #475569;">Thương hiệu</label>
            <select name="brand_id" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                <option value="">-- Tất cả thương hiệu --</option>
                <?php foreach ($brands as $br): ?>
                    <option value="<?= $br['id'] ?>" <?= $selBrand === (int)$br['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($br['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; color: #475569;">Trạng thái</label>
            <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                <option value="">-- Tất cả --</option>
                <option value="1" <?= $selStatus === '1' ? 'selected' : '' ?>>Đang bán</option>
                <option value="0" <?= $selStatus === '0' ? 'selected' : '' ?>>Tạm ẩn</option>
            </select>
        </div>

        <div>
            <button type="submit" style="background: #1e293b; color: #fff; padding: 9px 18px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-magnifying-glass"></i> Lọc
            </button>
        </div>
    </form>
</div>

<!-- BẢNG DANH SÁCH SẢN PHẨM -->
<div class="admin-card" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 12.5px; color: #475569; text-transform: uppercase;">
                <th style="padding: 12px 16px; width: 60px;">ID</th>
                <th style="padding: 12px 16px; width: 80px;">Ảnh</th>
                <th style="padding: 12px 16px;">Tên sản phẩm & SKU</th>
                <th style="padding: 12px 16px;">Danh mục / Hãng</th>
                <th style="padding: 12px 16px; text-align: right;">Giá bán</th>
                <th style="padding: 12px 16px; text-align: center;">Tồn kho</th>
                <th style="padding: 12px 16px; text-align: center;">Trạng thái</th>
                <th style="padding: 12px 16px; text-align: right; width: 120px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" style="padding: 36px; text-align: center; color: #94a3b8;">
                        Không có sản phẩm nào phù hợp với bộ lọc tìm kiếm.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <?php
                        $thumb = !empty($p['thumbnail']) ? asset($p['thumbnail']) : asset('images/product-placeholder.svg');
                        if (str_starts_with($p['thumbnail'] ?? '', 'http')) $thumb = $p['thumbnail'];
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; font-size: 13.5px; transition: background 0.15s;">
                        <td style="padding: 12px 16px; font-weight: 700; color: #94a3b8;">#<?= $p['id'] ?></td>
                        <td style="padding: 12px 16px;">
                            <img src="<?= htmlspecialchars($thumb) ?>" alt="" width="50" height="50" style="object-fit: contain; border-radius: 4px; border: 1px solid #e2e8f0;">
                        </td>
                        <td style="padding: 12px 16px;">
                            <a href="<?= base_url('san-pham/' . rawurlencode($p['slug'])) ?>" target="_blank" style="font-weight: 700; color: #1e293b; text-decoration: none; display: block; margin-bottom: 2px;">
                                <?= htmlspecialchars($p['name']) ?>
                            </a>
                            <span style="font-size: 11.5px; color: #64748b; font-family: monospace;">SKU: <?= htmlspecialchars($p['sku']) ?></span>
                            <?php if (!empty($p['is_hot'])): ?>
                                <span style="background: #fee2e2; color: #dc2626; font-size: 10.5px; font-weight: 800; padding: 2px 6px; border-radius: 3px; margin-left: 6px;">HOT SALE</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px 16px;">
                            <span style="display: block; font-weight: 600; color: #0284c7;"><?= htmlspecialchars($p['category_name'] ?? 'Chưa phân loại') ?></span>
                            <span style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($p['brand_name'] ?? 'OEM') ?></span>
                        </td>
                        <td style="padding: 12px 16px; text-align: right;">
                            <span style="display: block; font-weight: 800; color: #dc2626;">
                                <?= number_format(!empty($p['discount_price']) ? $p['discount_price'] : $p['price'], 0, ',', '.') ?> đ
                            </span>
                            <?php if (!empty($p['discount_price'])): ?>
                                <span style="font-size: 11.5px; color: #94a3b8; text-decoration: line-through;">
                                    <?= number_format($p['price'], 0, ',', '.') ?> đ
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <span style="font-weight: 700; color: <?= (int)$p['quantity'] > 5 ? '#16a34a' : '#ea580c' ?>;">
                                <?= (int)$p['quantity'] ?>
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <?php if ((int)$p['status'] === 1): ?>
                                <span style="background: #dcfce7; color: #15803d; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">Đang bán</span>
                            <?php else: ?>
                                <span style="background: #f1f5f9; color: #64748b; font-size: 11.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">Tạm ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px 16px; text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                <a href="<?= base_url('admin/products/edit/' . $p['id']) ?>" title="Chỉnh sửa" style="padding: 6px 10px; background: #e0f2fe; color: #0284c7; border-radius: 4px; text-decoration: none; font-size: 13px;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="<?= base_url('admin/products/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" title="Xóa" style="padding: 6px 10px; background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- PHÂN TRANG ADMIN -->
    <?php if ($totalPages > 1): ?>
        <div style="padding: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: center; gap: 6px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php $pageUrl = base_url('admin/products?' . http_build_query(array_merge($params, ['page' => $i]))); ?>
                <a href="<?= $pageUrl ?>" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 4px; text-decoration: none; font-weight: 700; font-size: 13px; <?= $i === (int)$page ? 'background: #0284c7; color: #fff;' : 'background: #fff; border: 1px solid #cbd5e1; color: #334155;' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>
