-- ========================================================
-- DỮ LIỆU MẪU (SEEDERS) CHO DỰ ÁN WEBPC
-- Môn: Lập trình Web - UTH
-- Tương thích hoàn toàn với schema.sql & Mô hình siêu thị PC thực tế
-- ========================================================

USE `webpc_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. SEED TÀI KHOẢN NGƯỜI DÙNG (USERS)
TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `avatar`, `status`, `created_at`) VALUES
(1, 'Quản Trị Viên (Admin)', 'admin@pcmart.vn', '0901234567', '$2y$10$MvuJB1x63HA3xDtZWVO0kumVgCUVsPv7GR4eB4DrG.oZtTA3TOuQi', 'admin', 'assets/images/admin-avatar.png', 1, NOW()),
(2, 'Nhân Viên Bán Hàng', 'staff@pcmart.vn', '0907654321', '$2y$10$MvuJB1x63HA3xDtZWVO0kumVgCUVsPv7GR4eB4DrG.oZtTA3TOuQi', 'staff', 'assets/images/staff-avatar.png', 1, NOW()),
(3, 'Nguyễn Văn Khách', 'customer@pcmart.vn', '0988889999', '$2y$10$jR/m6NHNjPbaLrywknKgB.Y7JXRnAoiE4oW4qj7SugwrCsjU862Ku', 'customer', 'assets/images/default-avatar.png', 1, NOW()),
(4, 'Trần Minh Tuấn', 'tuan.tran@gmail.com', '0912345678', '$2y$10$jR/m6NHNjPbaLrywknKgB.Y7JXRnAoiE4oW4qj7SugwrCsjU862Ku', 'customer', 'assets/images/default-avatar.png', 1, NOW());

-- 2. SEED DANH MỤC (CATEGORIES)
TRUNCATE TABLE `categories`;
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `icon`, `display_order`, `status`) VALUES
(1, NULL, 'PC Gaming', 'pc-gaming', 'fa-gamepad', 1, 1),
(2, NULL, 'PC Đồ Họa & Render', 'pc-do-hoa', 'fa-desktop', 2, 1),
(3, NULL, 'PC Văn Phòng & Học Tập', 'pc-van-phong', 'fa-briefcase', 3, 1),
(4, NULL, 'Linh Kiện Máy Tính', 'linh-kien-may-tinh', 'fa-microchip', 4, 1),
(5, 4, 'Vi Xử Lý (CPU)', 'cpu-vi-xu-ly', 'fa-microchip', 1, 1),
(6, 4, 'Bo Mạch Chủ (Mainboard)', 'mainboard-bo-mach-chu', 'fa-server', 2, 1),
(7, 4, 'Card Màn Hình (VGA)', 'vga-card-man-hinh', 'fa-vr-cardboard', 3, 1),
(8, 4, 'Bộ Nhớ Trong (RAM)', 'ram-bo-nho-trong', 'fa-memory', 4, 1),
(9, 4, 'Ổ Cứng SSD/HDD', 'o-cung-ssd-hdd', 'fa-hdd', 5, 1),
(10, 4, 'Nguồn Máy Tính (PSU)', 'nguon-may-tinh-psu', 'fa-bolt', 6, 1),
(11, 4, 'Vỏ Case Máy Tính', 'vo-case-may-tinh', 'fa-box', 7, 1),
(12, 4, 'Tản Nhiệt (Cooling)', 'tan-nhiet-cooling', 'fa-fan', 8, 1),
(13, NULL, 'Màn Hình Máy Tính', 'man-hinh-may-tinh', 'fa-tv', 5, 1),
(14, NULL, 'Bàn Phím & Chuột (Gear)', 'gaming-gear', 'fa-keyboard', 6, 1),
(15, NULL, 'PC Workstation', 'pc-workstation', 'fa-calculator', 7, 1),
(16, NULL, 'PC AMD Gaming', 'pc-amd-gaming', 'fa-fire', 8, 1),
(17, NULL, 'PC AI - Trí Tuệ Nhân Tạo', 'pc-ai-tri-tue-nhan-tao', 'fa-brain', 9, 1);

-- 3. SEED THƯƠNG HIỆU (BRANDS)
TRUNCATE TABLE `brands`;
INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `status`) VALUES
(1, 'ASUS', 'asus', 'assets/images/brands/asus.png', 1),
(2, 'MSI', 'msi', 'assets/images/brands/msi.png', 1),
(3, 'GIGABYTE', 'gigabyte', 'assets/images/brands/gigabyte.png', 1),
(4, 'INTEL', 'intel', 'assets/images/brands/intel.png', 1),
(5, 'AMD', 'amd', 'assets/images/brands/amd.png', 1),
(6, 'CORSAIR', 'corsair', 'assets/images/brands/corsair.png', 1),
(7, 'KINGSTON', 'kingston', 'assets/images/brands/kingston.png', 1),
(8, 'DEEPCOOL', 'deepcool', 'assets/images/brands/deepcool.png', 1),
(9, 'LIAN LI', 'lian-li', 'assets/images/brands/lian-li.png', 1),
(10, 'ZOTAC', 'zotac', 'assets/images/brands/zotac.png', 1),
(11, 'COLORFUL', 'colorful', 'assets/images/brands/colorful.png', 1),
(12, 'AOC', 'aoc', 'assets/images/brands/aoc.png', 1),
(13, 'LG', 'lg', 'assets/images/brands/lg.png', 1),
(14, 'SSTC', 'sstc', 'assets/images/brands/sstc.png', 1);

-- 4. SEED SẢN PHẨM (PRODUCTS - 48 cấu hình và linh kiện chuẩn siêu thị PC)
TRUNCATE TABLE `products`;
INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `sku`, `price`, `discount_price`, `quantity`, `thumbnail`, `summary`, `specifications`, `is_featured`, `is_hot`, `status`, `view_count`, `created_at`) VALUES
-- 1. HOT SALE
(1, 1, 4, 'PC ULTRA GAMING I5 14400F - RTX 5050 8GB', 'pc-ultra-gaming-i5-14400f-rtx-5050-8gb', 'PCM-HOT-01', 25990000.00, 22980000.00, 20, 'assets/images/products/hotsale_pc_1.jpg', 'Cấu hình Gaming thế hệ mới 2026 trang bị RTX 5050 và Intel Gen 14th.', '{\"cpu\":\"Intel Core i5 14400F\",\"ram\":\"16GB DDR5 5600MHz\",\"vga\":\"GeForce RTX 5050 8GB\",\"storage\":\"SSD 512GB NVMe Gen4\"}', 1, 1, 1, 1250, NOW()),
(2, 16, 5, 'PC AMD GAMING PRO Ryzen 7 5700X - RTX 5050 8GB OC', 'pc-amd-gaming-pro-ryzen-7-5700x-rtx-5050-8gb-oc', 'PCM-HOT-02', 22990000.00, 22280000.00, 15, 'assets/images/products/hotsale_pc_2.jpg', 'Chiến Delta Force, Valorant và AAA mượt mà với 8 nhân 16 luồng.', '{\"cpu\":\"AMD Ryzen 7 5700X\",\"ram\":\"16GB DDR4 3200MHz\",\"vga\":\"GeForce RTX 5050 8GB OC\",\"storage\":\"SSD 512GB NVMe\"}', 1, 1, 1, 980, NOW()),
(3, 1, 4, 'PC ULTRA GAMING I5 12400F - RTX 5050 8GB', 'pc-ultra-gaming-i5-12400f-rtx-5050-8gb', 'PCM-HOT-03', 23900000.00, 22480000.00, 25, 'assets/images/products/hotsale_pc_3.jpg', 'Cấu hình quốc dân tối ưu chi phí nhưng sở hữu sức mạnh đồ họa đỉnh cao.', '{\"cpu\":\"Intel Core i5 12400F\",\"ram\":\"16GB DDR4 3200MHz\",\"vga\":\"GeForce RTX 5050 8GB\",\"storage\":\"SSD 512GB NVMe\"}', 1, 1, 1, 840, NOW()),
(4, 16, 5, 'PC AMD Ryzen 5 5500 - RTX 5050 8GB OC', 'pc-amd-ryzen-5-5500-rtx-5050-8gb-oc', 'PCM-HOT-04', 21990000.00, 20480000.00, 18, 'assets/images/products/hotsale_pc_4.jpg', 'Mức giá hấp dẫn cho bộ máy trang bị card đồ họa thế hệ mới.', '{\"cpu\":\"AMD Ryzen 5 5500\",\"ram\":\"16GB DDR4 3200MHz\",\"vga\":\"GeForce RTX 5050 8GB OC\",\"storage\":\"SSD 512GB NVMe\"}', 1, 1, 1, 720, NOW()),
(5, 16, 5, 'PC AMD GAMING PRO Ryzen 7 5700X - RTX 3050 8GB DUAL', 'pc-amd-gaming-pro-ryzen-7-5700x-rtx-3050-8gb-dual', 'PCM-HOT-05', 19990000.00, 18280000.00, 12, 'assets/images/products/hotsale_pc_5.jpg', 'Tối ưu cho sinh viên ngành thiết kế và game thủ Esport.', '{\"cpu\":\"AMD Ryzen 7 5700X\",\"ram\":\"16GB DDR4 3200MHz\",\"vga\":\"GeForce RTX 3050 8GB DUAL\",\"storage\":\"SSD 512GB NVMe\"}', 1, 1, 1, 650, NOW()),
(6, 16, 5, 'PC AMD GAMING Ryzen 7 5700X - RTX 3050 6GB TWIN X2', 'pc-amd-gaming-ryzen-7-5700x-rtx-3050-6gb-twin-x2', 'PCM-HOT-06', 17990000.00, 16860000.00, 14, 'assets/images/products/hotsale_pc_6.jpg', 'Dàn máy chiến game phân khúc cận phổ thông giá cực tốt.', '{\"cpu\":\"AMD Ryzen 7 5700X\",\"ram\":\"16GB DDR4 3200MHz\",\"vga\":\"GeForce RTX 3050 6GB TWIN X2\",\"storage\":\"SSD 512GB NVMe\"}', 1, 1, 1, 590, NOW()),

-- 2. PC GAMING, STREAMING
(7, 1, 1, 'PC ULTRA GAMING I5 14400F - RTX 5050 8GB GAMING X', 'pc-ultra-gaming-i5-14400f-rtx-5050-8gb-gaming-x', 'PCM-GM-01', 25990000.00, 22980000.00, 10, 'assets/images/products/pc_gaming_1.jpg', 'Bộ máy livestream và gaming mượt mà độ phân giải 2K.', '{\"cpu\":\"Intel Core i5 14400F\",\"ram\":\"16GB DDR5\",\"vga\":\"RTX 5050 8GB\",\"storage\":\"SSD 512GB Gen4\"}', 1, 0, 1, 950, NOW()),
(8, 1, 2, 'PC AMD GAMING PRO Ryzen 7 5700X - RTX 5050 8GB OC PLUS', 'pc-amd-gaming-pro-ryzen-7-5700x-rtx-5050-8gb-oc-plus', 'PCM-GM-02', 22990000.00, 22280000.00, 12, 'assets/images/products/pc_gaming_2.jpg', 'Thiết kế case kính cường lực và hệ thống quạt RGB lung linh.', '{\"cpu\":\"AMD Ryzen 7 5700X\",\"ram\":\"16GB DDR4\",\"vga\":\"RTX 5050 8GB OC\",\"storage\":\"SSD 512GB\"}', 1, 0, 1, 880, NOW()),
(9, 1, 3, 'PC AMD GAMING PRO Ryzen 7 5700X - RTX 5060 8GB OC', 'pc-amd-gaming-pro-ryzen-7-5700x-rtx-5060-8gb-oc', 'PCM-GM-03', 27990000.00, 25480000.00, 8, 'assets/images/products/pc_gaming_3.jpg', 'Card đồ họa RTX 5060 8GB mạnh mẽ, chơi game chuẩn 144Hz.', '{\"cpu\":\"AMD Ryzen 7 5700X\",\"ram\":\"16GB DDR4\",\"vga\":\"RTX 5060 8GB OC\",\"storage\":\"SSD 1TB\"}', 1, 1, 1, 1120, NOW()),
(10, 1, 5, 'PC AMD Ryzen 5 5500 - RTX 5050 8GB OC STREAMER', 'pc-amd-ryzen-5-5500-rtx-5050-8gb-oc-streamer', 'PCM-GM-04', 21990000.00, 20480000.00, 15, 'assets/images/products/pc_gaming_4.jpg', 'Tối ưu cho Streamer mới bắt đầu, ghi hình sắc nét.', '{\"cpu\":\"AMD Ryzen 5 5500\",\"ram\":\"16GB DDR4\",\"vga\":\"RTX 5050 8GB\",\"storage\":\"SSD 512GB\"}', 1, 0, 1, 780, NOW()),
(11, 1, 1, 'PC ASUS ROG 20th Anniversary | Ryzen 9 9950X3D - RAM 128GB - MATRIX', 'pc-asus-rog-20th-anniversary-ryzen-9-9950x3d-ram-128gb', 'PCM-GM-05', 559900000.00, 532000000.00, 2, 'assets/images/products/pc_gaming_5.jpg', 'Siêu phẩm kỷ niệm 20 năm ROG đỉnh cao công nghệ thế giới.', '{\"cpu\":\"AMD Ryzen 9 9950X3D\",\"ram\":\"128GB DDR5 6400MHz\",\"vga\":\"ROG Matrix RTX 4090 24GB\",\"storage\":\"SSD 4TB Gen5\"}', 1, 1, 1, 3500, NOW()),
(12, 1, 4, 'PC GAMING RTX 3060 12GB - i5 12400F (ALL NEW) - SL: 10 bộ', 'pc-gaming-rtx-3060-12gb-i5-12400f-all-new', 'PCM-GM-06', 24990000.00, 22280000.00, 10, 'assets/images/products/pc_gaming_6.jpg', 'VRAM khủng 12GB mượt mà mọi tựa game thế giới mở.', '{\"cpu\":\"Intel Core i5 12400F\",\"ram\":\"16GB DDR4\",\"vga\":\"GeForce RTX 3060 12GB\",\"storage\":\"SSD 512GB\"}', 1, 0, 1, 870, NOW()),

-- 3. PC WORKSTATION
(13, 15, 4, 'PC Workstation 3D - Edit Video I9 14900KF - RTX 3060 12GB OC', 'pc-workstation-3d-edit-video-i9-14900kf-rtx-3060-12gb-oc', 'PCM-WS-01', 49890000.00, 46280000.00, 8, 'assets/images/products/workstation_1.jpg', 'Cỗ máy dựng phim 4K chuyên nghiệp Premiere, After Effects.', '{\"cpu\":\"Intel Core i9 14900KF\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 3060 12GB OC\",\"storage\":\"SSD 1TB Gen4\"}', 1, 1, 1, 620, NOW()),
(14, 15, 4, 'PC Workstation- 3D Render- Edit Video I7 14700KF - RTX 5070 Ti 16GB', 'pc-workstation-3d-render-edit-video-i7-14700kf-rtx-5070-ti-16gb', 'PCM-WS-02', 75690000.00, 69980000.00, 6, 'assets/images/products/workstation_2.jpg', 'Sức mạnh dựng hình kiến trúc 3ds Max, Maya, Blender.', '{\"cpu\":\"Intel Core i7 14700KF\",\"ram\":\"64GB DDR5\",\"vga\":\"RTX 5070 Ti 16GB\",\"storage\":\"SSD 2TB NVMe\"}', 1, 1, 1, 710, NOW()),
(15, 15, 4, 'PC Workstation- 3D Render- Edit Video I5 14600KF - RTX 5060 Ti 16GB', 'pc-workstation-3d-render-edit-video-i5-14600kf-rtx-5060-ti-16gb', 'PCM-WS-03', 48990000.00, 48480000.00, 9, 'assets/images/products/workstation_3.jpg', 'Tối ưu render video đa luồng với 14 nhân 20 luồng.', '{\"cpu\":\"Intel Core i5 14600KF\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 5060 Ti 16GB\",\"storage\":\"SSD 1TB Gen4\"}', 1, 0, 1, 480, NOW()),
(16, 15, 4, 'PC Workstation- 3D Render- Edit Video I5 14600KF - RTX 5070 12GB', 'pc-workstation-3d-render-edit-video-i5-14600kf-rtx-5070-12gb', 'PCM-WS-04', 48990000.00, 47480000.00, 7, 'assets/images/products/workstation_4.jpg', 'Hiệu năng đồ họa mượt mà trong các dự án dựng phim dài tập.', '{\"cpu\":\"Intel Core i5 14600KF\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 5070 12GB\",\"storage\":\"SSD 1TB Gen4\"}', 1, 0, 1, 510, NOW()),
(17, 15, 1, 'PC TUF GAMING LUXURY ULTRA 7 270K PLUS - RTX 5070 Ti 16GB OC', 'pc-tuf-gaming-luxury-ultra-7-270k-plus-rtx-5070-ti-16gb-oc', 'PCM-WS-05', 99990000.00, 94980000.00, 5, 'assets/images/products/workstation_5.jpg', 'Kiến trúc Core Ultra 200 mới nhất kết hợp linh kiện TUF quân đội.', '{\"cpu\":\"Intel Core Ultra 7 270K\",\"ram\":\"64GB DDR5\",\"vga\":\"RTX 5070 Ti 16GB OC\",\"storage\":\"SSD 2TB Gen5\"}', 1, 1, 1, 890, NOW()),
(18, 15, 4, 'PC Đồ Họa 2D - Edit Video i5 12400F- RTX 3050 6GB OC', 'pc-do-hoa-2d-edit-video-i5-12400f-rtx-3050-6gb-oc', 'PCM-WS-06', 18990000.00, 16980000.00, 14, 'assets/images/products/workstation_6.jpg', 'Chuyên dùng Photoshop, Illustrator, Premiere Full HD mượt mà.', '{\"cpu\":\"Intel Core i5 12400F\",\"ram\":\"16GB DDR4\",\"vga\":\"RTX 3050 6GB OC\",\"storage\":\"SSD 512GB\"}', 1, 0, 1, 430, NOW()),

-- 4. PC AMD GAMING
(19, 16, 5, 'PC AMD GAMING LUXURY RYZEN 7 9800X3D - RTX 5070 Ti 16GB', 'pc-amd-gaming-luxury-ryzen-7-9800x3d-rtx-5070-ti-16gb', 'PCM-AMD-01', 89990000.00, 82980000.00, 6, 'assets/images/products/amd_gaming_1.jpg', 'Vua gaming thế giới mới Ryzen 7 9800X3D tản nhiệt siêu êm ái.', '{\"cpu\":\"AMD Ryzen 7 9800X3D\",\"ram\":\"32GB DDR5 6000MHz\",\"vga\":\"RTX 5070 Ti 16GB\",\"storage\":\"SSD 1TB Gen4\"}', 1, 1, 1, 1200, NOW()),
(20, 16, 5, 'PC AMD GAMING LUXURY RYZEN 7 7800X3D - RTX 5070 Ti 16GB BLACK', 'pc-amd-gaming-luxury-ryzen-7-7800x3d-rtx-5070-ti-16gb-black', 'PCM-AMD-02', 75990000.00, 69980000.00, 8, 'assets/images/products/amd_gaming_2.jpg', 'Huyền thoại gaming 7800X3D kết hợp case hắc ám mạnh mẽ.', '{\"cpu\":\"AMD Ryzen 7 7800X3D\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 5070 Ti 16GB\",\"storage\":\"SSD 1TB Gen4\"}', 1, 1, 1, 980, NOW()),
(21, 16, 5, 'PC AMD GAMING RYZEN 5 7500F - RTX 5060 8GB', 'pc-amd-gaming-ryzen-5-7500f-rtx-5060-8gb', 'PCM-AMD-03', 30990000.00, 28680000.00, 12, 'assets/images/products/amd_gaming_3.jpg', 'Bộ vi xử lý AM5 xung nhịp cao kết hợp đồ họa thế hệ mới.', '{\"cpu\":\"AMD Ryzen 5 7500F\",\"ram\":\"16GB DDR5\",\"vga\":\"RTX 5060 8GB\",\"storage\":\"SSD 512GB Gen4\"}', 1, 0, 1, 650, NOW()),
(22, 16, 5, 'PC AMD GAMING RYZEN 7 7700 - RTX 5060 8GB', 'pc-amd-gaming-ryzen-7-7700-rtx-5060-8gb', 'PCM-AMD-04', 33990000.00, 30480000.00, 9, 'assets/images/products/amd_gaming_4.jpg', '8 nhân 16 luồng Zen 4 chạy đa nhiệm game và stream cực mượt.', '{\"cpu\":\"AMD Ryzen 7 7700\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 5060 8GB\",\"storage\":\"SSD 1TB\"}', 1, 0, 1, 570, NOW()),
(23, 16, 5, 'PC AMD Ryzen 5 5500 - RTX 3050 6GB', 'pc-amd-ryzen-5-5500-rtx-3050-6gb', 'PCM-AMD-05', 15990000.00, 14280000.00, 20, 'assets/images/products/amd_gaming_5.jpg', 'Giá siêu rẻ cho bộ máy gaming chuẩn bảo hành chính hãng.', '{\"cpu\":\"AMD Ryzen 5 5500\",\"ram\":\"16GB DDR4\",\"vga\":\"RTX 3050 6GB\",\"storage\":\"SSD 512GB\"}', 1, 0, 1, 740, NOW()),
(24, 16, 5, 'PC AMD Ryzen 5 5500 - RTX 5050 8GB OC Special', 'pc-amd-ryzen-5-5500-rtx-5050-8gb-oc-special', 'PCM-AMD-06', 21990000.00, 20480000.00, 15, 'assets/images/products/amd_gaming_6.jpg', 'Phiên bản nâng cấp nguồn 650W và tản nhiệt tháp RGB.', '{\"cpu\":\"AMD Ryzen 5 5500\",\"ram\":\"16GB DDR4\",\"vga\":\"RTX 5050 8GB OC\",\"storage\":\"SSD 512GB NVMe\"}', 1, 0, 1, 610, NOW()),

-- 5. PC VĂN PHÒNG
(25, 3, 4, 'PC OFFICE Core i7 12700 - RAM 16GB - SSD 500GB - Màn hình 24 Inch', 'pc-office-core-i7-12700-ram-16gb-ssd-500gb-man-hinh-24-inch', 'PCM-OFF-01', 20990000.00, 18680000.00, 18, 'assets/images/products/office_pc_1.jpg', 'Trọn bộ máy văn phòng cao cấp kèm màn hình 24 inch tràn viền sắc nét.', '{\"cpu\":\"Intel Core i7 12700\",\"ram\":\"16GB DDR4\",\"storage\":\"SSD 500GB NVMe\",\"monitor\":\"24 Inch IPS FHD\"}', 1, 1, 1, 830, NOW()),
(26, 3, 4, 'PC OFFICE Core i7 12700 - RAM 16GB - SSD 500GB', 'pc-office-core-i7-12700-ram-16gb-ssd-500gb', 'PCM-OFF-02', 19390000.00, 16980000.00, 22, 'assets/images/products/office_pc_2.jpg', 'Thân máy i7 đa nhiệm nhiều ứng dụng kế toán, văn phòng nặng.', '{\"cpu\":\"Intel Core i7 12700\",\"ram\":\"16GB DDR4\",\"storage\":\"SSD 500GB NVMe\",\"psu\":\"550W\"}', 1, 0, 1, 560, NOW()),
(27, 3, 4, 'PC OFFICE PENTIUM GOLD G6405 - RAM 8GB - SSD 256GB (Dành Cho Kế Toán)', 'pc-office-pentium-gold-g6405-ram-8gb-ssd-256gb', 'PCM-OFF-03', 7990000.00, 6980000.00, 30, 'assets/images/products/office_pc_3.jpg', 'Cực kỳ tiết kiệm điện, khởi động nhanh, êm ái cho công việc.', '{\"cpu\":\"Intel Pentium Gold G6405\",\"ram\":\"8GB DDR4\",\"storage\":\"SSD 256GB\",\"psu\":\"400W\"}', 1, 0, 1, 720, NOW()),
(28, 3, 4, 'PC OFFICE PENTIUM GOLD G6405 - RAM 8GB - SSD 256GB - Màn Hình 22 Inch', 'pc-office-pentium-gold-g6405-ram-8gb-ssd-256gb-man-hinh-22-inch', 'PCM-OFF-04', 9980000.00, 8880000.00, 25, 'assets/images/products/office_pc_4.jpg', 'Trọn gói đầy đủ cây máy tính và màn hình 22 inch cho văn phòng.', '{\"cpu\":\"Intel Pentium Gold G6405\",\"ram\":\"8GB DDR4\",\"storage\":\"SSD 256GB\",\"monitor\":\"22 Inch LED\"}', 1, 0, 1, 640, NOW()),
(29, 3, 4, 'PC OFFICE Core i5 10400 - RAM 8GB - SSD 256GB - Màn hình 24 inch', 'pc-office-core-i5-10400-ram-8gb-ssd-256gb-man-hinh-24-inch', 'PCM-OFF-05', 11990000.00, 10280000.00, 16, 'assets/images/products/office_pc_5.jpg', 'Combo Core i5 xử lý dữ liệu Excel hàng ngàn dòng mượt mà.', '{\"cpu\":\"Intel Core i5 10400\",\"ram\":\"8GB DDR4\",\"storage\":\"SSD 256GB\",\"monitor\":\"24 Inch IPS\"}', 1, 1, 1, 780, NOW()),
(30, 3, 4, 'PC OFFICE Core i5 10400 - RAM 8GB - SSD 256GB (Dành Cho Kế Toán)', 'pc-office-core-i5-10400-ram-8gb-ssd-256gb', 'PCM-OFF-06', 9990000.00, 8680000.00, 20, 'assets/images/products/office_pc_6.jpg', 'Dàn máy văn phòng tin cậy được hàng trăm doanh nghiệp tin dùng.', '{\"cpu\":\"Intel Core i5 10400\",\"ram\":\"8GB DDR4\",\"storage\":\"SSD 256GB\",\"psu\":\"450W\"}', 1, 0, 1, 510, NOW()),

-- 6. MÀN HÌNH MÁY TÍNH
(31, 13, 1, 'Màn hình văn phòng SSTC S2410 | 25 inch, FHD, 100Hz, IPS', 'man-hinh-van-phong-sstc-s2410-25-inch-fhd-100hz-ips', 'PCM-MON-01', 2450000.00, 1600000.00, 40, 'assets/images/products/monitor_1.jpg', 'Màn hình 25 inch siêu rộng tấm nền IPS tần số quét 100Hz dịu mắt.', '{\"size\":\"25 inch\",\"resolution\":\"FHD 1920x1080\",\"panel\":\"IPS\",\"refresh_rate\":\"100Hz\"}', 1, 1, 1, 1420, NOW()),
(32, 13, 2, 'Màn Hình MSI MAG 272F X24 (27 inch - IPS - FHD - 240Hz - 0.5ms)', 'man-hinh-msi-mag-272f-x24-27-inch-ips-fhd-240hz-0-5ms', 'PCM-MON-02', 3990000.00, 3490000.00, 25, 'assets/images/products/monitor_2.jpg', 'Tần số quét khủng 240Hz thời gian phản hồi 0.5ms chuẩn game thủ chuyên nghiệp.', '{\"size\":\"27 inch\",\"resolution\":\"FHD 1920x1080\",\"panel\":\"Rapid IPS\",\"refresh_rate\":\"240Hz\",\"response_time\":\"0.5ms\"}', 1, 1, 1, 980, NOW()),
(33, 13, 1, 'Màn Hình Gaming ASUS TUF Gaming VG259QM5A (24.5 inch/ Fast IPS/ 240Hz)', 'man-hinh-gaming-asus-tuf-gaming-vg259qm5a-24-5-inch-fast-ips-240hz', 'PCM-MON-03', 4999000.00, 3190000.00, 30, 'assets/images/products/monitor_3.jpg', 'Màn hình chiến game bắn súng CS2, Valorant số 1 phân khúc.', '{\"size\":\"24.5 inch\",\"resolution\":\"FHD 1920x1080\",\"panel\":\"Fast IPS\",\"refresh_rate\":\"240Hz\"}', 1, 1, 1, 1150, NOW()),
(34, 13, 1, 'Màn Hình AOC 24G50Z (23.8 inch - IPS - FHD - 260Hz - 0.3ms)', 'man-hinh-aoc-24g50z-23-8-inch-ips-fhd-260hz-0-3ms', 'PCM-MON-04', 3990000.00, 2690000.00, 20, 'assets/images/products/monitor_4.jpg', 'Tốc độ 260Hz ấn tượng, triệt tiêu hoàn toàn bóng ma khi xoay góc nhìn.', '{\"size\":\"23.8 inch\",\"resolution\":\"FHD 1920x1080\",\"panel\":\"Fast IPS\",\"refresh_rate\":\"260Hz\"}', 1, 0, 1, 840, NOW()),
(35, 13, 1, 'Màn Hình Gaming LG UltraGear 25G523B-B (25 inch - IPS - FHD - 200Hz)', 'man-hinh-gaming-lg-ultragear-25g523b-b-25-inch-ips-fhd-200hz', 'PCM-MON-05', 4990000.00, 3190000.00, 15, 'assets/images/products/monitor_5.jpg', 'Đẳng cấp dòng màn hình UltraGear từ LG với màu sắc chuẩn đồ họa.', '{\"size\":\"25 inch\",\"resolution\":\"FHD 1920x1080\",\"panel\":\"IPS sRGB 99%\",\"refresh_rate\":\"200Hz\"}', 1, 0, 1, 790, NOW()),
(36, 13, 1, 'Màn Hình Gaming LG UltraGear 27G64NA-B (27 Inch/ QHD/ IPS/ 300Hz)', 'man-hinh-gaming-lg-ultragear-27g64na-b-27-inch-qhd-ips-300hz', 'PCM-MON-06', 7990000.00, 7190000.00, 10, 'assets/images/products/monitor_6.jpg', 'Độ phân giải 2K QHD sắc nét cùng tần số quét kinh ngạc 300Hz.', '{\"size\":\"27 inch\",\"resolution\":\"QHD 2560x1440\",\"panel\":\"Nano IPS\",\"refresh_rate\":\"300Hz\"}', 1, 1, 1, 910, NOW()),

-- 7. PC AI - TRÍ TUỆ NHÂN TẠO
(37, 17, 4, 'PC AI Local Starter 12 - I5 14400F - RTX 3060 12GB', 'pc-ai-local-starter-12-i5-14400f-rtx-3060-12gb', 'PCM-AI-01', 29990000.00, 26980000.00, 10, 'assets/images/products/ai_pc_1.jpg', 'Cấu hình nhập môn chạy mô hình Stable Diffusion, Whisper và LLaMA nội bộ.', '{\"cpu\":\"Intel Core i5 14400F\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 3060 12GB VRAM\",\"storage\":\"SSD 1TB Gen4\"}', 1, 1, 1, 650, NOW()),
(38, 17, 4, 'PC AI Local Creator 16 - I5 14400F - RTX 5060 Ti 16GB', 'pc-ai-local-creator-16-i5-14400f-rtx-5060-ti-16gb', 'PCM-AI-02', 40990000.00, 39680000.00, 8, 'assets/images/products/ai_pc_2.jpg', 'Bộ nhớ VRAM 16GB lớn cho phép finetune các mô hình ngôn ngữ lớn.', '{\"cpu\":\"Intel Core i5 14400F\",\"ram\":\"32GB DDR5\",\"vga\":\"RTX 5060 Ti 16GB VRAM\",\"storage\":\"SSD 1TB Gen4\"}', 1, 0, 1, 580, NOW()),
(39, 17, 4, 'PC AI Local Creator 16 - Ultra 5 250K Plus RTX 5060 Ti 16GB', 'pc-ai-local-creator-16-ultra-5-250k-plus-rtx-5060-ti-16gb', 'PCM-AI-03', 59900000.00, 54680000.00, 7, 'assets/images/products/ai_pc_3.jpg', 'CPU Core Ultra 5 tích hợp NPU AI chuyên dụng kết hợp đồ họa 16GB.', '{\"cpu\":\"Intel Core Ultra 5 250K\",\"ram\":\"64GB DDR5\",\"vga\":\"RTX 5060 Ti 16GB\",\"storage\":\"SSD 2TB Gen4\"}', 1, 1, 1, 740, NOW()),
(40, 17, 4, 'PC AI Local Pro 16 - Ultra 7 270K Plus - RTX 5060 Ti 16GB', 'pc-ai-local-pro-16-ultra-7-270k-plus-rtx-5060-ti-16gb', 'PCM-AI-04', 59900000.00, 57980000.00, 6, 'assets/images/products/ai_pc_4.jpg', 'Cỗ máy AI chuyên nghiệp chạy các mô hình AI offline bảo mật tuyệt đối.', '{\"cpu\":\"Intel Core Ultra 7 270K\",\"ram\":\"64GB DDR5\",\"vga\":\"RTX 5060 Ti 16GB\",\"storage\":\"SSD 2TB NVMe\"}', 1, 0, 1, 620, NOW()),
(41, 17, 4, 'PC AI Local Creator 16 - I5 14600KF D4 - RTX 5070 Ti 16GB', 'pc-ai-local-creator-16-i5-14600kf-d4-rtx-5070-ti-16gb', 'PCM-AI-05', 69900000.00, 62980000.00, 5, 'assets/images/products/ai_pc_5.jpg', 'Sức mạnh tính toán Tensor Core thế hệ mới tăng tốc Deep Learning.', '{\"cpu\":\"Intel Core i5 14600KF\",\"ram\":\"64GB DDR5\",\"vga\":\"RTX 5070 Ti 16GB\",\"storage\":\"SSD 2TB Gen4\"}', 1, 1, 1, 810, NOW()),
(42, 17, 4, 'PC AI Local Pro 16 - I5 14600KF D4 - RTX 5080 16GB', 'pc-ai-local-pro-16-i5-14600kf-d4-rtx-5080-16gb', 'PCM-AI-06', 81900000.00, 75980000.00, 4, 'assets/images/products/ai_pc_6.jpg', 'Card đồ họa RTX 5080 16GB băng thông siêu khủng cho doanh nghiệp AI.', '{\"cpu\":\"Intel Core i5 14600KF\",\"ram\":\"64GB DDR5\",\"vga\":\"GeForce RTX 5080 16GB\",\"storage\":\"SSD 2TB Gen5\"}', 1, 1, 1, 930, NOW()),

-- 8. LINH KIỆN MÁY TÍNH
(43, 7, 1, 'Card màn hình Asus ROG Astral GeForce RTX 5090 Edition 20', 'card-man-hinh-asus-rog-astral-geforce-rtx-5090-edition-20', 'PCM-PART-01', 289990000.00, 280000000.00, 2, 'assets/images/products/component_1.jpg', 'Tuyệt tác card đồ họa mạnh nhất hành tinh với kiến trúc Blackwell vượt giới hạn.', '{\"vram\":\"32GB GDDR7\",\"bus\":\"512-bit\",\"cooling\":\"3 Quạt Axial-tech\",\"power\":\"600W 12V-2x6\"}', 1, 1, 1, 4100, NOW()),
(44, 7, 1, 'Card Màn Hình Colorful GeForce RTX 3060 Battle AX DUO 12GB V2-V', 'card-man-hinh-colorful-geforce-rtx-3060-battle-ax-duo-12gb-v2-v', 'PCM-PART-02', 12900000.00, 10990000.00, 25, 'assets/images/products/component_2.jpg', 'Chiếc card đồ họa bán chạy nhất phân khúc tầm trung với 12GB VRAM.', '{\"vram\":\"12GB GDDR6\",\"bus\":\"192-bit\",\"fans\":\"2 quạt 90mm\"}', 1, 0, 1, 1340, NOW()),
(45, 6, 1, 'Mainboard ASUS TUF GAMING B650M-PLUS WIFI DDR5', 'mainboard-asus-tuf-gaming-b650m-plus-wifi-ddr5', 'PCM-PART-03', 6999000.00, 4590000.00, 30, 'assets/images/products/component_3.jpg', 'Bo mạch chủ socket AM5 bền bỉ, hỗ trợ WiFi 6 và PCIe 5.0 M.2.', '{\"socket\":\"AM5\",\"chipset\":\"B650\",\"ram\":\"4x DDR5\",\"wifi\":\"WiFi 6\"}', 1, 1, 1, 980, NOW()),
(46, 7, 1, 'Card màn hình ZOTAC GAMING GeForce RTX 5060 Ti 8GB Twin Edge', 'card-man-hinh-zotac-gaming-geforce-rtx-5060-ti-8gb-twin-edge', 'PCM-PART-04', 18990000.00, 15890000.00, 18, 'assets/images/products/component_4.jpg', 'Thiết kế nhỏ gọn 2 quạt vừa vặn mọi thùng máy tính, hỗ trợ DLSS 4.', '{\"vram\":\"8GB GDDR7\",\"interface\":\"PCIe 5.0\",\"fans\":\"IceStorm 2.0\"}', 1, 0, 1, 760, NOW()),
(47, 7, 1, 'Card màn hình ZOTAC GAMING GeForce RTX 5060 8GB Twin Edge', 'card-man-hinh-zotac-gaming-geforce-rtx-5060-8gb-twin-edge', 'PCM-PART-05', 15990000.00, 13890000.00, 20, 'assets/images/products/component_5.jpg', 'Hiệu năng đồ họa mượt mà ở độ phân giải Full HD & 2K với mức tiêu thụ điện cực thấp.', '{\"vram\":\"8GB GDDR7\",\"interface\":\"PCIe 5.0\"}', 1, 0, 1, 820, NOW()),
(48, 5, 5, 'CPU AMD Ryzen 9 9950X3D Dual Edition (Up to 5.6GHz | 16 Cores 32 Threads)', 'cpu-amd-ryzen-9-9950x3d-dual-edition-up-to-5-6ghz-16-cores-32-threads', 'PCM-PART-06', 29990000.00, 28990000.00, 10, 'assets/images/products/component_6.jpg', 'Quái thú xử lý đa nhiệm và gaming số một hành tinh với 16 nhân 32 luồng và 128MB 3D V-Cache.', '{\"socket\":\"AM5\",\"cores\":\"16 Nhân 32 Luồng\",\"boost_clock\":\"5.6 GHz\",\"cache\":\"128MB 3D V-Cache\"}', 1, 1, 1, 1580, NOW());

-- 5. SEED MÃ GIẢM GIÁ (COUPONS)
TRUNCATE TABLE `coupons`;
INSERT INTO `coupons` (`id`, `code`, `description`, `type`, `discount_value`, `min_order_amount`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`) VALUES
(1, 'WELCOME50K', 'Giảm 50.000đ cho khách hàng mới', 'fixed', 50000.00, 500000.00, NULL, 500, 15, '2026-01-01', '2026-12-31', 1),
(2, 'PCMART100K', 'Giảm ngay 100.000đ cho đơn hàng từ 5 triệu', 'fixed', 100000.00, 5000000.00, NULL, 200, 42, '2026-01-01', '2026-12-31', 1),
(3, 'GAMING5', 'Giảm 5% tối đa 1.000.000đ cho bộ PC Gaming', 'percentage', 5.00, 15000000.00, 1000000.00, 50, 8, '2026-01-01', '2026-12-31', 1),
(4, 'VIP500K', 'Giảm 500.000đ cho dàn PC Hi-End trên 30 triệu', 'fixed', 500000.00, 30000000.00, NULL, 30, 4, '2026-01-01', '2026-12-31', 1);

