# BÁO CÁO TIẾN ĐỘ THỰC HIỆN DỰ ÁN WEBPC

**Phân hệ phụ trách:** Dev 3 – Giỏ hàng, Mã giảm giá, Thanh toán & Quản lý Đơn hàng  
**Phiên bản cập nhật:** 1.1  
**Trạng thái phân hệ:** Hoàn thành 100% phạm vi công việc được giao (Definition of Done)

---

## 1. MỤC TIÊU & PHẠM VI TRÁCH NHIỆM (DEV 3)

Theo tài liệu *Kế hoạch phát triển và bàn giao dự án WebPC*, Dev 3 chịu trách nhiệm toàn bộ phân hệ mua hàng và quản trị đơn hàng, bao gồm:
* Xây dựng giỏ hàng (Session/Ajax), cập nhật số lượng, xóa sản phẩm.
* Xử lý mã giảm giá (Coupon): Kiểm tra điều kiện, thời hạn, lượt dùng, giảm theo số tiền hoặc phần trăm có trần tối đa.
* Xử lý Thanh toán (Checkout): Form thông tin khách hàng, hỗ trợ COD và Chuyển khoản QR Banking.
* Xử lý Transaction đặt hàng an toàn tại Server: Thêm `orders`, `order_items`, tự động trừ kho sản phẩm và cộng số lượt dùng coupon.
* Phân hệ Admin Quản lý Đơn hàng: Xem danh sách, chi tiết đơn, chuyển trạng thái đơn và trạng thái thanh toán.
* Phân hệ Admin Quản lý Mã giảm giá: Đầy đủ tính năng CRUD (Thêm, Sửa, Xóa, Danh sách mã giảm giá).

---

## 2. DANH SÁCH CÁC MÃ NGUỒN & FILE ĐÃ HOÀN THÀNH

### 2.1 Backend Models & Controllers (`app/Modules/Order/` & `app/Modules/Coupon/`)
* **`OrderModel.php`**: 
  * Truy vấn thông tin đơn hàng, danh sách chi tiết đơn (`order_items`) và kiểm tra lại toàn bộ giá/tồn kho trực tiếp từ DB (`evaluateCart()`).
  * Thực thi `beginTransaction()`, `commit()`, `rollBack()` khi tạo đơn: kiểm tra & trừ tồn kho nguyên tử (`status = 1 AND quantity >= ?`), tăng lượt dùng coupon có điều kiện (`used_count < usage_limit`).
  * Cập nhật trạng thái đơn và thanh toán (`pending`, `confirmed`, `shipping`, `completed`, `cancelled`; `unpaid`, `paid`) với cơ chế tự động hoàn tồn kho & lượt dùng coupon khi hủy đơn (`cancelled`) và trừ lại khi mở lại đơn.
* **`CouponModel.php`**: 
  * Kiểm tra tính hợp lệ của coupon qua `validateAndCalculate()` (so sánh ngày `Y-m-d`, `min_order_amount`, `usage_limit`, giảm cố định/phần trăm kèm trần `max_discount`).
  * Thực hiện CRUD mã giảm giá khớp chuẩn 100% với cấu trúc CSDL thực tế.
* **`CartController.php`, `CouponController.php` & `CheckoutController.php`**: 
  * Thêm/sửa/xóa sản phẩm giỏ hàng via Session & Ajax API, đồng bộ lại mã giảm giá khi giỏ thay đổi, kiểm tra CSRF (`Middleware::csrf()`).
  * Tự động tính toán lại toàn bộ giá & mã giảm giá từ Database khi Checkout, chống submit lặp qua `checkout_nonce`, giữ `payment_status = 'unpaid'` cho cả COD và Banking chờ đối soát, và kiểm tra quyền xem trang thành công.
* **`AdminOrderController.php`**: 
  * Bảo vệ truy cập bằng `Middleware::staff()` và `Middleware::csrf()`.
  * Hiển thị danh sách đơn hàng, chi tiết sản phẩm khách đặt, cập nhật trạng thái (`pending`, `confirmed`, `shipping`, `completed`, `cancelled`) và thanh toán (`unpaid`, `paid`).
* **`AdminCouponController.php`**: 
  * Bảo vệ truy cập bằng `Middleware::staff()` và `Middleware::csrf()`.
  * Quản trị toàn bộ mã giảm giá, kiểm tra trùng mã, định dạng ngày `end_date >= start_date`, loại giảm (`fixed` / `percentage`), giá trị hợp lệ và giới hạn lượt dùng.

### 2.2 Frontend Views (`views/`)
* **Client Side:**
  * `views/client/order/cart.php`: Giao diện giỏ hàng, cập nhật số lượng và áp dụng mã giảm giá Ajax qua `cart.js`.
  * `views/client/order/checkout.php`: Form đặt hàng, `csrf_field()`, `checkout_nonce` và hướng dẫn thanh toán COD/Banking.
  * `views/client/order/success.php`: Thông báo đặt hàng thành công, hiển thị bảng sản phẩm đã đặt và mã VietQR kèm nội dung chuyển khoản khi chọn Banking.
  * Kết nối đồng bộ trên `client_header.php`, `client_footer.php`, `views/client/product/detail.php` (nút Thêm vào giỏ hàng), `views/client/partials/pcm_card.php` (`product_image_url()`).
