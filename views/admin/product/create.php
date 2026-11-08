<?php
require VIEWS_DIR . '/layouts/admin_header.php';
?>

<div style="max-width: 980px; margin: 0 auto; padding-bottom: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <a href="<?= base_url('admin/products') ?>" style="color: #64748b; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách sản phẩm
            </a>
            <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">
                <i class="fa-solid fa-circle-plus" style="color: #0284c7;"></i> Thêm Sản phẩm & Cấu hình Mới
            </h1>
        </div>
    </div>

    <form action="<?= base_url('admin/products/store') ?>" method="POST" enctype="multipart/form-data" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- CỘT TRÁI: THÔNG TIN CƠ BẢN & CẤU HÌNH BỘ LỌC -->
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                    1. THÔNG TIN CHÍNH
                </h3>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Tên sản phẩm / Cấu hình PC <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="name" required placeholder="Ví dụ: PC GAMING PRO i5 14400F | 16GB DDR5 | RTX 5060 8GB" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Mã SKU</label>
                        <input type="text" name="sku" placeholder="Tự sinh nếu để trống (VD: PCM-GM-01)" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; font-family: monospace;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Đường dẫn Slug (URL)</label>
                        <input type="text" name="slug" placeholder="Tự sinh từ tên nếu để trống" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Danh mục hiển thị <span style="color: #dc2626;">*</span></label>
                        <select name="category_id" required style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Thương hiệu / Hãng</label>
                        <select name="brand_id" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                            <option value="">-- Chọn hãng --</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- 2. CẤU HÌNH CHUẨN HÓA CHO BỘ LỌC TÌM KIẾM -->
                <h3 style="font-size: 16px; font-weight: 800; color: #0284c7; margin: 0 0 16px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                    <i class="fa-solid fa-sliders"></i> 2. PHÂN LOẠI CẤU HÌNH VÀO BỘ LỌC (CLIENT FILTER)
                </h3>
                <p style="font-size: 12.5px; color: #64748b; margin-top: -8px; margin-bottom: 16px;">
                    Điền các thông số này để sản phẩm tự động hiển thị trong các bộ lọc CPU, RAM, VGA, Ổ cứng ngoài trang cửa hàng.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 4px; color: #334155;">Vi xử lý (CPU)</label>
                        <input list="cpuList" name="spec_cpu" placeholder="VD: Intel Core i5 14400F hoặc Ryzen 7 7800X3D" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        <datalist id="cpuList">
                            <option value="Intel Core Ultra 5">
                            <option value="Intel Core Ultra 7">
                            <option value="Intel Core Ultra 9">
                            <option value="Intel Core i3">
                            <option value="Intel Core i5">
                            <option value="Intel Core i7">
                            <option value="Intel Core i9">
                            <option value="AMD Ryzen 5">
                            <option value="AMD Ryzen 7">
                            <option value="AMD Ryzen 9">
                        </datalist>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 4px; color: #334155;">Bộ nhớ trong (RAM)</label>
                        <select name="spec_ram" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                            <option value="">-- Chọn dung lượng RAM --</option>
                            <option value="8GB">8GB DDR4 / DDR5</option>
                            <option value="16GB">16GB (2x8GB) DDR4 / DDR5</option>
                            <option value="32GB">32GB (2x16GB) DDR5 6000MHz</option>
                            <option value="64GB">64GB (2x32GB) DDR5</option>
                            <option value="128GB">128GB DDR5 Workstation</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 4px; color: #334155;">Card đồ họa (VGA)</label>
                        <input list="vgaList" name="spec_vga" placeholder="VD: GeForce RTX 5060 8GB OC" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        <datalist id="vgaList">
                            <option value="GeForce RTX 3050 6GB/8GB">
                            <option value="GeForce RTX 3060 12GB">
                            <option value="GeForce RTX 4060 8GB">
                            <option value="GeForce RTX 4070 12GB / Super">
                            <option value="GeForce RTX 5050 8GB">
                            <option value="GeForce RTX 5060 Ti 8GB/16GB">
                            <option value="GeForce RTX 5070 Ti 16GB">
                            <option value="GeForce RTX 5080 16GB">
                            <option value="GeForce RTX 5090 32GB">
                        </datalist>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 4px; color: #334155;">Ổ cứng lưu trữ (Storage)</label>
                        <input list="storageList" name="spec_storage" placeholder="VD: SSD 1TB M.2 PCIe Gen4 NVMe" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        <datalist id="storageList">
                            <option value="SSD 256GB NVMe">
                            <option value="SSD 512GB NVMe M.2">
                            <option value="SSD 1TB M.2 PCIe Gen4">
                            <option value="SSD 2TB Gen5 NVMe">
                        </datalist>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 4px; color: #334155;">Bo mạch chủ (Mainboard)</label>
                        <input type="text" name="spec_mainboard" placeholder="VD: ASUS TUF GAMING B760M" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 4px; color: #334155;">Nguồn & Tản nhiệt</label>
                        <input type="text" name="spec_psu" placeholder="VD: 650W 80 Plus / Tản AIO 240 ARGB" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>
                </div>

                <!-- TÓM TẮT -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Tóm tắt ngắn (Summary)</label>
                    <textarea name="summary" rows="2" placeholder="Dàn PC Gaming cân mượt mọi game Esport và đồ họa độ phân giải 2K..." style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;"></textarea>
                </div>
            </div>

            <!-- CỘT PHẢI: GIÁ, ẢNH & TRẠNG THÁI -->
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                    3. GIÁ & TỒN KHO
                </h3>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Giá niêm yết (VNĐ) <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="price" required placeholder="VD: 25.990.000" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; font-weight: 700; color: #1e293b;">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #dc2626;">Giá khuyến mãi HOT SALE (VNĐ)</label>
                    <input type="text" name="discount_price" placeholder="VD: 22.980.000 (để trống nếu không giảm)" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; font-weight: 700; color: #dc2626;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #334155;">Số lượng tồn kho</label>
                    <input type="number" name="quantity" value="15" min="0" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>

                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                    4. HÌNH ẢNH SẢN PHẨM
                </h3>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; color: #334155;">Tải tệp ảnh lên (Upload file)</label>
                    <input type="file" name="thumbnail_file" accept="image/*" style="width: 100%; font-size: 13px;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; margin-bottom: 6px; color: #334155;">Hoặc nhập Link URL ảnh</label>
                    <input type="text" name="thumbnail_url" placeholder="https://..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                </div>

                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                    5. TÙY CHỌN & HIỂN THỊ
                </h3>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="is_hot" value="1" checked style="width: 16px; height: 16px; accent-color: #dc2626;">
                        <strong>Bật cờ HOT SALE (Hiện ở khối Hot Sale)</strong>
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" checked style="width: 16px; height: 16px; accent-color: #0284c7;">
                        <span>Sản phẩm nổi bật</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="status" value="1" checked style="width: 16px; height: 16px; accent-color: #16a34a;">
                        <span>Kích hoạt bán ngay (Hiển thị ra ngoài)</span>
                    </label>
                </div>

                <button type="submit" style="width: 100%; background: #0284c7; color: #ffffff; padding: 12px; border: none; border-radius: 6px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(2,132,199,0.35);">
                    <i class="fa-solid fa-floppy-disk"></i> LƯU SẢN PHẨM MỚI
                </button>
            </div>
        </div>
    </form>
</div>

<?php require VIEWS_DIR . '/layouts/admin_footer.php'; ?>
