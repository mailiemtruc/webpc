# BÁO CÁO TIẾN ĐỘ THỰC HIỆN DỰ ÁN WEBPC

**Phân hệ phụ trách:** Dev 3 – Giỏ hàng, Mã giảm giá, Thanh toán & Quản lý Đơn hàng  
**Phiên bản cập nhật:** 1.0  
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
  * Truy vấn thông tin đơn hàng, danh sách chi tiết đơn (`order_items`).
  * Thực thi `beginTransaction()`, `commit()`, `rollBack()` khi tạo đơn.
  * Tự động trừ tồn kho (`UPDATE products SET quantity = quantity - ?`).
  * Cập nhật trạng thái đơn và thanh toán với Prepared Statements an toàn.
* **`CouponModel.php`**:
  * Kiểm tra tính hợp lệ của coupon (thời hạn `start_date` / `end_date`, giới hạn `usage_limit`, đơn tối thiểu `min_order_amount`).
  * Thực hiện CRUD mã giảm giá khớp chuẩn 100% với cấu trúc CSDL thực tế.
* **`CartController.php` & `CheckoutController.php`**:
  * Thêm/sửa/xóa sản phẩm giỏ hàng via Session & Ajax API.
  * Tự động tính toán lại tổng tiền từ Database (không tin tưởng dữ liệu giá do Client gửi lên).
* **`AdminOrderController.php`**:
  * Hiển thị danh sách đơn hàng, chi tiết sản phẩm khách đặt.
  * Cập nhật trạng thái giao hàng (`pending`, `processing`, `completed`, `cancelled`) và thanh toán (`unpaid`, `paid`).
* **`AdminCouponController.php`**:
  * Quản trị toàn bộ mã giảm giá, xử lý validate trùng mã, loại giảm (`fixed` / `percentage`), mức giảm tối đa.

### 2.2 Frontend Views (`views/`)
* **Client Side:**
  * `views/client/cart/index.php`: Giao diện giỏ hàng, nhập mã giảm giá Ajax.
  * `views/client/cart/checkout.php`: Form đặt hàng và hướng dẫn thanh toán.
  * `views/client/cart/success.php`: Thông báo đặt hàng thành công.
* **Admin Side:**
  * `views/admin/orders/index.php`: Danh sách đơn hàng toàn hệ thống.
  * `views/admin/orders/detail.php`: Xem chi tiết đơn hàng & form cập nhật trạng thái.
  * `views/admin/coupon/index.php`: Danh sách mã giảm giá, hiển thị lượt dùng, hạn dùng, giá trị giảm.
  * `views/admin/coupon/form.php`: Form thêm mới & chỉnh sửa mã giảm giá dùng chung linh hoạt.

### 2.3 JavaScript & Routes
* **`public/assets/js/cart.js`**: Xử lý tăng giảm số lượng, xóa giỏ hàng và áp dụng mã giảm giá bằng Fetch API/Ajax mượt mà.
* **`routes/order.routes.php` & `routes/coupon.routes.php`**: Khai báo đầy đủ các định tuyến cho cả Client và Admin.

---

## 3. KẾT QUẢ ĐỒNG BỘ VÀ TỐI ƯU KỸ THUẬT

1. **Đồng bộ Schema Cơ sở dữ liệu (`coupons` & `orders`):**
   * Khớp các cột thực tế: `code`, `description`, `type` (`percentage`, `fixed`), `discount_value`, `min_order_amount`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`.
   * Khắc phục triệt để lỗi MySQL Strict Mode khi cập nhật cột trạng thái `status` trong bảng `orders`.

2. **Tương thích PHP 8.1+ / PHP 8.4+:**
   * Xử lý triệt để các cảnh báo `Deprecated: number_format()` khi gặp giá trị `null`.
   * Bọc kiểm tra an toàn `Undefined array key` cho các trường mảng và truy vấn CSDL.

3. **Bảo mật & An toàn dữ liệu:**
   * 100% câu lệnh SQL sử dụng PDO Prepared Statements (Named parameters `:status`, `:id`) chống lỗi SQL Injection.
   * Tất cả giá trị tiền tệ và số lượng đều được tính toán lại tại Server-side trước khi ghi vào Database.

---

## 4. TIÊU CHÍ NGHIỆM THU THEO KẾ HOẠCH DỰ ÁN (DEFINITION OF DONE)

| Tiêu chí nghiệm初 theo Kế hoạch | Kết quả thực tế | Trạng thái |
| :--- | :--- | :---: |
| Giỏ hàng Session/Ajax, tăng/giảm/xóa | Thêm, sửa số lượng, xóa sản phẩm mượt mà không reload trang | **ĐẠT** |
| Áp dụng Mã giảm giá (Coupon) | Kiểm tra chuẩn đơn tối thiểu, hạn dùng, trần giảm % tối đa | **ĐẠT** |
| Checkout COD & QR Banking | Đặt hàng thành công, lưu đầy đủ thông tin khách hàng | **ĐẠT** |
| Ràng buộc Transaction SQL | Đơn hàng, chi tiết đơn, trừ tồn kho và tăng lượt coupon chạy trong 1 Transaction | **ĐẠT** |
| Admin Quản lý Đơn hàng | Xem danh sách, chi tiết đơn, chuyển trạng thái đơn và thanh toán thành công | **ĐẠT** |
| Admin Quản lý Mã giảm giá | CRUD mã giảm giá hoàn thiện, giao diện trực quan, không phát sinh lỗi PHP | **ĐẠT** |

---

## 5. BÀN GIAO CHO CÁC THÀNH VIÊN CÒN LẠI

* **Bàn giao dữ liệu cho Dev 5 (Dashboard & Analytics):** Data đơn hàng đã chuẩn hóa để Dev 5 thực hiện truy vấn doanh thu thật và vẽ biểu đồ Chart.js.
* **Tích hợp cùng Dev 4 (Profile & Lịch sử đơn hàng):** Đã sẵn sàng các phương thức lấy danh sách đơn hàng theo `user_id` để hiển thị trong trang cá nhân của khách hàng.