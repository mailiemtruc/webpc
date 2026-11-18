# WebPC (PCMarket) — Website Thương Mại Điện Tử Bán PC & Linh Kiện Máy Tính
> Dự án cuối kỳ môn **Lập trình Web** — Trường Đại học Giao thông Vận tải TP.HCM (UTH)  
> Kiến trúc **Modular MVC** bằng PHP thuần + MySQL/MariaDB, lấy cảm hứng từ hệ thống bán lẻ phần cứng PCMarket.

---

## 1. Giới thiệu & Tính năng đã hoàn thiện

Hệ thống được xây dựng theo kiến trúc **Modular MVC** tách biệt hoàn toàn 5 phân hệ độc lập, chạy mượt trên **XAMPP (Apache + PHP + MariaDB)** lẫn **Laragon (MySQL 8)** mà không cần cài thêm framework.

### Phân hệ Khách hàng (Client)
- **Trang chủ thương mại điện tử:** Hero Slider 16:9 + 3 Sub-banner thiết kế riêng, Mega Menu 10 nhóm danh mục chi tiết, khối Hot Sale theo tab, showcase đầy đủ PC Gaming, Workstation, AMD Gaming, Văn phòng, Màn hình, PC AI, Linh kiện, Gaming Gear, Loa/Mic/Webcam và Tin tức công nghệ.
- **Kho dữ liệu thực tế:** **292 sản phẩm** kèm ảnh HD sắc nét thuộc **19 danh mục** và **24 thương hiệu** chính hãng.
- **Danh mục & Bộ lọc đa tiêu chí + Live Search:** Lọc kết hợp theo danh mục cha/con, thương hiệu, khoảng giá, CPU, RAM, VGA, sắp xếp, phân trang và gợi ý tìm kiếm tức thì qua Ajax (`GET /api/search`).
- **So sánh cấu hình PC (2–4 bộ máy):** Chọn trực tiếp từ card hoặc trang chi tiết, xem bảng đối chiếu thông số qua API `GET /api/compare`.
- **Xây dựng cấu hình PC (PC Builder):** Chọn từng linh kiện (CPU, Mainboard, RAM, VGA, SSD, PSU, Case, Tản nhiệt, Màn hình), kiểm tra tương thích & công suất nguồn, tính tổng tiền realtime, in/xuất báo giá và đẩy cả bộ vào giỏ hàng.
- **Giỏ hàng, Mã giảm giá & Thanh toán:**
  - Cập nhật số lượng, áp dụng Voucher (`WELCOME50K`, `PCMART100K`, `GAMING5`, `VIP500K`).
  - Hỗ trợ **COD** (thanh toán khi nhận hàng) và **Chuyển khoản VietQR** (tự sinh mã QR theo số tiền & mã đơn hàng, cấu hình tài khoản ngân hàng linh hoạt trong `.env`).
  - Tự động gửi **Email xác nhận đơn hàng** qua Gmail SMTP (hoặc ghi log local vào `storage/logs/mail.log` khi chưa cấu hình SMTP).
- **Tài khoản & Bảo mật nâng cao:** Đăng ký, xác nhận email, quên/đặt lại mật khẩu, **Đăng nhập nhanh bằng Google OAuth 2.0**, **Xác thực 2 bước (2FA TOTP Authenticator + mã dự phòng dùng 1 lần kiểu Discord)**, theo dõi lịch sử & hủy đơn hàng, đánh giá sao và bình luận sản phẩm.

### Phân hệ Quản trị (Admin Dashboard)
- **Dashboard thống kê trực quan:** Tổng doanh thu đã thanh toán, doanh thu hoàn tất, tổng đơn hàng, đơn chờ duyệt, sản phẩm bán chạy và biểu đồ doanh thu 7 ngày gần nhất (**Chart.js**).
- **Quản lý toàn diện (CRUD):** Sản phẩm (ảnh, thông số JSON), Danh mục, Thương hiệu, Đơn hàng (duyệt trạng thái, xác nhận thanh toán, in hóa đơn), Mã giảm giá (Coupon), Người dùng & Phân quyền (`admin` / `staff` / `customer`), Đánh giá, Tin tức Blog và Banner trang chủ.
- **An toàn & Bảo mật:** Prepared Statements (chống SQL Injection), CSRF Token cho mọi POST form, mã hóa mật khẩu `bcrypt`, session cookie `HttpOnly` + `SameSite=Lax`, chặn truy cập trái phép qua Middleware phân quyền.

