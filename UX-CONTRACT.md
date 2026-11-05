# UX Contract

## Product context

- Audience: khách mua PC/linh kiện tại Việt Nam; admin/staff vận hành cửa hàng.
- Primary jobs: tìm và đánh giá sản phẩm, build PC, mua hàng; quản lý catalog, đơn hàng và nội dung.
- Target market: Việt Nam.
- Active locale: `vi-VN`.
- Language/content register: tiếng Việt rõ, trực tiếp; tên model và thuật ngữ hãng giữ nguyên.
- Timezone/calendar policy: `Asia/Ho_Chi_Minh`, lịch Gregory, hiển thị `dd/mm/yyyy`.
- Accessibility target: WCAG 2.2 AA.

## Business-context sources

| Domain / scope | Authoritative source | Source type | Reviewed date |
|---|---|---|---|
| Phạm vi sản phẩm và phân công | `KE_HOACH_DU_AN.md` | Kế hoạch dự án | 05/11/2026 |
| Vai trò admin/staff/customer | `database/schema.sql`, `app/Core/Auth.php`, `app/Core/Middleware.php` | Schema + implementation invariant | 05/11/2026 |
| Catalog, auth, order và PC Builder | `README.md`, `routes/*.routes.php` | Product brief + route contract | 05/11/2026 |
| Thanh toán và xóa dữ liệu | Chưa có đặc tả duy trì | Unresolved high-risk policy | 05/11/2026 |

Các quyết định thanh toán, hoàn/hủy và xóa vĩnh viễn không được suy đoán từ giao diện; phải chờ controller/API và đặc tả của module tương ứng.

## Visual contract

- Project `DESIGN.md`: `DESIGN.md`.
- Token ownership model: runtime CSS canonical, `DESIGN.md` mirror.
- Runtime token source: `public/assets/css/client.css`, `public/assets/css/admin.css`.
- Mapping: CSS variables → shared layout/component classes → PHP views.
- Token drift gate: lint `DESIGN.md`, tìm raw hex/inline-style mới trong view, browser visual check.
- Supported themes: client light và admin light; olive/paper tokens dùng chung, chi tiết tại DESIGN.md.

## Canonical UI Map

| Capability | Canonical owner | Source of truth | Allowed variants | Verification |
|---|---|---|---|---|
| Select/Listbox | Native select | `UX-CONTRACT.md` | catalog sort | keyboard + popup |
| Form | `.field` + data-validate-form + server validation | `UX-CONTRACT.md`, AuthController | login / register | validation flow |
| Scrollbar | global CSS baseline | `DESIGN.md` | stable-gutter surface | computed style |
| Toast | shared flash alert region in layouts | `UX-CONTRACT.md` | success / warning / info / error | live-region check |
| CRUD | module controller + owning list route | Module route/controller | return to list / stay on recoverable error | full flow when modules exist |

Table selection and date owner are deferred until a workflow actually uses them.

## Component behavior

| Component | Default | Hover | Focus | Active | Disabled | Busy | Error |
|---|---|---|---|---|---|---|---|
| Button | semantic intent | tonal lift | 3px focus ring | translate 1px | muted/no events | same geometry + spinner | inline/persistent status |
| Icon button | accessible name | surface lift | visible ring | pressed surface | muted | fixed square | adjacent status |
| Input | label + help | darker border | focus ring | n/a | muted | adornment slot | text + aria-invalid |
| Secret input | masked + reveal | control hover | focus stays usable | aria-pressed | muted | n/a | inline text |
| Search | clear + submit | control hover | focus ring | submit press | muted | stable adornment | retry/status |
| Table/list | stable frame | row tint | focus on controls | selected state | n/a | reserved region | persistent retry |

## Dataset navigation

- Admin tables: server pagination mặc định khi module được triển khai.
- Exploratory catalog: phân trang hoặc “Xem thêm”; không infinite scroll mặc định.
- URL state: query/filter/sort/page/page-size phải nằm trong query string.
- Empty/no-results/error/loading: phân biệt rõ, giữ footprint, có clear/retry.
- Back/scroll restoration: browser-native; giữ query string khi quay lại list.

