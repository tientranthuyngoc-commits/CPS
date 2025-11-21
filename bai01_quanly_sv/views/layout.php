<?php
// Ensure UTF-8 headers
if (!headers_sent()) {
  header('Content-Type: text/html; charset=UTF-8');
}
$title = $title ?? 'CPS Shop';

// Flash helper
if (!isset($flash) && isset($_SESSION['flash'])) {
  $flash = $_SESSION['flash'];
  unset($_SESSION['flash']);
}
require_once __DIR__ . '/../includes/rbac.php';
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/layout.css">
  </head>
  <body>
    <div class="floating-gif">
      <img src="public/images/flash-sale.gif" alt="Flash Sale">
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
            <input id="searchInput" name="q" class="form-control" type="search" placeholder="Tim kiem san pham..." autocomplete="off">
            <i class="fas fa-search search-icon"></i>
            <div id="searchSuggest" class="search-suggestions"></div>
          </form>

          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
            <?php try { $categories = \App\Models\Category::all(); } catch (\Throwable $e) { $categories = []; } ?>
            <?php if (!empty($categories)): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navCat" role="button" data-bs-toggle="dropdown">
                  <i class="fas fa-list me-1"></i>Danh muc
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

            <li class="nav-item"><a class="nav-link" href="index.php?action=wishlist"><i class="fas fa-heart me-1"></i>Yeu thich</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?action=cart"><i class="fas fa-shopping-cart me-1"></i>Gio hang</a></li>

            <?php if (!empty($_SESSION['user_id'])): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navAccount" role="button" data-bs-toggle="dropdown">
                  <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['username'] ?? 'Tai khoan') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="index.php?action=account"><i class="fas fa-id-card me-2"></i>Ho so</a></li>
                  <li><a class="dropdown-item" href="index.php?action=account_orders"><i class="fas fa-box me-2"></i>Don hang cua toi</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="index.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Dang xuat</a></li>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="index.php?action=login"><i class="fas fa-sign-in-alt me-1"></i>Dang nhap</a></li>
              <li class="nav-item"><a class="nav-link" href="index.php?action=register"><i class="fas fa-user-plus me-1"></i>Dang ky</a></li>
            <?php endif; ?>

            <?php if (can('admin.panel')): ?>
              <li class="nav-item"><a class="nav-link" href="index.php?action=admin"><i class="fas fa-cog me-1"></i>Quan tri</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container">
      <?php if (can('admin.panel') && str_starts_with((string)($_GET['action'] ?? 'home'), 'admin')): ?>
        <div class="admin-nav shadow-sm mb-4">
          <ul class="nav nav-pills flex-wrap gap-2">
            <?php
              $a = $_GET['action'] ?? '';
              $link = function($act,$icon,$text) use ($a){
                $active = str_starts_with($a,$act) ? 'active' : '';
                return "<li class='nav-item'><a class='nav-link $active' href='index.php?action=$act'><i class='$icon me-2'></i>$text</a></li>";
              };
              $show = function($perm, $act, $icon, $text) use ($link){
                if ($perm === null || can($perm)) echo $link($act,$icon,$text);
              };
              echo $link('admin','fas fa-chart-line','Tong quan');
              $show('product.view','admin_products','fas fa-box','San pham');
              $show('order.view','admin_orders','fas fa-shopping-bag','Don hang');
              $show('report.view','admin_reports','fas fa-chart-pie','Bao cao');
              $show('content.manage','admin_banners','fas fa-image','Banner');
              $show('content.manage','admin_posts','fas fa-newspaper','Bai viet');
              $show('category.manage','admin_categories','fas fa-folder','Danh muc');
              $show('brand.manage','admin_brands','fas fa-award','Thuong hieu');
              $show('user.view','admin_users','fas fa-users','Nguoi dung');
              $show('tax.view','admin_tax_rates','fas fa-percent','Thue suat');
              $show('tax.view','admin_tax_categories','fas fa-layer-group','Nhom thue');
              $show('tax.view','admin_tax_mappings','fas fa-link','Anh xac thue');
              $show('report.view','admin_report_tax','fas fa-file-csv','Bao cao thue');
              $show('content.manage','admin_promotions','fas fa-bolt','Khuyen mai');
              $show('content.manage','admin_coupons','fas fa-ticket-alt','Ma giam gia');
              $show('content.manage','admin_pages','fas fa-file','Trang');
              $show('customer.view','admin_customers','fas fa-user-friends','Khach hang');
              $show('product.update','admin_attr_types','fas fa-sitemap','Nhom thuoc tinh');
              $show('product.update','admin_attrs','fas fa-tags','Thuoc tinh');
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
            <p class="text-muted">Noi ban co the mua sam truc tuyen voi nhieu san pham chat luong.</p>
          </div>
          <div class="col-md-3">
            <h6 class="mb-3">Lien ket</h6>
            <ul class="list-unstyled">
              <li><a href="index.php" class="text-muted text-decoration-none">Trang chu</a></li>
              <li><a href="index.php?action=cart" class="text-muted text-decoration-none">Gio hang</a></li>
            </ul>
          </div>
          <div class="col-md-3">
            <h6 class="mb-3">Ket noi</h6>
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
          <a href="index.php?action=product&id=${it.id}" class="d-flex align-items-center p-3 text-decoration-none suggest-item">
            <i class="fas fa-box me-3 suggest-icon"></i>
            <div class="flex-grow-1">
              <div class="fw-semibold">${it.name}</div>
              <small class="text-muted">${new Intl.NumberFormat('vi-VN').format(it.price)} ₫</small>
            </div>
            <i class="fas fa-arrow-right suggest-arrow"></i>
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
      document.querySelectorAll('.nav-item.dropdown').forEach(item => {
        item.addEventListener('mouseleave', () => {
          const dropdown = bootstrap.Dropdown.getInstance(item.querySelector('.dropdown-toggle'));
          if (dropdown) dropdown.hide();
        });
      });
    </script>
  </body>
</html>
