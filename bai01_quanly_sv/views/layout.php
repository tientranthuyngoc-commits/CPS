<?php
// Đặt charset UTF-8 trước mọi output (ngừa lỗi chữ)
if (!headers_sent()) {
  header('Content-Type: text/html; charset=UTF-8');
}
$title = $title ?? 'CPS Shop';

// Hỗ trợ flash (nếu dùng session flash)
if (!isset($flash) && isset($_SESSION['flash'])) {
  $flash = $_SESSION['flash'];
  unset($_SESSION['flash']);
}
?>
<!doctype html>
<html lang="vi">
  <head> <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
      :root {
        --primary: #4c00ffff;
        --border: #ddd;
        --bg-light: #f9f9f9;
        --text-dark: #222;
      }

      /* ==== Navbar ==== */
      .navbar-sci-fi {
        background: linear-gradient(90deg, #4f66d4ff, #995bebff);
        color: white;
      }
      .navbar-sci-fi .navbar-brand,
      .navbar-sci-fi .nav-link {
        color: white !important;
        transition: 0.3s;
      }
      .navbar-sci-fi .nav-link:hover {
        color: #070610ff !important;
      }

      /* ==== Search Box ==== */
      .search-wrapper {
        position: relative;
      }
      .search-wrapper input {
        border-radius: 25px;
        padding-left: 40px;
        transition: all 0.3s ease;
      }
      .search-wrapper input:focus {
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
      }
      .search-wrapper .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: gray;
      }

      /* ===== Dropdown mở khi hover ===== */
