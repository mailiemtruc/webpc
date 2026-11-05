-- ========================================================
-- DỮ LIỆU MẪU (SEEDERS) CHO DỰ ÁN WEBPC
-- Môn: Lập trình Web - UTH
-- Tương thích hoàn toàn với schema.sql
-- ========================================================

USE `webpc_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. SEED TÀI KHOẢN NGƯỜI DÙNG (USERS)
-- Mật khẩu mặc định:
-- admin@pcmart.vn / admin123
-- staff@pcmart.vn / admin123
-- customer@pcmart.vn / 123456
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
(14, NULL, 'Bàn Phím & Chuột (Gear)', 'gaming-gear', 'fa-keyboard', 6, 1);

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
(9, 'LIAN LI', 'lian-li', 'assets/images/brands/lian-li.png', 1);

-- 4. SEED SẢN PHẨM (PRODUCTS)
TRUNCATE TABLE `products`;
INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `sku`, `price`, `discount_price`, `quantity`, `thumbnail`, `summary`, `description`, `specifications`, `is_featured`, `is_hot`, `status`, `view_count`) VALUES
(1, 1, 1, 'PC Gaming PCM ROG Strix Core i7 14700F / RTX 4070 12GB / 32GB RAM', 'pc-gaming-pcm-rog-strix-i7-14700f-rtx-4070', 'PCM-PC-ROG01', 38900000.00, 35990000.00, 15, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=600', 
'Dàn PC Gaming cao cấp dành cho game thủ hardcore và livestreamer. Cấu hình cực mạnh cân mượt mọi game AAA ở độ phân giải 2K, 4K.', 
'<h3>Đặc điểm nổi bật của dàn PC Gaming ROG Strix</h3><p>Được xây dựng từ các linh kiện tuyển chọn hàng đầu của ASUS ROG và vi xử lý Intel Core i7 thế hệ 14 mới nhất, bộ máy mang lại hiệu năng đỉnh cao cùng thiết kế tản nhiệt nước ARGB lung linh.</p><ul><li>Hiệu năng vượt trội với vi xử lý 20 nhân 28 luồng</li><li>Đồ họa RTX 4070 hỗ trợ DLSS 3 và Ray Tracing cực chân thực</li><li>Hệ thống làm mát êm ái, hoạt động bền bỉ suốt 24/7</li></ul>', 
'{"cpu":"Intel Core i7 14700F","mainboard":"ASUS B760M ROG STRIX","ram":"32GB (2x16GB) DDR5 6000MHz","vga":"ASUS Dual GeForce RTX 4070 12GB","storage":"SSD 1TB M.2 NVMe PCIe 4.0","psu":"750W 80 Plus Bronze","case":"Lian Li O11 Dynamic White","cooling":"Tản nhiệt nước AIO 240 ARGB"}', 1, 1, 1, 1250),

(2, 1, 2, 'PC Gaming PCM Dragon Core i5 13400F / RTX 4060 8GB / 16GB RAM', 'pc-gaming-pcm-dragon-i5-13400f-rtx-4060', 'PCM-PC-MSI02', 21500000.00, 19490000.00, 25, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=600', 
'Cấu hình quốc dân chiến mượt các tựa game Esport (Valorant, CS2, LOL, PUBG) và đồ họa cơ bản ở mức thiết lập đồ họa cao.', 
'<h3>Cấu hình gaming tối ưu ngân sách tốt nhất</h3><p>Kết hợp giữa CPU Core i5 thế hệ 13 và card đồ họa thế hệ RTX 40-Series mới nhất với công nghệ DLSS 3 nhân đôi khung hình, đảm bảo trải nghiệm chơi game cực kỳ mượt mà.</p>', 
'{"cpu":"Intel Core i5 13400F","mainboard":"MSI B760M BOMBER","ram":"16GB (2x8GB) DDR4 3200MHz","vga":"MSI GeForce RTX 4060 VENTUS 2X 8GB","storage":"SSD 512GB M.2 NVMe","psu":"650W 80 Plus","case":"Xigmatek Gaming RGB","cooling":"Tản khí CR1000 EVO ARGB"}', 1, 1, 1, 980),

(3, 2, 3, 'PC Đồ Họa PCM Creator Ryzen 9 7900X / RTX 4080 Super 16GB / 64GB RAM', 'pc-do-hoa-pcm-creator-ryzen-9-7900x-rtx-4080-super', 'PCM-PC-CREATOR01', 65000000.00, 59900000.00, 8, 'https://images.unsplash.com/photo-1547082299-de196ea013d6?w=600', 
'Dàn máy chuyên dụng cho dựng phim 4K/8K Premiere, After Effects, 3D Max, Blender và kiến trúc Render.', 
'<h3>Sức mạnh tối thượng cho nhà sáng tạo nội dung chuyên nghiệp</h3><p>Với 12 nhân 24 luồng của Ryzen 9 7900X và 64GB RAM DDR5 siêu tốc độ, đây là cỗ máy giải quyết mọi tác vụ dựng hình và xử lý AI trong chớp mắt.</p>', 
'{"cpu":"AMD Ryzen 9 7900X","mainboard":"GIGABYTE X670 GAMING X","ram":"64GB (2x32GB) DDR5 5600MHz","vga":"GIGABYTE GeForce RTX 4080 SUPER 16GB","storage":"SSD 2TB M.2 NVMe PCIe Gen 4","psu":"850W 80 Plus Gold","case":"Corsair 4000D Airflow","cooling":"Tản nước AIO DeepCool LT720 360mm"}', 1, 0, 1, 620),

(4, 5, 4, 'CPU Intel Core i7 14700F (Up to 5.4GHz, 20 Nhân 28 Luồng, 33MB Cache, LGA 1700)', 'cpu-intel-core-i7-14700f', 'CPU-INTEL-14700F', 9590000.00, 8890000.00, 30, 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?w=600', 
'Vi xử lý Intel Core i7 thế hệ thứ 14 Raptor Lake Refresh hiệu năng vượt bậc cho cả chơi game và đồ họa chuyên sâu.', 
'<p>Intel Core i7 14700F mang đến bước nhảy vọt về số lượng nhân E-Core (tổng cộng 20 nhân, 28 luồng), xung nhịp turbo đạt tới 5.4GHz.</p>', 
'{"socket":"LGA 1700","cores":"20 (8 P-Core + 12 E-Core)","threads":"28","base_clock":"2.1 GHz","boost_clock":"5.4 GHz","tdp":"65W"}', 0, 1, 1, 1540),

(5, 5, 5, 'CPU AMD Ryzen 7 7800X3D (Up to 5.0GHz, 8 Nhân 16 Luồng, 96MB 3D V-Cache, AM5)', 'cpu-amd-ryzen-7-7800x3d', 'CPU-AMD-7800X3D', 11500000.00, 10490000.00, 20, 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=600', 
'Ông vua chơi game số 1 thế giới với công nghệ bộ nhớ đệm xếp chồng khổng lồ 96MB 3D V-Cache.', 
'<p>Ryzen 7 7800X3D là vi xử lý được game thủ toàn cầu săn đón nhờ hiệu năng gaming vượt trội mọi đối thủ trong tầm giá.</p>', 
'{"socket":"AM5","cores":"8","threads":"16","base_clock":"4.2 GHz","boost_clock":"5.0 GHz","cache":"96MB 3D V-Cache","tdp":"120W"}', 1, 1, 1, 2100),

(6, 7, 1, 'Card Màn Hình ASUS TUF Gaming GeForce RTX 4070 Ti SUPER 16GB GDDR6X', 'vga-asus-tuf-rtx-4070-ti-super-16gb', 'VGA-ASUS-4070TIS', 26500000.00, 24890000.00, 12, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=600', 
'Thiết kế chuẩn độ bền quân đội TUF Gaming, làm mát 3 quạt Axial-tech siêu mát và ổn định.', 
'<p>Sở hữu bộ nhớ 16GB GDDR6X băng thông cao, hỗ trợ Ray Tracing thế hệ 3 và NVIDIA DLSS 3.5 tái tạo chi tiết đồ họa tuyệt mỹ.</p>', 
'{"vram":"16GB GDDR6X","bus":"256-bit","cuda_cores":"8448","recommended_psu":"750W","outputs":"2x HDMI 2.1a, 3x DisplayPort 1.4a"}', 1, 1, 1, 1780),

(7, 8, 6, 'RAM Corsair Vengeance RGB 32GB (2x16GB) DDR5 6000MHz Black', 'ram-corsair-vengeance-rgb-32gb-ddr5-6000mhz', 'RAM-CORSAIR-32GB-D5', 3450000.00, 2990000.00, 40, 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=600', 
'Bộ nhớ RAM DDR5 thế hệ mới tích hợp dải LED RGB động 10 vùng siêu sáng và hỗ trợ Intel XMP 3.0 / AMD EXPO.', 
'<p>Tốc độ 6000MHz đem lại độ phản hồi tức thì cho hệ thống, tản nhiệt nhôm nguyên khối giữ nhiệt độ luôn mát mẻ.</p>', 
'{"type":"DDR5","capacity":"32GB (2x16GB)","speed":"6000MHz","latency":"CL36","rgb":"Có (iCUE supported)"}', 0, 1, 1, 890),

(8, 9, 7, 'Ổ Cứng SSD Kingston KC3000 1TB M.2 PCIe Gen4 NVMe (Đọc 7000MB/s - Ghi 6000MB/s)', 'ssd-kingston-kc3000-1tb-pcie-gen4-nvme', 'SSD-KINGSTON-KC3000-1TB', 2600000.00, 2250000.00, 50, 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=600', 
'Tốc độ đọc ghi kinh ngạc lên đến 7000MB/s, trang bị miếng tản nhiệt nhôm Graphene mỏng nhẹ tản nhiệt cực tốt.', 
'<p>Sử dụng bộ điều khiển Phison E18 và chip nhớ 3D TLC NAND cao cấp, độ bền 800 TBW bảo hành 5 năm.</p>', 
'{"interface":"PCIe 4.0 NVMe M.2 2280","capacity":"1TB","read_speed":"7000 MB/s","write_speed":"6000 MB/s","tbw":"800 TBW"}', 0, 0, 1, 450);

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
(1, 'PCM-20261101-0001', 3, 'Nguyễn Văn Khách', '0988889999', 'customer@pcmart.vn', 'Số 83A Cửu Long, Phường 15, Quận 10, TP.HCM', 'Giao giờ hành chính, gọi trước 15 phút', 35990000.00, 4, 'VIP500K', 500000.00, 35490000.00, 'banking', 'paid', 'completed', '2026-11-01 10:30:00'),
(2, 'PCM-20261103-0002', 4, 'Trần Minh Tuấn', '0912345678', 'tuan.tran@gmail.com', '456 Lê Duẩn, Quận 1, TP.HCM', 'Đóng gói cẩn thận', 19490000.00, 2, 'PCMART100K', 100000.00, 19390000.00, 'cod', 'unpaid', 'shipping', '2026-11-03 14:15:00'),
(3, 'PCM-20261105-0003', NULL, 'Lê Hoàng Nam (Khách vãng lai)', '0933221144', 'nam.le@gmail.com', '12 Võ Văn Ngân, TP. Thủ Đức, TP.HCM', 'Kiểm tra hàng trước khi nhận', 2990000.00, 1, 'WELCOME50K', 50000.00, 2940000.00, 'cod', 'unpaid', 'pending', '2026-11-05 09:00:00');

TRUNCATE TABLE `order_items`;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `product_thumbnail`, `price`, `quantity`, `total`) VALUES
(1, 1, 1, 'PC Gaming PCM ROG Strix Core i7 14700F / RTX 4070 12GB / 32GB RAM', 'PCM-PC-ROG01', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=600', 35990000.00, 1, 35990000.00),
(2, 2, 2, 'PC Gaming PCM Dragon Core i5 13400F / RTX 4060 8GB / 16GB RAM', 'PCM-PC-MSI02', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=600', 19490000.00, 1, 19490000.00),
(3, 3, 7, 'RAM Corsair Vengeance RGB 32GB (2x16GB) DDR5 6000MHz Black', 'RAM-CORSAIR-32GB-D5', 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=600', 2990000.00, 1, 2990000.00);

-- 7. SEED ĐÁNH GIÁ (REVIEWS)
TRUNCATE TABLE `reviews`;
INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 3, 1, 5, 'Dàn PC giao rất nhanh, đóng gói xốp rất kỹ. Bật lên quạt chạy êm ru, test game Cyberpunk 2077 bật Ray Tracing max setting fps vẫn trên 90!', 1, NOW()),
(2, 4, 2, 5, 'Máy đẹp, LED RGB đồng bộ rất lung linh. Cửa hàng hỗ trợ cài sẵn Win 11 và các app cơ bản rất chu đáo.', 1, NOW()),
(3, 3, 5, 5, 'Con chip Ryzen 7 7800X3D này chơi CS2 và Valorant fps cực kỳ cao và ổn định, đáng từng đồng bát gạo!', 1, NOW());

-- 8. SEED TIN TỨC CÔNG NGHỆ (BLOGS)
TRUNCATE TABLE `blogs`;
INSERT INTO `blogs` (`id`, `author_id`, `title`, `slug`, `thumbnail`, `summary`, `content`, `view_count`, `status`, `created_at`) VALUES
(1, 1, 'Top 5 Cấu Hình PC Gaming Đáng Mua Nhất Dịp Cuối Năm 2026', 'top-5-cau-hinh-pc-gaming-dang-mua-nhat', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=600', 
'Điểm danh những bộ PC Gaming có tỉ lệ hiệu năng trên giá thành (P/P) tốt nhất năm 2026 chiến mượt mọi tựa game bom tấn.', 
'<p>Năm 2026 chứng kiến sự bùng nổ của card đồ họa RTX 40 Super Series và các dòng vi xử lý mới. Dưới đây là những gợi ý build PC hoàn hảo nhất theo từng mức ngân sách từ 15 đến 60 triệu đồng...</p>', 2500, 1, NOW()),
(2, 1, 'Hướng Dẫn Tự Xây Dựng Cấu Hình PC Cho Người Mới Bắt Đầu', 'huong-dan-tu-xay-dung-cau-hinh-pc', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=600', 
'Những lưu ý sống còn về tính tương thích giữa CPU, Mainboard, RAM và công suất nguồn PSU khi tự ráp máy tại nhà.', 
'<p>Việc chọn linh kiện không đơn thuần chỉ là chọn đồ đắt tiền. Bạn cần đặc biệt chú ý đến chuẩn socket CPU, form factor của vỏ case và mức công suất thực của nguồn máy tính...</p>', 1850, 1, NOW());

-- 9. SEED BANNERS SLIDER TRANG CHỦ
TRUNCATE TABLE `banners`;
INSERT INTO `banners` (`id`, `title`, `image`, `link`, `position`, `display_order`, `status`) VALUES
(1, 'Lễ Hội PC Gaming - Giảm Đến 30%', 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1200', '/san-pham?danh-muc=pc-gaming', 'home_main', 1, 1),
(2, 'Siêu Phẩm RTX 40 Super Series', 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=1200', '/san-pham?danh-muc=vga-card-man-hinh', 'home_main', 2, 1),
(3, 'Tự Build PC Tặng Ngay Voucher 500K', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=600', '/build-pc', 'home_sub1', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;

