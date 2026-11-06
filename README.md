# WebPC - Website Thương Mại Điện Tử Bán PC & Linh Kiện Máy Tính
> Dự án cuối kỳ môn **Lập trình Web** — Trường Đại học Giao thông Vận tải TP.HCM (UTH)  
> Website tham khảo phong cách: [PC Market](https://pcmarket.vn/)

---

## 📌 1. Giới thiệu dự án
Dự án được xây dựng theo kiến trúc **Modular MVC (Model - View - Controller)** bằng ngôn ngữ **PHP** thuần kết hợp cơ sở dữ liệu **MySQL**, thiết kế theo tiêu chuẩn tách biệt module độc lập giúp nhóm 5 thành viên phát triển song song mà **không bị phụ thuộc hay xung đột Git**.

### ✨ Các tính năng nổi bật:
- **Phân hệ Khách hàng (Client):**
  - Trang chủ chuẩn phong cách PCMarket (Banner, Flash Sale, PC Gaming, Linh kiện).
  - Danh mục đa cấp & Bộ lọc sản phẩm đa tiêu chí (Hãng, Giá tiền, Loại linh kiện).
  - Tìm kiếm sản phẩm gợi ý tức thì (Ajax Live Search).
  - Giỏ hàng linh hoạt & Áp dụng mã giảm giá (Coupon/Voucher).
  - Đặt hàng & thanh toán (COD + quét mã QR chuyển khoản VNPay).
  - **Tính năng độc quyền "Xây dựng cấu hình PC (PC Builder)":** Tự chọn từng linh kiện tương thích, tự cộng tiền realtime, in báo giá và thêm cả bộ vào giỏ hàng.
  - Đăng ký, Đăng nhập, Trang thông tin cá nhân & Lịch sử đơn hàng.
  - Đánh giá sao & bình luận sản phẩm; Tin tức công nghệ (Blog).
- **Phân hệ Quản trị (Admin Dashboard):**
  - Thống kê doanh thu, đơn hàng, khách hàng, biểu đồ trực quan (Chart.js).
  - Quản lý danh mục, thương hiệu, sản phẩm (upload nhiều ảnh, bảng specs JSON, CKEditor).
  - Quản lý đơn hàng (cập nhật trạng thái, in hóa đơn giao hàng).
  - Quản lý mã giảm giá (Voucher).
  - Quản lý người dùng, phân quyền (Admin, Staff, Customer).
  - Quản lý đánh giá, tin tức công nghệ và banner quảng cáo.

---

## Giao diện hiện tại

Frontend dùng PHP views + CSS/JS riêng theo phong cách cửa hàng phần cứng: nền trắng/xám lạnh, xanh cobalt, tiêu đề Barlow Condensed và nội dung Roboto.
Trang chủ, catalog, chi tiết sản phẩm, đăng nhập/đăng ký, 404 và dashboard dùng chung hệ thiết kế tại [DESIGN.md](./DESIGN.md).
Motion dùng chung nằm ở `public/assets/css/motion.css` và `public/assets/js/motion.js`, được nạp qua master layouts.
Hiệu ứng không chứa logic nghiệp vụ; view/model/controller tiếp tục theo cấu trúc Modular MVC.
Chạy `node scripts/verify-motion.cjs` để kiểm tra animation, reduced-motion, bàn phím và chế độ không JavaScript.
Skeleton dùng `loading.css/loading.js` và partial `views/layouts/client_loading.php`.
Chạy `node scripts/verify-loading.cjs` để kiểm tra skeleton khi mạng chậm, lỗi ảnh và hiển thị mobile.
So sánh 2–4 PC dùng chung trên card, trang chi tiết và bàn chọn cấu hình: `pc-compare.js/css`,
`views/layouts/pc_compare.php` và `pc_compare_button.php`. Lưu ID vào trình duyệt, tải lại thông số
qua API chỉ đọc `GET /api/compare?ids=1,2,3` trong module Product. Kiểm tra: `node scripts/verify-pc-compare.cjs`.
Các nghiệp vụ backend hiện có và database được giữ nguyên; riêng tính năng so sánh bổ sung API chỉ đọc trong module Product. Các chức năng chưa có controller (giỏ hàng, thanh toán, PC Builder, profile và CRUD) chưa thể thao tác nghiệp vụ; UI hiển thị trạng thái rõ ràng.

Kiểm tra trực quan và tương tác: [scripts/verify-frontend.cjs](./scripts/verify-frontend.cjs).
Đặt biến `PLAYWRIGHT_PATH` trỏ tới package Playwright, `CHROME_PATH` tới Chrome nếu cần và `WEBPC_QA_URL` tới URL ứng dụng; chạy `node scripts/verify-frontend.cjs`.
Ảnh chụp và kết quả được xuất vào `artifacts/frontend/`. Dữ liệu mẫu hiện tại có một số tên lỗi mã hóa và ảnh không khớp sản phẩm; chưa sửa dữ liệu trong đợt làm frontend.

## 👥 2. Phân công công việc nhóm 5 thành viên
Xem chi tiết đầy đủ trong file: 📄 **[KE_HOACH_DU_AN.md](./KE_HOACH_DU_AN.md)**

| Thành viên | Phân hệ đảm nhiệm | Tỉ lệ đóng góp |
| :--- | :--- | :---: |
| **Dev 1 (Lead)** | Khung kiến trúc Core MVC, Database Schema, Auth & Phân quyền, Master Layouts | **25%** |
| **Dev 2** | Sản phẩm, Danh mục, Thương hiệu, Bộ lọc đa tiêu chí, Live search Ajax | **20%** |
| **Dev 3** | Giỏ hàng, Mã giảm giá (Coupon), Đặt hàng Checkout, Quản lý đơn hàng Admin | **20%** |
| **Dev 4** | Quản lý Tài khoản cá nhân, Đánh giá sản phẩm, Tin tức Blog, Slider Banner | **15%** |
| **Dev 5** | Tính năng "Xây dựng cấu hình PC (PC Builder)" & Dashboard thống kê doanh thu | **20%** |

---

## 🚀 3. Hướng dẫn cài đặt & Chạy ứng dụng

### Yêu cầu môi trường:
- PHP >= 7.4 (khuyến nghị PHP 8.0 - 8.2)
- MySQL / MariaDB (XAMPP hoặc Laragon)
- Apache có bật module `mod_rewrite`

### Các bước cài đặt:
1. **Clone mã nguồn:**
   ```bash
   git clone https://github.com/mailiemtruc/webpc.git
   cd webpc
   ```
2. **Tạo CSDL và nạp dữ liệu mẫu:**
   - Mở `phpMyAdmin` (hoặc MySQL Workbench / Navicat / HeidiSQL).
   - Tạo mới một cơ sở dữ liệu tên là: `webpc_db` (bảng mã `utf8mb4_unicode_ci`).
   - Import file `database/schema.sql` để tạo bảng.
   - Import file `database/seeders.sql` để có sẵn dữ liệu mẫu (sản phẩm, đơn hàng, coupon...).
3. **Cấu hình môi trường:**
   - Sao chép file `.env.example` thành `.env`:
     ```bash
     copy .env.example .env
     ```
   - Chỉnh sửa thông số kết nối CSDL trong file `.env` nếu cổng hoặc mật khẩu MySQL của bạn khác mặc định:
     ```env
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_NAME=webpc_db
     DB_USER=root
     DB_PASS=
     APP_URL=http://localhost/webpc/public
     ```
4. **Truy cập ứng dụng:**
   - Mở trình duyệt và truy cập: `http://localhost/webpc/public/`
   - Tài khoản Admin mặc định:
     - **Email:** `admin@pcmart.vn`
     - **Mật khẩu:** `admin123`
   - Tài khoản Khách hàng thử nghiệm:
     - **Email:** `customer@pcmart.vn`
     - **Mật khẩu:** `123456`