.nav-item.dropdown:hover .dropdown-menu {
  display: block;
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.navbar .dropdown-menu {
  margin-top: 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.2s ease;
}
/* ==== Dropdown item hover đẹp ==== */
.dropdown-menu .dropdown-item {
  padding: 0.6rem 1rem;
  border-radius: 8px;
  transition: all 0.25s ease;
  color: #333;
  font-weight: 500;
}

.dropdown-menu .dropdown-item:hover {
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
  color: #6680f2ff !important;
  transform: translateX(4px);
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}



      /* ==== Admin Nav ==== */
      .admin-nav {
        background: var(--bg-light);
        border-radius: 12px;
        padding: 10px;
      }
      .admin-nav .nav-link {
        border-radius: 20px;
      }
      .admin-nav .nav-link.active {
        background: var(--primary);
        color: white !important;
      }

      /* ==== Footer ==== */
      footer {
        background: var(--bg-light);
        color: var(--text-dark);
      }
      footer h5, footer h6 {
        color: var(--primary);
      }
      footer a:hover {
        text-decoration: underline;
      }

      /* ==== Responsive ==== */
      @media (max-width: 768px) {
        .search-wrapper input {
          font-size: 14px;
        }
        .navbar-sci-fi {
          background: #007bff;
        }
      }
      
    </style>
    </head>
<div class="floating-gif">
  <img src="public/images/flash-sale.gif" alt="Flash Sale">
</div>

<style>
.floating-gif {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 1050;
  width: 100px;
  height: auto;
  animation: floatUpDown 2s ease-in-out infinite;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.floating-gif:hover {
  transform: scale(1.15);
}

.floating-gif img {
  width: 100%;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.25);
}

@keyframes floatUpDown {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}
</style>

  <body>
</div>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top navbar-sci-fi">
      <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-shopping-bag me-2"></i>CPS Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
          <form class="me-3 flex-grow-1 search-wrapper" role="search" action="index.php" method="get">
            <input type="hidden" name="action" value="home">
            <input id="searchInput" name="q" class="form-control" type="search" placeholder="Tìm kiếm sản phẩm..." autocomplete="off">
            <i class="fas fa-search search-icon"></i>
            <div id="searchSuggest" class="search-suggestions"
                 style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1000; background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.15); margin-top:.5rem;"></div>
          </form>

          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
            <?php try { $categories = \App\Models\Category::all(); } catch (\Throwable $e) { $categories = []; } ?>
            <?php if (!empty($categories)): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navCat" role="button" data-bs-toggle="dropdown">
                  <i class="fas fa-list me-1"></i>Danh mục
                </a>
                <ul class="dropdown-menu">
                  <?php foreach ($categories as $c): ?>
                    <li><a class="dropdown-item" href="index.php?action=brand&category=<?= (int)$c['id'] ?>">
                      <?= htmlspecialchars($c['name']) ?>
                    </a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php endif; ?>

            <li class="nav-item"><a class="nav-link" href="index.php?action=wishlist"><i class="fas fa-heart me-1"></i>Yêu thích</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?action=cart"><i class="fas fa-shopping-cart me-1"></i>Giỏ hàng</a></li>

            <?php if (!empty($_SESSION['user_id'])): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navAccount" role="button" data-bs-toggle="dropdown">
                  <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['username'] ?? 'Tài khoản') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="index.php?action=account"><i class="fas fa-id-card me-2"></i>Hồ sơ</a></li>
                  <li><a class="dropdown-item" href="index.php?action=account_orders"><i class="fas fa-box me-2"></i>Đơn hàng của tôi</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="index.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="index.php?action=login"><i class="fas fa-sign-in-alt me-1"></i>Đăng nhập</a></li>
              <li class="nav-item"><a class="nav-link" href="index.php?action=register"><i class="fas fa-user-plus me-1"></i>Đăng ký</a></li>
            <?php endif; ?>

            <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
              <li class="nav-item"><a class="nav-link" href="index.php?action=admin"><i class="fas fa-cog me-1"></i>Quản trị</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container">
      <?php if (($_SESSION['role'] ?? '') === 'admin' && str_starts_with((string)($_GET['action'] ?? 'home'), 'admin')): ?>
        <div class="admin-nav shadow-sm mb-4">
          <ul class="nav nav-pills flex-wrap gap-2">
            <?php
              $a = $_GET['action'] ?? '';
              $link = function($act,$icon,$text) use ($a){
                $active = str_starts_with($a,$act) ? 'active' : '';
                return "<li class='nav-item'><a class='nav-link $active' href='index.php?action=$act'><i class='$icon me-2'></i>$text</a></li>";
              };
              echo $link('admin','fas fa-chart-line','Tổng quan');
              echo $link('admin_products','fas fa-box','Sản phẩm');
              echo $link('admin_orders','fas fa-shopping-bag','Đơn hàng');
              echo $link('admin_reports','fas fa-chart-pie','Báo cáo');
              echo $link('admin_banners','fas fa-image','Banner');
              echo $link('admin_posts','fas fa-newspaper','Bài viết');
              echo $link('admin_categories','fas fa-folder','Danh mục');
              echo $link('admin_brands','fas fa-award','Thương hiệu');
              echo $link('admin_users','fas fa-users','Người dùng');
              echo $link('admin_tax_rates','fas fa-percent','Thuế suất');
              echo $link('admin_tax_categories','fas fa-layer-group','Nhóm thuế');
              echo $link('admin_tax_mappings','fas fa-link','Ánh xạ thuế');
              echo $link('admin_report_tax','fas fa-file-csv','Báo cáo thuế');
              echo $link('admin_promotions','fas fa-bolt','Khuyến mãi');
              echo $link('admin_coupons','fas fa-ticket-alt','Mã giảm giá');
              echo $link('admin_pages','fas fa-file','Trang');
              echo $link('admin_customers','fas fa-user-friends','Khách hàng');
              echo $link('admin_attr_types','fas fa-sitemap','Nhóm thuộc tính');
              echo $link('admin_attrs','fas fa-tags','Thuộc tính');
            ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (!empty($flash)): ?>
        <div class="alert alert-info alert-dismissible fade show">
          <i class="fas fa-info-circle me-2"></i><?= htmlspecialchars($flash) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?= $content ?? '' ?>
    </main>

    <footer class="py-4">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <h5 class="mb-3"><i class="fas fa-shopping-bag me-2"></i>CPS Shop</h5>
            <p class="text-muted">Địa chỉ mua sắm trực tuyến tin cậy với hàng ngàn sản phẩm chất lượng.</p>
          </div>
          <div class="col-md-3">
            <h6 class="mb-3">Liên kết</h6>
            <ul class="list-unstyled">
              <li><a href="index.php" class="text-muted text-decoration-none">Trang chủ</a></li>
              <li><a href="index.php?action=cart" class="text-muted text-decoration-none">Giỏ hàng</a></li>
            </ul>
          </div>
          <div class="col-md-3">
            <h6 class="mb-3">Kết nối</h6>
            <div class="d-flex gap-3">
              <a href="#" class="text-primary fs-4"><i class="fab fa-facebook"></i></a>
              <a href="#" class="text-info fs-4"><i class="fab fa-twitter"></i></a>
              <a href="#" class="text-danger fs-4"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
        </div>
        <hr class="my-4">
        <div class="text-center text-muted"><small>&copy; <?= date('Y') ?> CPS Shop. All rights reserved.</small></div>
      </div>
    </footer>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      const input = document.getElementById('searchInput');
      const box = document.getElementById('searchSuggest');

      function renderSuggest(items){
        if(!box) return;
        if(!items || !items.length){
          box.style.display='none'; box.innerHTML=''; return;
        }
        box.innerHTML = items.map(it => `
          <a href="index.php?action=product&id=${it.id}" class="d-flex align-items-center p-3 text-decoration-none" style="color:#333;">
            <i class="fas fa-box me-3" style="color:var(--primary);"></i>
            <div class="flex-grow-1">
              <div class="fw-semibold">${it.name}</div>
              <small class="text-muted">${new Intl.NumberFormat('vi-VN').format(it.price)} ₫</small>
            </div>
            <i class="fas fa-arrow-right" style="color:var(--primary);"></i>
          </a>
        `).join('');
        box.style.display = 'block';
      }

      async function suggest(q){
        if(!q || q.length < 2){ renderSuggest([]); return; }
        try{
          const r = await fetch(`index.php?action=search_suggest&q=${encodeURIComponent(q)}`);
          const data = await r.json();
          renderSuggest(data);
        }catch(e){ renderSuggest([]); }
      }

      if(input){
        input.addEventListener('input', e => suggest(e.target.value));
        input.addEventListener('focus', e => suggest(e.target.value));
        document.addEventListener('click', e => {
          if(!box.contains(e.target) && e.target !== input){ renderSuggest([]); }
        });
      }
    </script>
    <script>
  // Ngăn việc nhấp chuột làm dropdown dính mở
  document.querySelectorAll('.nav-item.dropdown').forEach(item => {
    item.addEventListener('mouseleave', () => {
      const dropdown = bootstrap.Dropdown.getInstance(item.querySelector('.dropdown-toggle'));
      if (dropdown) dropdown.hide();
    });
  });
</script>
  </body>
</html>