-- 6. SEED ĐƠN HÀNG MẪU (ORDERS & ORDER_ITEMS)
TRUNCATE TABLE `orders`;
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `note`, `subtotal`, `coupon_id`, `coupon_code`, `discount_amount`, `final_total`, `payment_method`, `payment_status`, `status`, `created_at`) VALUES
(1, 'PCM-20261101-0001', 3, 'Nguyễn Văn Khách', '0988889999', 'customer@pcmart.vn', 'Số 83A Cửu Long, Phường 15, Quận 10, TP.HCM', 'Giao giờ hành chính, gọi trước 15 phút', 22980000.00, 4, 'VIP500K', 500000.00, 22480000.00, 'banking', 'paid', 'completed', '2026-11-01 10:30:00'),
(2, 'PCM-20261103-0002', 4, 'Trần Minh Tuấn', '0912345678', 'tuan.tran@gmail.com', '456 Lê Duẩn, Quận 1, TP.HCM', 'Đóng gói cẩn thận', 22280000.00, 2, 'PCMART100K', 100000.00, 22180000.00, 'cod', 'unpaid', 'shipping', '2026-11-03 14:15:00');

TRUNCATE TABLE `order_items`;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `product_thumbnail`, `price`, `quantity`, `total`) VALUES
(1, 1, 1, 'PC ULTRA GAMING I5 14400F - RTX 5050 8GB', 'PCM-HOT-01', 'assets/images/products/hotsale_pc_1.jpg', 22980000.00, 1, 22980000.00),
(2, 2, 2, 'PC AMD GAMING PRO Ryzen 7 5700X - RTX 5050 8GB OC', 'PCM-HOT-02', 'assets/images/products/hotsale_pc_2.jpg', 22280000.00, 1, 22280000.00);

