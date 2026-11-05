# KẾ HOẠCH DỰ ÁN & KIẾN TRÚC HỆ THỐNG WEBSITE BÁN PC (PCMART / PCMARKET)
> **Môn học:** Lập trình Web — ĐH Giao thông Vận tải TP.HCM (UTH)  
> **Chủ đề:** Website Thương mại điện tử Bán PC & Linh kiện máy tính chuyên nghiệp  
> **Website tham khảo:** [PC Market](https://pcmarket.vn/)  
> **Quy mô:** Nhóm 5 thành viên | **Dev 1:** Lead Dev / Software Architect  
> **Repository Git:** `https://github.com/mailiemtruc/webpc.git`

---

## 1. ĐỐI CHIẾU YÊU CẦU ĐỀ BÀI & MỤC TIÊU ĐIỂM 10

| Tiêu chí giảng viên chấm | Thang điểm | Giải pháp triển khai để đạt điểm tối đa |
| :--- | :---: | :--- |
| **Demo, tính năng đầy đủ** | **3.0đ** | Đầy đủ luồng E-commerce: Xem, Lọc đa tiêu chí, Giỏ hàng, Mã giảm giá, Checkout, Tra cứu đơn hàng. **Đặc biệt: Tính năng "Xây dựng cấu hình PC (PC Builder)"** - tính năng ăn điểm độc quyền của web PC. Quản trị Admin đầy đủ nghiệp vụ CRUD. |
| **Thuyết trình** | **3.0đ** | Phân chia kịch bản thuyết trình 5 người mạch lạc; demo mượt mà không lỗi; giải thích rõ ràng kiến trúc MVC và bảo mật. |
| **Chất lượng source code & bài làm** | **2.0đ** | Cấu trúc thư mục **Modular MVC** sạch sẽ, chuẩn PSR-4, tách biệt rõ ràng, code có comment, an toàn trước SQL Injection (PDO Prepared Statement), XSS, CSRF, mã hóa mật khẩu `bcrypt`. |
| **Giao diện đẹp mắt, hiện đại** | **1.0đ** | Giao diện chuẩn phong cách công nghệ cao cấp của PCMarket.vn (Dark/Light mode accent, banner bắt mắt, responsive 100% trên Mobile, Tablet, Desktop). |
| **Slide báo cáo đầy đủ** | **1.0đ** | Đầy đủ sơ đồ phân tích: Use Case, Activity, Sequence, Thiết kế CSDL (ERD, Relational Schema), bảng phân công và đánh giá đóng góp % từng thành viên. |
| **TỔNG CỘNG** | **10.0đ** | **Mục tiêu đạt điểm A / A+ (9.5 - 10.0)** |

---

## 2. KIẾN TRÚC FOLDER STRUCTURE ĐỘC LẬP (MODULE-ISOLATED MVC)

Vấn đề lớn nhất của làm việc nhóm 5 người trong PHP là: **Conflict Git ở file `routes.php`, xung đột file header/footer, xung đột file config CSDL**.  
Giải pháp là sử dụng mô hình **Modular / Domain-based MVC**: mỗi Dev phụ trách hoàn toàn thư mục Module của mình, hệ thống tự động nạp (Auto-load) các Route và Controller mà không ai phải đụng vào file của người khác.

```text
webpc/
│
├── .env.example                     # File cấu hình mẫu (DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_URL)
├── .gitignore                       # Bỏ qua: .env, vendor/, uploads/temp/, .vscode/
├── composer.json                    # Khai báo PSR-4 Autoloading (nếu dùng composer)
├── KE_HOACH_DU_AN.md                # Tài liệu kế hoạch, phân chia công việc cho 5 người
├── README.md                        # Hướng dẫn cài đặt, chạy XAMPP/Laragon, sơ đồ hệ thống
│
├── database/                        # [DEV 1 QUẢN LÝ TẬP TRUNG]
│   ├── schema.sql                   # Cấu trúc toàn bộ bảng CSDL MySQL
│   ├── seeders.sql                  # Dữ liệu mẫu phong phú (PC Gaming, VGA, CPU, đơn hàng, coupon...)
│   └── erd_diagram.png              # Ảnh sơ đồ quan hệ thực thể ERD
│
├── public/                          # Web Root (Thư mục công khai - Chỉ trỏ Apache/Nginx vào đây)
│   ├── index.php                    # Single Entry Point duy nhất khởi động ứng dụng
│   ├── .htaccess                    # Rewrite URL sạch (Pretty URLs: /san-pham, /build-pc, /admin/...)
│   ├── uploads/                     # Thư mục chứa ảnh tải lên
│   │   ├── products/                # Ảnh sản phẩm
│   │   ├── banners/                 # Ảnh banner slider
│   │   ├── blogs/                   # Ảnh bài viết tin tức
│   │   └── avatars/                 # Ảnh đại diện người dùng
│   └── assets/                      # Tài nguyên tĩnh
│       ├── css/
│       │   ├── client.css           # Style trang khách hàng (hoặc Tailwind / Bootstrap)
│       │   ├── admin.css            # Style trang quản trị Admin
│       │   └── responsive.css       # Tùy biến tương thích di động
│       ├── js/
│       │   ├── main.js              # Script chung (menu mobile, format tiền, toast notify)
│       │   ├── cart.js              # Xử lý giỏ hàng & áp mã giảm giá Ajax
│       │   ├── pc-builder.js        # Logic tính năng Build PC tương thích thời gian thực
│       │   └── admin.js             # Xử lý datatables, biểu đồ chart, preview ảnh
│       └── images/                  # Logo, favicon, icon cố định
│
├── app/                             # Logic chính của ứng dụng
│   ├── Core/                        # [DO DEV 1 XÂY DỰNG - CÁC DEV KHÁC CHỈ KẾ THỪA]
│   │   ├── App.php                  # Application Bootstrap
│   │   ├── Database.php             # Kết nối PDO Singleton (chống SQL Injection)
│   │   ├── Router.php               # Trình định tuyến linh hoạt hỗ trợ GET/POST/Middleware
│   │   ├── Controller.php           # Base Controller (render view, json response, redirect)
│   │   ├── Model.php                # Base Model (CRUD cơ bản: all, find, create, update, delete)
│   │   ├── Request.php              # Xử lý dữ liệu đầu vào, validate, get, post
│   │   ├── Response.php             # Xử lý phản hồi JSON, Header, HTTP Status
│   │   ├── Session.php              # Quản lý phiên đăng nhập, Flash messages
│   │   ├── Auth.php                 # Quản lý trạng thái đăng nhập, mã hóa password, phân quyền
│   │   ├── Middleware.php           # Kiểm tra quyền: IsAdmin, IsStaff, IsCustomer, CSRFVerify
│   │   └── Helper.php               # Tiện ích: formatMoney(), slugify(), uploadFile(), paginate()
│   │
│   ├── Config/                      # Cấu hình hệ thống
│   │   ├── app.php                  # Cấu hình chung, tên web, múi giờ
│   │   └── database.php             # Đọc cấu hình từ .env
│   │
│   └── Modules/                     # TÁCH THEO TỪNG THÀNH VIÊN ĐỂ TRÁNH CONFLICT
│       │
│       ├── Auth/                    # [DO DEV 1 PHỤ TRÁCH]
│       │   ├── AuthController.php   # Login, Register, Logout, Forgot Password
│       │   └── UserModel.php        # Truy vấn dữ liệu người dùng
│       │
│       ├── Product/                 # [DO DEV 2 PHỤ TRÁCH]
│       │   ├── ProductController.php# Hiển thị danh sách, chi tiết, bộ lọc sản phẩm
│       │   ├── AdminProductController.php # Quản lý CRUD Sản phẩm, upload nhiều ảnh
│       │   ├── CategoryController.php    # Quản lý Danh mục (CRUD)
│       │   ├── BrandController.php       # Quản lý Thương hiệu (CRUD)
│       │   └── ProductModel.php          # Truy vấn sản phẩm, lọc theo CPU, VGA, giá...
│       │
│       ├── Order/                   # [DO DEV 3 PHỤ TRÁCH]
│       │   ├── CartController.php   # Thêm, sửa số lượng, xóa giỏ hàng
│       │   ├── CouponController.php # Kiểm tra và áp dụng mã giảm giá
│       │   ├── CheckoutController.php # Đặt hàng, chọn địa chỉ, thanh toán COD/VNPay demo
│       │   ├── AdminOrderController.php  # Quản lý đơn hàng, đổi trạng thái, in đơn
│       │   ├── AdminCouponController.php # Quản lý CRUD Mã giảm giá
│       │   ├── OrderModel.php       # Truy vấn đơn hàng, chi tiết đơn hàng
│       │   └── CouponModel.php      # Truy vấn mã giảm giá
│       │
│       ├── Interaction/             # [DO DEV 4 PHỤ TRÁCH]
│       │   ├── ProfileController.php# Quản lý thông tin cá nhân, lịch sử mua hàng
│       │   ├── ReviewController.php # Đánh giá sao, bình luận sản phẩm
│       │   ├── BlogController.php   # Trang tin tức công nghệ
│       │   ├── AdminUserController.php   # Quản lý tài khoản khách & nhân viên
│       │   ├── AdminReviewController.php # Duyệt / xóa đánh giá
│       │   ├── AdminBlogController.php   # Viết bài với CKEditor
│       │   ├── AdminBannerController.php # Cấu hình Banner/Slider
│       │   └── BlogModel.php, ReviewModel.php
│       │
│       └── PCBuilder/               # [DO DEV 5 PHỤ TRÁCH]
│           ├── PCBuilderController.php   # Giao diện build PC & tính toán linh kiện
│           ├── AdminDashboardController.php # Thống kê doanh thu, biểu đồ Chart.js
│           └── AnalyticsModel.php        # Truy vấn báo cáo tài chính, top bán chạy
│
├── routes/                          # CÁC FILE ROUTE ĐỘC LẬP - KHÔNG AI CHẠM NHAU
│   ├── auth.routes.php              # [DEV 1] Routes: /dang-nhap, /dang-ky, /dang-xuat
│   ├── product.routes.php           # [DEV 2] Routes: /san-pham, /danh-muc, /admin/products...
│   ├── order.routes.php             # [DEV 3] Routes: /gio-hang, /thanh-toan, /admin/orders...
│   ├── interaction.routes.php       # [DEV 4] Routes: /tai-khoan, /tin-tuc, /admin/users...
│   └── builder.routes.php           # [DEV 5] Routes: /build-pc, /admin/dashboard, /api/specs...
│
└── views/                           # Giao diện người dùng
    ├── layouts/                     # Master templates do Dev 1 dựng khung
    │   ├── client_header.php        # Topbar, Logo, Search bar, Mini-cart, Navbar menu
    │   ├── client_footer.php        # Showroom, chính sách bảo hành, hotline, bản quyền
    │   ├── admin_header.php         # Navbar Admin, thông báo, thông tin tài khoản
    │   ├── admin_sidebar.php        # Menu quản trị: Sản phẩm, Đơn hàng, Khuyến mãi, Doanh thu
    │   └── admin_footer.php         # Footer trang admin
    │
    ├── client/                      # Giao diện phía khách hàng
    │   ├── home/index.php           # Trang chủ (Banner, Flash Sale, PC Bán chạy, Linh kiện)
    │   ├── product/                 # [DEV 2]
    │   │   ├── list.php             # Danh sách sản phẩm + Bộ lọc đa tiêu chí
    │   │   └── detail.php           # Chi tiết sản phẩm, gallery, bảng thông số, đánh giá
    │   ├── cart/                    # [DEV 3]
    │   │   ├── index.php            # Giỏ hàng, ô nhập mã giảm giá
    │   │   ├── checkout.php         # Điền thông tin giao hàng & chọn thanh toán
    │   │   └── success.php          # Thông báo đặt hàng thành công + mã đơn tra cứu
    │   ├── account/                 # [DEV 4]
    │   │   ├── profile.php          # Đổi mật khẩu, xem thông tin
    │   │   └── orders.php           # Danh sách đơn hàng đã mua & tình trạng
    │   ├── blog/                    # [DEV 4] Danh sách bài viết & chi tiết tin tức
    │   └── pc_builder/              # [DEV 5] Giao diện cấu hình máy tính (PC Builder)
    │
    └── admin/                       # Giao diện phía Quản trị viên
        ├── dashboard/index.php      # [DEV 5] Thống kê, biểu đồ doanh số, số đơn
        ├── products/                # [DEV 2] CRUD sản phẩm, upload gallery, thông số
        ├── categories/              # [DEV 2] CRUD danh mục
        ├── brands/                  # [DEV 2] CRUD thương hiệu
        ├── orders/                  # [DEV 3] Danh sách & chi tiết đơn hàng, in hóa đơn
        ├── coupons/                 # [DEV 3] Quản lý mã giảm giá
        ├── users/                   # [DEV 4] Quản lý danh sách tài khoản
        ├── reviews/                 # [DEV 4] Quản lý bình luận
        ├── blogs/                   # [DEV 4] Quản lý tin tức (CKEditor)
        └── banners/                 # [DEV 4] Quản lý slider trang chủ
```

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU CHUẨN (MYSQL SCHEMA)

Dev 1 sẽ tạo sẵn toàn bộ CSDL và nạp dữ liệu mẫu để cả 5 người có thể làm việc ngay mà không bị thiếu dữ liệu chéo nhau:

### Danh sách các bảng chính:
1. `users`: Tài khoản, phân quyền (`admin`, `staff`, `customer`).
2. `categories`: Danh mục sản phẩm đa cấp (PC Gaming, PC Đồ Họa, CPU, VGA, RAM, Màn hình, Gear...).
3. `brands`: Thương hiệu sản phẩm (Asus, MSI, Gigabyte, Intel, AMD, Corsair...).
4. `products`: Sản phẩm, giá niêm yết, giá khuyến mãi, tồn kho, thông số kỹ thuật JSON, ảnh đại diện.
5. `product_images`: Nhiều ảnh gallery cho mỗi sản phẩm.
6. `coupons`: Mã giảm giá (loại giảm %, giảm tiền cố định, đơn tối thiểu, số lượt dùng).
7. `orders`: Đơn hàng (mã đơn `PCM-XXXX`, thông tin người nhận, hình thức COD/VNPay, trạng thái).
8. `order_items`: Chi tiết từng sản phẩm trong đơn hàng.
9. `reviews`: Đánh giá 1-5 sao và bình luận sản phẩm.
10. `blogs`: Tin tức, bài viết công nghệ, đánh giá linh kiện (hỗ trợ CKEditor).
11. `banners`: Quản lý slider ảnh trang chủ.

---

## 4. BẢNG PHÂN CÔNG CÔNG VIỆC CHI TIẾT CHO 5 THÀNH VIÊN

| Thành viên | Vai trò & Phân hệ đảm nhiệm | File mã nguồn phụ trách | Tiêu chí nghiệm thu (Checklist) | Tỉ lệ đóng góp |
| :--- | :--- | :--- | :--- | :---: |
| **DEV 1 (Bạn)** | **Lead Dev & Core Architect**<br>• Dựng Core MVC & Routing tự động<br>• Dựng Database & Seeder chuẩn<br>• Phân hệ Xác thực & Phân quyền (Auth)<br>• Dựng Master Layout Client & Admin<br>• Hợp nhất mã nguồn Git | `app/Core/*`<br>`app/Config/*`<br>`database/*`<br>`app/Modules/Auth/*`<br>`routes/auth.routes.php`<br>`views/layouts/*` | - Hệ thống chạy mượt trên Local (XAMPP/Laragon)<br>- Login/Logout/Register phân quyền 3 cấp (Admin, Staff, Customer)<br>- Chặn quyền truy cập trái phép bằng Middleware<br>- Hướng dẫn 4 dev còn lại clone và phân nhánh Git không xung đột. | **25%** |
| **DEV 2** | **Quản lý Sản phẩm & Danh mục (Catalog)**<br>• CRUD Danh mục & Thương hiệu<br>• CRUD Sản phẩm & Upload nhiều ảnh<br>• Tích hợp CKEditor mô tả sản phẩm<br>• Trang danh sách sản phẩm + Bộ lọc đa năng (Hãng, giá, CPU, VGA)<br>• Trang chi tiết sản phẩm + Live Search Ajax | `app/Modules/Product/*`<br>`routes/product.routes.php`<br>`views/client/product/*`<br>`views/admin/products/*`<br>`views/admin/categories/*`<br>`views/admin/brands/*` | - Thêm/Sửa/Xóa sản phẩm kèm upload nhiều ảnh mượt mà<br>- Lọc sản phẩm tức thì không giật lag<br>- Tìm kiếm gợi ý (Ajax Live Search) chuẩn như pcmarket.vn<br>- Phân trang sản phẩm mượt mà. | **20%** |
| **DEV 3** | **Giỏ hàng, Mã giảm giá & Đơn hàng (E-Commerce Flow)**<br>• Giỏ hàng Ajax (Thêm/Sửa số lượng/Xóa)<br>• Xử lý Mã giảm giá (Coupon validation)<br>• Trang thanh toán (Checkout Form)<br>• Tích hợp Demo Thanh toán (COD + QR Chuyển khoản VNPay)<br>• Quản trị Đơn hàng & Quản trị Coupon<br>• In hóa đơn đơn hàng | `app/Modules/Order/*`<br>`routes/order.routes.php`<br>`views/client/cart/*`<br>`views/admin/orders/*`<br>`views/admin/coupons/*`<br>`public/assets/js/cart.js` | - Giỏ hàng cập nhật số lượng realtime bằng Ajax<br>- Mã giảm giá kiểm tra đúng hạn, đúng số tiền tối thiểu, trừ tiền chính xác<br>- Đặt hàng tạo mã đơn `PCM-XXXX`, lưu đúng CSDL<br>- Admin xem chi tiết đơn hàng, đổi trạng thái và in hóa đơn đẹp mắt. | **20%** |
| **DEV 4** | **Tài khoản, Đánh giá, Tin tức & Slider (Interactions)**<br>• Quản lý thông tin cá nhân & Đổi mật khẩu<br>• Lịch sử đơn hàng của khách hàng<br>• Đánh giá sao (1-5 sao) & Bình luận sản phẩm<br>• Trang Tin tức công nghệ (Blog)<br>• Quản trị Blog (CKEditor), Banner Slider, Quản trị Users | `app/Modules/Interaction/*`<br>`routes/interaction.routes.php`<br>`views/client/account/*`<br>`views/client/blog/*`<br>`views/admin/users/*`<br>`views/admin/blogs/*`<br>`views/admin/reviews/*`<br>`views/admin/banners/*` | - Khách hàng xem được lịch sử đơn hàng của mình<br>- Khách hàng gửi đánh giá sao được lưu vào DB<br>- Admin duyệt/ẩn bình luận, khóa/mở tài khoản người dùng<br>- Đăng bài viết tin tức công nghệ có chèn ảnh định dạng đẹp. | **15%** |
| **DEV 5** | **Build PC (Cấu hình máy tính) & Dashboard Thống kê**<br>• **Tính năng độc quyền: Xây dựng cấu hình PC** (chọn CPU, Main, RAM, VGA, SSD, Nguồn, Vỏ case...)<br>• Tự động tính tổng tiền cấu hình<br>• Xuất file in cấu hình / Thêm cả dàn vào giỏ hàng<br>• Admin Dashboard thống kê doanh thu, số đơn, biểu đồ Chart.js | `app/Modules/PCBuilder/*`<br>`routes/builder.routes.php`<br>`views/client/pc_builder/*`<br>`views/admin/dashboard/*`<br>`public/assets/js/pc-builder.js`<br>Phối hợp làm Slide báo cáo | - Giao diện Build PC trực quan như pcmarket.vn<br>- Chọn linh kiện cộng tiền thời gian thực, in được báo giá cấu hình<br>- Dashboard Admin hiển thị 4 thẻ thống kê và 2 biểu đồ doanh thu Chart.js<br>- Hỗ trợ nhóm hoàn thành Slide thuyết trình (.pptx). | **20%** |

---

## 5. LỘ TRÌNH TRIỂN KHAI 4 TUẦN (SPRINT ROADMAP)

* **Tuần 1: Dựng nền móng (Dev 1 Lead)**
  * Dev 1 dựng Core MVC, Database Schema, Seeder mẫu, Master Layout.
  * Cả nhóm clone Git, import DB và test chạy thử.
* **Tuần 2: Phát triển song song các Module chính**
  * Dev 1: Hoàn thiện Auth, Profile, Middleware bảo mật.
  * Dev 2: CRUD Danh mục, Thương hiệu, Sản phẩm, Upload ảnh.
  * Dev 3: Giỏ hàng, Mã giảm giá Coupon, Checkout COD.
  * Dev 4: Module Tin tức Blog, Đánh giá Reviews.
  * Dev 5: Dựng giao diện PC Builder & Logic chọn linh kiện.
* **Tuần 3: Kết nối & Tính năng nâng cao**
  * Dev 2: Bộ lọc đa tiêu chí + Live search Ajax.
  * Dev 3: Admin Quản lý Đơn hàng, In hóa đơn, Demo VNPay QR.
  * Dev 4: Quản trị Banner, Quản trị Users, Duyệt bình luận.
  * Dev 5: Thêm cả dàn PC vào giỏ hàng + Thống kê Chart.js.
* **Tuần 4: Test, Tối ưu, Slide & Tập duyệt báo cáo**
  * Test responsive Mobile, bảo mật SQLi, CSRF, XSS.
  * Chuẩn bị dữ liệu demo thật bắt mắt.
  * Hoàn thiện Slide thuyết trình (.pptx) & Thuyết minh đồ án.
  * Phân vai thuyết trình 5 người & Tổng duyệt.

---

## 6. NGUYÊN TẮC GIT WORKFLOW ĐỂ KHÔNG BAO GIỜ BỊ CONFLICT

1. **Quy tắc phân nhánh (Branching):**
   * Nhánh `main`: Nhánh chạy được, chỉ Dev 1 mới có quyền merge vào đây sau khi review.
   * Nhánh `develop`: Nhánh tập hợp các tính năng đã hoàn thiện của cả nhóm.
   * Các nhánh cá nhân:
     * `feature/dev1-core-auth`
     * `feature/dev2-product-catalog`
     * `feature/dev3-cart-order-coupon`
     * `feature/dev4-blog-review-user`
     * `feature/dev5-pcbuilder-dashboard`

2. **Quy tắc làm việc hàng ngày:**
   * **Đầu mỗi buổi làm việc:** Gõ `git checkout develop` rồi `git pull origin develop`, sau đó chuyển về nhánh cá nhân và `git merge develop` để nhận code mới nhất.
   * **Tuyệt đối không sửa file của bạn khác:**
     * Dev 2 chỉ làm trong thư mục `app/Modules/Product/`, `views/client/product/`, `views/admin/products/`, `routes/product.routes.php`.
     * Dev 3 chỉ làm trong `app/Modules/Order/`, `views/client/cart/`, `views/admin/orders/`, `routes/order.routes.php`.
     * Không ai sửa trực tiếp vào `app/Core/` trừ Dev 1.
   * **Tất cả kết nối CSDL và URL:** Phải dùng biến cấu hình thông qua `.env` và hàm helper `base_url()`, không được hardcode `http://localhost/...`.