---

## 2. Phân công nhóm 5 thành viên

| Thành viên | Phân hệ đảm nhiệm | Tỉ lệ đóng góp |
| :--- | :--- | :---: |
| **Dev 1 (Lead)** | Kiến trúc Core MVC, Database Schema & Seeders, Auth (Email/OAuth/2FA), Master Layouts, Tích hợp & Kiểm thử toàn hệ thống | **25%** |
| **Dev 2** | Sản phẩm, Danh mục, Thương hiệu, Bộ lọc đa tiêu chí, Live Search Ajax, So sánh PC | **20%** |
| **Dev 3** | Giỏ hàng, Mã giảm giá (Coupon), Thanh toán COD + VietQR, Email xác nhận đơn, Quản lý đơn hàng Admin | **20%** |
| **Dev 4** | Hồ sơ cá nhân & Lịch sử đơn hàng, Đánh giá sản phẩm, Tin tức Blog, Banner Slider | **15%** |
| **Dev 5** | Công cụ Xây dựng cấu hình PC (PC Builder) & Admin Dashboard thống kê Chart.js | **20%** |

---

## 3. Hướng dẫn cài đặt & chạy ứng dụng (XAMPP / Laragon)

### Yêu cầu môi trường
- **PHP >= 7.4** (khuyến nghị PHP 8.1 – 8.3; có sẵn trong XAMPP mới nhất).
- **MySQL / MariaDB** (XAMPP hoặc Laragon).
- **Apache** đã bật `mod_rewrite` (mặc định bật sẵn trên XAMPP).
- Không bắt buộc cài Composer (hệ thống có sẵn PSR-4 autoloader trong `public/index.php`).

### Các bước cài đặt
1. **Clone mã nguồn vào thư mục webroot** (`C:\xampp\htdocs\` với XAMPP):
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/mailiemtruc/webpc.git
   cd webpc
   ```
2. **Tạo file cấu hình `.env`:**
   ```bash
   copy .env.example .env
   ```
   Kiểm tra các thông số trong `.env` (mặc định đã khớp sẵn với XAMPP):
   ```env
   APP_URL=http://localhost/webpc/public
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=webpc_db
   DB_USER=root
   DB_PASS=

   # Tài khoản nhận chuyển khoản VietQR (tùy chỉnh theo nhu cầu)
   PAYMENT_BANK_CODE=MB
   PAYMENT_BANK_NAME="MB Bank (Ngân hàng Quân Đội)"
   PAYMENT_ACCOUNT_NO=0987654321
   PAYMENT_ACCOUNT_NAME="CONG TY PCMARKET"
   ```
   *(Tùy chọn: điền thêm `MAIL_USERNAME` / `MAIL_PASSWORD` để gửi email Gmail thật và `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` để bật đăng nhập Google; nếu bật trên XAMPP nhớ bỏ dấu `;` ở `extension=curl` và `extension=openssl` trong `C:\xampp\php\php.ini`.)*
3. **Import cơ sở dữ liệu trên phpMyAdmin (`http://localhost/phpmyadmin`):**
   - Khởi động **Apache** và **MySQL**.
   - Import file **`database/schema.sql`** trước (tự tạo database `webpc_db` chuẩn `utf8mb4_unicode_ci` và toàn bộ bảng).
   - Import tiếp file **`database/seeders.sql`** để nạp đầy đủ 292 sản phẩm, 19 danh mục, 24 thương hiệu, đơn hàng mẫu, đánh giá, banner và blog.
4. **Truy cập hệ thống:**
   - **Trang khách hàng:** `http://localhost/webpc/public/`
   - **Trang quản trị:** `http://localhost/webpc/public/admin` (đăng nhập tại `/dang-nhap`)

### Tài khoản thử nghiệm mặc định
| Vai trò | Email | Mật khẩu |
| :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@pcmart.vn` | `admin123` |
| **Nhân viên (Staff)** | `staff@pcmart.vn` | `admin123` |
| **Khách hàng (Customer)** | `customer@pcmart.vn` | `123456` |
