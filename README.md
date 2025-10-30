# CPS PHP App — Tổng hợp cấu trúc & ghi chú

## Tổng quan
- Ứng dụng PHP (kiểu MVC nhẹ) cho cửa hàng/e‑commerce mẫu.
- Router chính qua `bai01_quanly_sv/public/index.php` với tham số `action`.
- CSDL SQLite: tự tạo bảng/migration nhẹ và seed dữ liệu mặc định.
- Tài khoản quản trị mặc định (seed): `admin / admin123`.

## Yêu cầu
- PHP 8+ với PDO SQLite bật sẵn.
- Môi trường dev: XAMPP (Apache + PHP) hoặc PHP Built-in server.

## Chạy nhanh
- Truy cập `http://localhost/CPS/index.php` (tự redirect vào `bai01_quanly_sv/public/index.php`).
- Hoặc cấu hình DocumentRoot trỏ tới `bai01_quanly_sv/public` và mở `http://localhost/`.

## Cấu trúc thư mục chính
- `bai01_quanly_sv/public/`: Entry point và route (`index.php`), các endpoint OAuth/Thanh toán (PayOS, MoMo), demo, QR ngân hàng.
- `bai01_quanly_sv/src/`: Mã nguồn PHP (Controllers, Models, Services) và `Database.php`.
- `bai01_quanly_sv/views/`: Giao diện (user + admin).
- `bai01_quanly_sv/includes/`: Cấu hình OAuth/LDAP, tiện ích.
- `bai01_quanly_sv/data/`: `schema.sql`, `seed.sql`, `database.sqlite`, `app_config.json`.
- `bai01_quanly_sv/payos/`, `bai01_quanly_sv/momo/`: Tích hợp cổng thanh toán.
- `bai01_quanly_sv/administrator/`: Thành phần quản trị cũ/phụ trợ (elements_LTT...).
- `men/men/`: Mẫu trang/legacy (checkout, customer_orders, includes/auth_guard.php).
- `backup/`: Ảnh chụp sao lưu code/asset trước đó.

## Tệp trọng tâm (theo các tab bạn đang mở)
- `bai01_quanly_sv/src/Controllers/AuthController.php`: Đăng nhập/đăng xuất; hỗ trợ fallback LDAP; view `login_utf8.php`.
- `bai01_quanly_sv/src/Services/TaxCalculator.php`: Tính thuế theo tax category; hỗ trợ kiểu inclusive/exclusive.
- `bai01_quanly_sv/src/Database.php`: Kết nối SQLite, tạo bảng (migrate nhẹ), seed VAT10 và admin mặc định.
- `bai01_quanly_sv/public/oauth_google_start.php`, `bai01_quanly_sv/public/oauth_google_callback.php`: OAuth Google (bắt đầu + callback).
- `bai01_quanly_sv/includes/auth_providers.php`: Cấu hình LDAP, Google, Facebook (cần thay `client_id/client_secret` phù hợp môi trường).
- `bai01_quanly_sv/src/Controllers/BrandController.php`: Trang/logic thương hiệu.
- `bai01_quanly_sv/src/Controllers/AccountController.php`: Đăng ký, quên mật khẩu, đặt lại, hồ sơ, đơn hàng tài khoản.
- `bai01_quanly_sv/public/index.php`: Router chính switch theo `action` (home, product, cart, checkout, admin_*, oauth_*, wishlist...).
- Views thường dùng: `views/login_utf8.php`, `views/admin/orders_list.php`, `views/admin/user_form.php`, `views/product_detail.php`, `views/admin/posts_list.php`, `views/brand.php`, `views/error_404.php`, `views/error_500.php`, `views/admin/brand_form.php`, `views/admin/product_form.php`, `views/account_orders.php`, `views/invoice.php`, `views/login.php`, `views/register.php`, `views/forgot.php`, `views/reset.php`, `views/layout.php`, `views/admin_dashboard.php`, `views/wishlist.php`, `views/account_profile.php`.
- Models tiêu biểu: `src/Models/Product.php`, `src/Models/Page.php`, `src/Models/Post.php`, `src/Models/Brand.php`.
- Controllers khác: `HomeController.php`, `ProductController.php`, `CartController.php`, `CheckoutController.php`, `AdminController.php`, `PostController.php`.
- Thuế: `views/admin/tax_rates.php`, `views/admin/tax_rate_form.php`, `views/admin/tax_categories.php`, `views/admin/tax_category_form.php`.

## CSDL & khởi tạo
- Tệp CSDL: `bai01_quanly_sv/data/database.sqlite` (tự tạo nếu chưa tồn tại).
- Schema: `bai01_quanly_sv/data/schema.sql` chạy tự động khi khởi động nếu đọc được.
- Seed dữ liệu: `bai01_quanly_sv/data/seed.sql` chỉ chạy khi bảng `products` trống.
- `Database.php` còn đảm bảo (idempotent) các cột mới cho `users`, `orders`, `order_items`, tạo bảng `brands`, `shipping_zones`, `tax_*` và seed `tax_rates` (VAT10) khi rỗng.

## Cấu hình hiển thị
- `bai01_quanly_sv/data/app_config.json`:
  - `featured_strategy`: cách chọn sản phẩm nổi bật (`newest`, ...)
  - `featured_min_reviews`, `featured_min_rating`: ngưỡng lọc.

## Xác thực & OAuth
- `bai01_quanly_sv/includes/auth_providers.php` cấu hình LDAP, Google, Facebook.
  - Hãy thay thế `client_id`, `client_secret`, `redirect_uri` cho phù hợp dự án thật.
  - Có ví dụ redirect: `http://localhost/CPS/bai01_quanly_sv/public/index.php?action=oauth_google_callback`.

## Thanh toán
- PayOS:
  - Cấu hình: `bai01_quanly_sv/payos/config.php`.
  - Luồng: `public/payos_create.php` → người dùng thanh toán → `public/payos_return.php`; webhook: `public/payos_webhook.php`.
- MoMo:
  - Cấu hình: `bai01_quanly_sv/momo/config.php`, SDK: `bai01_quanly_sv/momo/Momo.php`.
  - Endpoint: `public/momo_create.php`, `public/momo_ipn.php`, `public/momo_demo.php`.

## Ghi chú bảo mật
- Không commit secrets (OAuth/Payment keys) lên repo công khai.
- Giá trị mẫu trong `includes/auth_providers.php` chỉ để phát triển nội bộ — hãy thay bằng thông tin ứng dụng của bạn.

## Mẹo triển khai
- Nếu dùng Apache, trỏ DocumentRoot tới `bai01_quanly_sv/public` để ẩn mã nguồn và truy cập trực tiếp qua `index.php` public.
- Đảm bảo PHP có quyền ghi thư mục `bai01_quanly_sv/data/` để tạo `database.sqlite`.

---
Tệp này tổng hợp nhanh cấu trúc và các tệp chính để bạn tra cứu nhanh khi làm việc trong dự án.