* **Admin Side:**
  * `views/admin/order/index.php`: Danh sách đơn hàng toàn hệ thống.
  * `views/admin/order/detail.php`: Xem chi tiết đơn hàng & form cập nhật trạng thái kèm CSRF token.
  * `views/admin/coupon/index.php`: Danh sách mã giảm giá, hiển thị lượt dùng, hạn dùng, giá trị giảm và form xóa có CSRF.
  * `views/admin/coupon/form.php`: Form thêm mới & chỉnh sửa mã giảm giá dùng chung có CSRF token.
  * Mở khóa liên kết **Đơn hàng** và **Khuyến mãi** trong `views/layouts/admin_sidebar.php`.

### 2.3 JavaScript & Routes
* **`public/assets/js/cart.js` & `public/assets/js/pcm-homepage.js`**: Gửi kèm `X-CSRF-TOKEN`, cập nhật đồng bộ `.bag-count`, xử lý lỗi HTTP 403/422 rõ ràng và tính lại tổng tiền / mã giảm giá mượt mà.
* **`routes/order.routes.php` & `routes/coupon.routes.php`**: Khai báo chuẩn xác và không trùng lặp các định tuyến cho cả Client, Ajax API và Admin.

---

## 3. KẾT QUẢ ĐỒNG BỘ VÀ TỐI ƯU KỸ THUẬT

1. **Đồng bộ Schema Cơ sở dữ liệu (`coupons` & `orders`):**
   * Khớp chính xác ENUM bảng `orders`: `status` (`pending`, `confirmed`, `shipping`, `completed`, `cancelled`) và `payment_status` (`unpaid`, `paid`).
   * Khớp các cột thực tế bảng `coupons`: `code`, `description`, `type` (`percentage`, `fixed`), `discount_value`, `min_order_amount`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`.

2. **Tương thích PHP 8.1+ / PHP 8.4+ & Hiển thị ảnh:**
   * Bổ sung `product_image_url()` trong `app/Core/Helper.php` để xử lý nhất quán đường dẫn `assets/images/products/...`, `uploads/...`, URL ngoài và ảnh dự phòng.
   * Xử lý an toàn kiểu dữ liệu, kiểm tra mảng và định dạng tiền tệ.

3. **Bảo mật & An toàn dữ liệu:**
   * Áp dụng `Middleware::staff()` cho toàn bộ trang Admin Đơn hàng & Mã giảm giá; áp dụng `Middleware::csrf()` cho mọi form POST và API (`/api/cart/*`, `/api/coupon/*`).
   * 100% câu lệnh SQL sử dụng PDO Prepared Statements và giao dịch nguyên tử (Transaction + Row Lock) khi đặt hàng hoặc hủy đơn.
   * Tất cả giá trị tiền tệ, tồn kho và mã giảm giá đều được xác thực lại tại Server-side trước khi ghi vào Database.

---

## 4. TIÊU CHÍ NGHIỆM THU THEO KẾ HOẠCH DỰ ÁN (DEFINITION OF DONE)

| Tiêu chí nghiệm thu theo Kế hoạch | Kết quả thực tế | Trạng thái |
| :--- | :--- | :---: |
| Giỏ hàng Session/Ajax, tăng/giảm/xóa | Thêm, sửa số lượng, xóa sản phẩm mượt mà không reload trang | **ĐẠT** |
| Áp dụng Mã giảm giá (Coupon) | Kiểm tra chuẩn đơn tối thiểu, hạn dùng, trần giảm % tối đa | **ĐẠT** |
| Checkout COD & QR Banking | Đặt hàng thành công, lưu đầy đủ thông tin khách hàng, QR thanh toán | **ĐẠT** |
| Ràng buộc Transaction SQL | Đơn hàng, chi tiết đơn, trừ tồn kho và tăng lượt coupon chạy trong 1 Transaction | **ĐẠT** |
| Admin Quản lý Đơn hàng | Xem danh sách, chi tiết đơn, chuyển trạng thái đơn và thanh toán thành công | **ĐẠT** |
| Admin Quản lý Mã giảm giá | CRUD mã giảm giá hoàn thiện, giao diện trực quan, không phát sinh lỗi PHP | **ĐẠT** |

---

## 5. BÀN GIAO CHO CÁC THÀNH VIÊN CÒN LẠI

* **Bàn giao dữ liệu cho Dev 5 (Dashboard & Analytics):** Data đơn hàng đã chuẩn hóa để Dev 5 thực hiện truy vấn doanh thu thật và vẽ biểu đồ Chart.js.
* **Tích hợp cùng Dev 4 (Profile & Lịch sử đơn hàng):** Đã sẵn sàng các phương thức lấy danh sách đơn hàng theo `user_id` để hiển thị trong trang cá nhân của khách hàng.