## Flow ledger

| Operation | Trigger | Pending | Success destination | Success feedback | Failure recovery | Focus outcome | Source ref |
|---|---|---|---|---|---|---|---|
| Đăng nhập | nút “Đăng nhập” | khóa submit, giữ kích thước | admin hoặc trang chủ theo role | flash success | giữ email, báo lỗi form | lỗi đầu tiên / tiêu đề trang | `AuthController.php` |
| Đăng ký | nút “Tạo tài khoản” | khóa submit | trang chủ | flash success | giữ dữ liệu không nhạy cảm | lỗi đầu tiên | `AuthController.php` |
| Tìm kiếm | submit/300ms khi live search có owner | cancel stale | catalog với `q` | cập nhật kết quả | retry + giữ query | input/kết quả | `ProductController.php` |
| Tạo/sửa CRUD | form action | khóa submit | list sở hữu | flash success | giữ form + inline error | tiêu đề/lỗi đầu | module controller |
| Xóa | hành động explicit | trong dialog | list sở hữu | toast | lỗi trong dialog + retry | hàng kế tiếp/trigger | Chưa có policy |

## Navigation and responsive behavior

- Mỗi route có title tiếng Việt trung thực.
- 404 giữ chrome client và đường về trang chủ; 403 riêng sẽ được thêm khi permission UI hoàn thiện.
- Desktop dùng wordmark/navigation/actions và search panel; mobile có menu mở rộng trong header.
- Admin sidebar chuyển thành drawer có backdrop, focus loop, inert và Escape ở viewport dưới 992px.
- Bảng admin cuộn ngang trong panel, không khóa scroll shell.
- Sticky header không che focus; dùng `scroll-margin-top`.

## Overlays and feedback

- Dialog primitive: native HTML dialog là owner hiện tại; không dùng `alert/confirm/prompt`.
- Destructive confirmation: warning cho archive/soft-delete, danger cho hard-delete; policy phải có nguồn.
- Flash alerts: đầu nội dung, role status/alert theo mức; dismiss được.
- Layer order: native dialog top layer > admin sidebar 50 > backdrop 45 > header 20.
- Unsaved changes: sẽ dùng app-owned modal cho điều hướng nội bộ khi CRUD form được triển khai.

## Async and resilience

- Mutation mặc định pessimistic; ngăn submit lặp.
- Search 300ms, IME-safe, cancel/ignore stale response.
- Không tự retry thanh toán hoặc tác vụ side-effect không idempotent.
- Session hết hạn: đưa về đăng nhập, giữ thông báo; không giữ secret.

## Validation

- PHP/server là nguồn sự thật; HTML/JS chỉ phản hồi sớm.
- Form dùng `novalidate`; lỗi sau submit và sau đó cập nhật theo change/blur.
- Lỗi server chung ở alert; lỗi field đặt cạnh field khi controller hỗ trợ.
- Password không repopulate, masked mặc định, có reveal, autocomplete đúng.
- Submit khóa chống lặp và giữ nguyên kích thước.

## Permission and clipboard

- Admin/staff được chặn ở server bởi Middleware; UI không thay thế authorization.
- Người đã xác thực nhưng thiếu quyền cần 403 riêng khi route guards được mở rộng; không giả 404.
- Disabled action phải giải thích lý do khi không hiển nhiên.

## Verification

- Static: PHP lint tất cả file, JS syntax check, anti-pattern search. Premium audit cũ không được coi là kết quả của frontend mới.
- Browser: scripts/verify-frontend.cjs; kết quả và screenshot tại artifacts/frontend/.
- Runtime: client homepage/auth/404 và admin dashboard ở desktop + mobile, keyboard, reduced motion.
- Canonical sibling: login/register; homepage product sections; dashboard cards/table.
- CRUD/failure evidence: bổ sung khi các module Dev 2–5 tồn tại thật trong source.