-- 7. SEED ĐÁNH GIÁ (REVIEWS)
TRUNCATE TABLE `reviews`;
INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 3, 1, 5, 'Dàn PC ULTRA GAMING giao siêu nhanh, test mượt mọi tựa game Delta Force và CS2 fps trên 240!', 1, NOW()),
(2, 4, 11, 5, 'Bộ máy ASUS ROG 20th Anniversary đỉnh cao thực sự, đèn LED Matrix cực kỳ ấn tượng!', 1, NOW());

-- 8. SEED BANNERS SLIDER TRANG CHỦ
TRUNCATE TABLE `banners`;
INSERT INTO `banners` (`id`, `title`, `image`, `link`, `position`, `display_order`, `status`) VALUES
(1, 'Top Cấu Hình PC Chiến Delta Force - Trải Nghiệm Đỉnh Cao', 'assets/images/banners/hero_delta_force.jpg', '/san-pham?danh-muc=pc-gaming', 'home_main', 1, 1),
(2, 'PC Gaming RTX 40 & 50 Series', 'assets/images/banners/hero_slide_2.jpg', '/san-pham?danh-muc=pc-gaming', 'home_main', 2, 1),
(3, 'PC AI & Deep Learning Studio 2026', 'assets/images/banners/hero_slide_3.jpg', '/san-pham?danh-muc=pc-ai-tri-tue-nhan-tao', 'home_main', 3, 1);

SET FOREIGN_KEY_CHECKS = 1;
