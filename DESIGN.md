---
version: alpha
name: "PCMarket — Everyday Computing"
description: "Cửa hàng máy tính sáng, thoáng; xanh olive, nền giấy và ảnh góc làm việc thật."
colors:
  primary: "#49634E"
  primary-dark: "#354C3A"
  ink: "#303D35"
  muted: "#6B756B"
  background: "#F7F8F3"
  surface: "#FFFFFF"
  sage: "#E8EDDF"
  border: "#DCE1D6"
  error: "#A44039"
typography:
  body:
    fontFamily: '"Be Vietnam Pro", sans-serif'
  accent:
    fontFamily: 'Georgia, "Times New Roman", serif'
rounded:
  DEFAULT: "0.25rem"
  sm: "0.125rem"
spacing:
  page-max: "92.5rem"
  page-gutter: "3rem"
  section-gap: "4.375rem"
components:
  header: {}
  product: {}
  catalog: {}
  form: {}
  dialog: {}
  dashboard: {}
---

# PCMarket — Everyday Computing

## Direction

Thiết kế lại toàn bộ view hiện có theo yêu cầu bỏ bố cục cũ và bảng màu đỏ/đen.
Cửa hàng mang cảm giác một không gian máy tính dành cho công việc, sáng tạo và giải trí:
nền giấy sáng, xanh olive tiết chế, ảnh không gian thật và sản phẩm có giá rõ ràng.

Không giữ carousel, cụm banner khuyến mãi, menu category hai tầng hay các khối Bootstrap cũ.
Storefront và quản trị đều dùng CSS riêng, cùng font và semantic color tokens.

## Runtime ownership

- `public/assets/css/client.css`: tokens và toàn bộ storefront.
- `public/assets/css/admin.css`: bố cục không gian quản trị, kế thừa tokens client.
- `public/assets/js/ui.js`: search/menu, form validation, password reveal, dialog, drawer admin.
- `views/client/partials/product_card.php`: product component dùng chung.
- Không thêm framework build hoặc thay đổi controller/model/router/database.

## Color

Olive #49634E dùng cho CTA, wordmark accent và các tương tác.
Ink #303D35 là màu chữ. Paper #F7F8F3 là nền chính; sage #E8EDDF cho khu nội dung nhấn.
Bề mặt quản trị là trắng trên nền sáng. Màu trạng thái luôn kèm nhãn chữ.
Đỏ chỉ dùng cho lỗi form; không dùng đỏ làm màu thương hiệu hoặc màu giá.

## Typography

Be Vietnam Pro cho nội dung và dữ liệu; Georgia italic chỉ cho từ nhấn trong hero.
H1 desktop 35–59px, mobile 41px; section heading 24–34px.
Tên sản phẩm desktop 14px, mobile 12px; giá đậm hơn phần mô tả.
Không dùng toàn bộ tiêu đề/CTA viết hoa. Eyebrow nhỏ dùng để phân loại section.

## Composition

Khung tối đa 1480px. Gutter desktop 48px, tablet 24–32px, mobile 18px.

Trang chủ:
1. Header wordmark + navigation + actions; tìm kiếm là panel riêng.
2. Intro text và một ảnh góc máy tính tĩnh.
3. Dải điều hướng theo nhu cầu: game, sáng tạo, học/làm việc, nâng cấp.
4. Máy tính bộ dạng product grid, không khung card nặng.
5. Editorial split ảnh và nội dung khám phá linh kiện.
6. Grid linh kiện.
7. Nguyên tắc mua sắm và footer sáng.

Catalog dùng sidebar desktop, dải danh mục cuộn ngang mobile.
Query, category và sort lưu ở URL; view trình bày/lọc dữ liệu mà controller hiện có trả về.
Chi tiết dùng ảnh lớn, mô tả, giá, tồn kho và bảng thông số.

Auth dùng ảnh một bên và form bên còn lại; mobile chỉ giữ form.
Thông tin tài khoản demo nằm trong disclosure, không trộn vào nội dung chính.

Admin: sidebar sáng cố định, topbar thấp, KPI một hàng, biểu đồ giá trị đơn thực tế và bảng đơn.
Biểu đồ dùng dữ liệu `recentOrders` của controller, không hiển thị số doanh thu tự dựng.
Sidebar mobile có backdrop, Escape, focus loop và inert khi đóng.

## Interaction and accessibility

- Main landmark, skip link, heading hierarchy, label và accessible name.
- Form xác thực có CSRF và giữ action/name tương thích backend.
- Client validation trước submit; server vẫn là nguồn xác thực cuối cùng.
- Lỗi field nối bằng aria-describedby; focus lỗi đầu tiên.
- Password masked mặc định, reveal giữ focus và aria-pressed.
- Native HTML dialog cho thông báo chức năng chưa được triển khai.
- Không tạo đường dẫn đến controller chưa tồn tại trong điều hướng hoạt động.
- Không giả lập thành công cho cart/checkout/PC Builder.
- Reduced motion: tắt transition và smooth scroll.
- Product image lỗi có fallback local; kích thước ảnh giữ ổn định.

## Imagery

Ảnh không gian lưu local, nguồn Unsplash:
- `workspace-monitor.jpg`: https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd
- `workspace.jpg`: https://images.unsplash.com/photo-1496181133206-80ce9b88a853
- `desk-detail.jpg`: https://images.unsplash.com/photo-1498050108023-c5249f4df085

Ảnh catalog giữ nguyên dữ liệu backend. Ảnh không gian chỉ minh họa, không đại diện cấu hình được bán.
Không dùng ảnh AI hoặc tạo ảnh sản phẩm sai model.

## Verification and existing data limits

Chạy `scripts/verify-frontend.cjs` với Playwright/Chrome để kiểm tra luồng thật và xuất ảnh.
Kết quả: `artifacts/frontend/result.json`; ảnh desktop/mobile nằm cùng thư mục.
PHP lint và JS syntax check chạy riêng.

Database hiện tại có tên tiếng Việt lỗi mã hóa và một số thumbnail mẫu không khớp sản phẩm.
Không thay đổi database để tuân thủ phạm vi chỉ frontend.
Cart/checkout/builder/profile/CRUD chưa có controller triển khai; UI báo trạng thái thay vì giả lập nghiệp vụ.
