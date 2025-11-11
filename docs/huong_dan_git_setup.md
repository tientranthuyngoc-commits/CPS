# Hướng dẫn Git + Setup dự án

## 1) Yêu cầu môi trường
- Git 2.x trở lên (Windows: cài Git for Windows).
- PHP 8+ (bật PDO SQLite). XAMPP hoặc PHP standalone đều được.
- Trên Windows (XAMPP): đảm bảo PHP có quyền ghi vào `bai01_quanly_sv/data/`.

## 2) Lấy code về máy (clone)
- Tạo thư mục làm việc (ví dụ `C:\xampp\htdocs\CPS`).
- Clone repo:
  - `git clone <REPO_URL> .` (dấu chấm để clone vào thư mục hiện tại)
  - Hoặc: `git clone <REPO_URL> CPS` rồi mở thư mục `CPS`.
- Kiểm tra remote:
  - `git remote -v`
  - Thêm/sửa remote:
    - `git remote add origin <REPO_URL>`
    - `git remote set-url origin <REPO_URL>`

## 3) Cập nhật code mới nhất
- `git fetch origin`
- `git checkout <ten_nhanh>` (ví dụ `main` hoặc `develop`)
- `git pull` (mặc định từ `origin/<ten_nhanh>`)

## 4) Tạo nhánh làm việc và đẩy code
- Tạo nhánh mới từ nhánh gốc (ví dụ `develop`):
  - `git checkout develop`
  - `git pull`
  - `git checkout -b feature/<mo-ta-ngan>`
- Làm việc, thêm file và commit:
  - `git status`
  - `git add <file>` hoặc `git add .`
  - `git commit -m "<noi dung ngan gon, ro rang>"`
- Đẩy nhánh lần đầu (thiết lập upstream):
  - `git push -u origin feature/<mo-ta-ngan>`
- Sau đó chỉ cần: `git push`

## 5) Quy ước commit ngắn gọn (gợi ý)
- Dạng: `type(scope): message`
- Ví dụ: `feat(auth): thêm đăng nhập Google`, `fix(cart): sửa tính tổng tiền`
- Type gợi ý: `feat`, `fix`, `chore`, `refactor`, `docs`, `style`, `test`.

## 6) Đồng bộ khi có thay đổi từ team
- Đang ở nhánh đang làm việc: `git pull --rebase origin <base-branch>` (ví dụ `develop`)
- Giải quyết xung đột nếu có, rồi tiếp tục `git rebase --continue`
- Đẩy lại nhánh: `git push -f` (chỉ khi đã rebase, cẩn trọng khi force push)

## 7) Setup và chạy dự án
- Cấu trúc vào app chính: `bai01_quanly_sv/public/index.php` là entry router.
- CSDL: SQLite tự khởi tạo ở `bai01_quanly_sv/data/database.sqlite`.
  - Lần chạy đầu, mã sẽ tự áp dụng `data/schema.sql` và (nếu rỗng) `data/seed.sql`.
  - User admin mặc định: `admin / admin123`.
- Cách chạy với XAMPP (khuyến nghị trên Windows):
  - Đặt code tại: `C:\xampp\htdocs\CPS`.
  - Truy cập: `http://localhost/CPS/index.php` (sẽ chuyển hướng vào `bai01_quanly_sv/public/index.php`).
  - Tốt nhất: cấu hình Apache DocumentRoot vào `bai01_quanly_sv/public` để truy cập trực tiếp `http://localhost/`.
  - Đảm bảo thư mục `bai01_quanly_sv/data/` được PHP ghi (tạo file SQLite).
- Chạy nhanh bằng PHP built-in server (tuỳ chọn):
  - `cd <thu_muc_du_an>`
  - `php -S localhost:8000 -t bai01_quanly_sv/public`
  - Mở: `http://localhost:8000`

## 8) Biến môi trường (tùy chọn, cho OAuth/Thanh toán)
- Tạo file `.env` ở thư mục gốc (đã được `.gitignore`), ví dụ:
  - `GOOGLE_CLIENT_ID=...`
  - `GOOGLE_CLIENT_SECRET=...`
  - `GOOGLE_REDIRECT_URI=http://localhost/CPS/bai01_quanly_sv/public/index.php?action=oauth_google_callback`
  - `PAYOS_CLIENT_ID=...`
  - `PAYOS_API_KEY=...`
  - `PAYOS_CHECKSUM_KEY=...`
  - `MOMO_PARTNER_CODE=...`
  - `MOMO_ACCESS_KEY=...`
  - `MOMO_SECRET_KEY=...`
- Các file đọc biến: `bai01_quanly_sv/includes/auth_providers.php`, `bai01_quanly_sv/payos/config.php`, `bai01_quanly_sv/momo/config.php`.

## 9) Mẹo & xử lý sự cố
- Bật các extension PHP thường dùng trong `php.ini`: `openssl`, `curl`, `mbstring` (cần cho OAuth/API).
- Nếu muốn reset dữ liệu demo: xoá file `bai01_quanly_sv/data/database.sqlite` rồi tải lại trang để app tự tạo lại.
- Kiểm tra quyền ghi của PHP với `bai01_quanly_sv/data/` khi SQLite không tạo được.
- Cấu hình CRLF trên Windows để tránh diff không cần thiết: `git config core.autocrlf true`.

## 10) Quy trình tạo Pull Request (PR)
- Đẩy nhánh `feature/` lên `origin`.
- Tạo PR vào nhánh gốc (ví dụ `develop`).
- Assign người review, fix comment nếu có, squash hoặc rebase theo quy ước team.

---
File này tóm tắt thao tác Git cơ bản và các bước setup nhanh cho dự án CPS. Tham khảo thêm `README.md` để hiểu cấu trúc và luồng chính của ứng dụng.

