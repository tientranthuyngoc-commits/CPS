PRAGMA foreign_keys=ON;

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  price INTEGER NOT NULL,
  image TEXT,
  sku TEXT,
  stock INTEGER DEFAULT 0,
  status TEXT DEFAULT 'active',
  brand_id INTEGER REFERENCES brands(id),
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS brands (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT UNIQUE NOT NULL,
  slug TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  customer_name TEXT NOT NULL,
  phone TEXT NOT NULL,
  address TEXT NOT NULL,
  total INTEGER NOT NULL,
  status TEXT DEFAULT 'pending',
  payment_status TEXT DEFAULT 'unpaid',
  payment_method TEXT,
  shipping_method TEXT,
  shipping_fee INTEGER DEFAULT 0,
  tax INTEGER DEFAULT 0,
  discount_total INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id INTEGER NOT NULL REFERENCES products(id),
  quantity INTEGER NOT NULL,
  price INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  role TEXT DEFAULT 'admin',
  email TEXT,
  phone TEXT,
  avatar TEXT,
  is_active INTEGER DEFAULT 1,
  email_verified_at TEXT,
  reset_token TEXT,
  reset_expires TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS addresses (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  name TEXT,
  phone TEXT,
  address_line TEXT NOT NULL,
  ward TEXT,
  district TEXT,
  province TEXT,
  is_default INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Extra structures migrated from MEN: categories and product_categories
CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS product_categories (
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
  PRIMARY KEY (product_id, category_id)
);

-- Promotions (giá khuyến mãi theo thời gian giống TGDD)
CREATE TABLE IF NOT EXISTS promotions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  promo_price INTEGER NOT NULL,
  starts_at TEXT NOT NULL,
  ends_at TEXT,
  active INTEGER DEFAULT 1,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Product attributes for filtering (giống men/TGDD ở mức tối thiểu)
CREATE TABLE IF NOT EXISTS attribute_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS attributes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  type_id INTEGER NOT NULL REFERENCES attribute_types(id) ON DELETE CASCADE,
  name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS product_attributes (
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  attribute_id INTEGER NOT NULL REFERENCES attributes(id) ON DELETE CASCADE,
  value TEXT,
  PRIMARY KEY (product_id, attribute_id)
);

-- Product images (gallery)
CREATE TABLE IF NOT EXISTS product_images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  image TEXT NOT NULL,
  sort INTEGER DEFAULT 0
);

-- Banners (hero slider)
CREATE TABLE IF NOT EXISTS banners (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  image TEXT NOT NULL,
  title TEXT,
  link TEXT,
  sort INTEGER DEFAULT 0,
  active INTEGER DEFAULT 1,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Posts (tin tức / ưu đãi)
CREATE TABLE IF NOT EXISTS posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  slug TEXT,
  cover TEXT,
  content TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Pages (giới thiệu/chính sách...)
CREATE TABLE IF NOT EXISTS pages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  slug TEXT UNIQUE,
  content TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Ratings (đánh giá cơ bản để lọc theo sao)
CREATE TABLE IF NOT EXISTS ratings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  user_id INTEGER,
  rating INTEGER NOT NULL,
  comment TEXT,
  approved INTEGER DEFAULT 1,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Yêu cầu đổi/trả hàng của người dùng (demo)
CREATE TABLE IF NOT EXISTS order_returns (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  user_id INTEGER,
  reason TEXT,
  status TEXT DEFAULT 'requested', -- requested|approved|rejected
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Wishlist: lưu sản phẩm yêu thích của người dùng
CREATE TABLE IF NOT EXISTS wishlists (
  user_id INTEGER NOT NULL,
  product_id INTEGER NOT NULL,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(user_id, product_id)
);

-- Coupons (mã giảm giá)
CREATE TABLE IF NOT EXISTS coupons (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  code TEXT UNIQUE NOT NULL,
  type TEXT NOT NULL, -- percent|fixed
  value INTEGER NOT NULL,
  min_order INTEGER DEFAULT 0,
  valid_from TEXT,
  valid_to TEXT,
  active INTEGER DEFAULT 1,
  usage_limit INTEGER,
  used_count INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Shipping zones (cấu hình phí theo khu vực)
CREATE TABLE IF NOT EXISTS shipping_zones (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  fee INTEGER NOT NULL DEFAULT 30000,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Cấu trúc bổ sung (theo ERD tiếng Việt)
-- Không thay đổi cấu trúc cũ để tránh lỗi tương thích.
-- Bảng sử dụng tên tiếng Việt độc lập với các bảng hiện tại.
-- =========================

-- DANHMUC (danh mục sản phẩm)
CREATE TABLE IF NOT EXISTS danhmuc (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ten_danhmuc TEXT NOT NULL,
  mo_ta TEXT,
  loai TEXT
);

-- NHACUNGCAP
CREATE TABLE IF NOT EXISTS nhacungcap (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ma_ncc TEXT UNIQUE,
  ten_cong_ty TEXT,
  dia_chi TEXT,
  so_dien_thoai TEXT,
  email TEXT
);

-- NHANVIEN
CREATE TABLE IF NOT EXISTS nhanvien (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ma_nv TEXT UNIQUE,
  ho_ten TEXT,
  email TEXT,
  so_dien_thoai TEXT,
  dia_chi TEXT,
  chuc_vu TEXT
);

-- KHACHHANG
CREATE TABLE IF NOT EXISTS khachhang (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ma_kh TEXT UNIQUE,
  ho_ten TEXT,
  email TEXT,
  so_dien_thoai TEXT,
  dia_chi TEXT
);

-- SANPHAM (độc lập với bảng products sẵn có)
CREATE TABLE IF NOT EXISTS sanpham (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ma_sp TEXT UNIQUE,
  ten_sp TEXT NOT NULL,
  mo_ta TEXT,
  so_luong_ton INTEGER DEFAULT 0,
  don_vi_tinh TEXT,
  hinh_anh TEXT,
  id_danhmuc INTEGER REFERENCES danhmuc(id) ON DELETE SET NULL,
  id_nhacungcap INTEGER REFERENCES nhacungcap(id) ON DELETE SET NULL,
  hang TEXT,
  gia_von REAL DEFAULT 0
);

-- GIABAN: lịch sử giá bán cho SANPHAM
CREATE TABLE IF NOT EXISTS giaban (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  id_sanpham INTEGER NOT NULL REFERENCES sanpham(id) ON DELETE CASCADE,
  gia_ban REAL NOT NULL,
  ngay_ap_dung TEXT NOT NULL,
  ngay_ket_thuc TEXT
);
CREATE INDEX IF NOT EXISTS idx_giaban_sp ON giaban(id_sanpham);

-- DONHANG (độc lập với orders)
CREATE TABLE IF NOT EXISTS donhang (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ma_dh TEXT UNIQUE,
  ngay_dat TEXT,
  tong_tien REAL DEFAULT 0,
  trang_thai TEXT,
  dia_chi_giao_hang TEXT,
  phuong_thuc_thanh_toan TEXT,
  id_khachhang INTEGER REFERENCES khachhang(id) ON DELETE SET NULL,
  id_nhanvien INTEGER REFERENCES nhanvien(id) ON DELETE SET NULL
);

-- CHITIETDONHANG
CREATE TABLE IF NOT EXISTS chitietdonhang (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  id_donhang INTEGER NOT NULL REFERENCES donhang(id) ON DELETE CASCADE,
  id_sanpham INTEGER NOT NULL REFERENCES sanpham(id),
  so_luong INTEGER NOT NULL,
  gia_ban REAL NOT NULL,
  thanh_tien REAL NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_ctdh_dh ON chitietdonhang(id_donhang);
CREATE INDEX IF NOT EXISTS idx_ctdh_sp ON chitietdonhang(id_sanpham);

-- PHIEUNHAP
CREATE TABLE IF NOT EXISTS phieunhap (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ma_phieu TEXT UNIQUE,
  ngay_nhap TEXT,
  tong_tien REAL DEFAULT 0,
  ghi_chu TEXT,
  id_nhanvien INTEGER REFERENCES nhanvien(id) ON DELETE SET NULL,
  id_nhacungcap INTEGER REFERENCES nhacungcap(id) ON DELETE SET NULL
);

-- CHITIETPHIEUNHAP
CREATE TABLE IF NOT EXISTS chitietphieunhap (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  id_phieunhap INTEGER NOT NULL REFERENCES phieunhap(id) ON DELETE CASCADE,
  id_sanpham INTEGER NOT NULL REFERENCES sanpham(id),
  so_luong INTEGER NOT NULL,
  gia_nhap REAL NOT NULL,
  thanh_tien REAL NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_ctpn_pn ON chitietphieunhap(id_phieunhap);
CREATE INDEX IF NOT EXISTS idx_ctpn_sp ON chitietphieunhap(id_sanpham);
