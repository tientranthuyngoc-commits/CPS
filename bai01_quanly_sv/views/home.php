<?php $title = 'Trang chủ'; ob_start(); ?>
 <link rel="stylesheet" href="/assets/css/index.css">
<style>
  .hero-banner {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    margin-bottom: 3rem;
  }
  .carousel-item {
    height: 400px;
  transition: transform 1s ease-in-out; /* hiệu ứng trượt mượt */
}

  .hero-banner img { height: 400px; max-width: 1200px; }
  /* ==== SECTION HEADER NỔI BẬT ==== */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.75rem;
  padding: 0.75rem 1.25rem;
  border: 2px solid rgba(37, 99, 235, 0.3); /* viền màu xanh nhạt */
  border-radius: 14px;
  background: linear-gradient(135deg, #f9fafb, #eef2ff);
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}

.section-header:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.12);
  border-color: rgba(37, 99, 235, 0.6);
}

/* Tiêu đề chính trong section */
.section-header .section-title {
  font-size: 1.9rem;
  font-weight: 800;
  color: #1e3a8a; /* xanh đậm */
  letter-spacing: 0.5px;
  position: relative;
  margin: 0;
}

/* Các nút điều hướng trong section */
.section-header .btn {
  border-radius: 50%;
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  transition: all 0.25s ease;
}

.section-header .btn:hover {
  background-color: #2563eb;
  color: #fff;
  transform: scale(1.1);
}

  .section-title { font-size:1.75rem; font-weight:700; margin:0; }
  .scroll-container { display:flex; gap:1.5rem; overflow-x:auto; scroll-behavior:smooth; padding:1rem .5rem; -webkit-overflow-scrolling:touch; scrollbar-width:thin; }
  .scroll-container::-webkit-scrollbar { height:8px; }
  .scroll-container::-webkit-scrollbar-thumb { background: var(--primary); border-radius:10px; }
  .product-card-mini { min-width:220px; flex-shrink:0; border-radius:16px; overflow:hidden; background:#fff; box-shadow:0 4px 15px rgba(0,0,0,.1); transition:all .3s ease; }
  .product-card-mini:hover { transform:translateY(-8px); box-shadow:0 12px 30px rgba(0,0,0,.2); }
  .product-card-mini img { height:180px; object-fit:cover; }
  .discount-badge { position:absolute; top:10px; right:10px; background:#ef4444; color:#fff; padding:.4rem .8rem; border-radius:8px; font-weight:700; font-size:.875rem; box-shadow:0 2px 8px rgba(239,68,68,.4); }
  .filter-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:20px; padding:2rem; color:#fff; margin-bottom:2rem; }
  .filter-card .form-label { color:#fff; font-weight:600; }
  .filter-card .form-control, .filter-card .form-select { border-radius:10px; border:2px solid rgba(255,255,255,.3); background:rgba(255,255,255,.9); }
  .product-grid-card { height:100%; border-radius:16px; overflow:hidden; transition:all .3s ease; position:relative; }
  .product-grid-card img { height:240px; object-fit:cover; transition: transform .3s ease; }
  .product-grid-card:hover img { transform: scale(1.1); }
  .price-section { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
  .price-original { font-size:1.5rem; font-weight:800; }
  .price-promo { font-size:1.75rem; font-weight:800; color:#dc2626; }
  .price-old { font-size:1rem; color:#9ca3af; text-decoration:line-through; }
  .category-sidebar { background:#fff; border-radius:16px; padding:1.5rem; box-shadow:0 4px 15px rgba(0,0,0,.1); }
  .category-item { padding:.75rem 1rem; border-radius:10px; transition:all .2s ease; display:flex; justify-content:space-between; align-items:center; }
  .category-item:hover { background: rgba(37,99,235,.12); transform: translateX(4px); }
</style>

<?php $banners = \App\Models\Banner::allActive(); if (!empty($banners)): ?>
<div id="hero" class="carousel slide hero-banner" data-bs-ride="carousel" data-bs-interval="2000">
  <div class="carousel-indicators">
    <?php foreach ($banners as $i => $bn): ?>
      <button type="button" data-bs-target="#hero" data-bs-slide-to="<?= $i ?>" <?= $i===0 ? 'class="active"' : '' ?>></button>
    <?php endforeach; ?>
  </div>
  <div class="carousel-inner">
    <?php foreach ($banners as $i => $bn): ?>
      <div class="carousel-item <?= $i===0 ? 'active' : '' ?>">
        <?php if (!empty($bn['link'])): ?><a href="<?= htmlspecialchars($bn['link']) ?>"><?php endif; ?>
        <img src="<?= htmlspecialchars($bn['image']) ?>" class="d-block w-100" alt="">
        <?php if (!empty($bn['link'])): ?></a><?php endif; ?>
        <?php if (!empty($bn['title'])): ?>
        <div class="carousel-caption"><h5><?= htmlspecialchars($bn['title']) ?></h5></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#hero" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
  <button class="carousel-control-next" type="button" data-bs-target="#hero" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>
<?php endif; ?>

<?php $deals = \App\Models\Product::getDeals(12); if (!empty($deals)): ?>
<div class="mb-5">
  <div class="section-header">
    <h2 class="section-title">Deal Hot</h2>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-primary btn-sm" id="dealPrev">‹</button>
      <button class="btn btn-outline-primary btn-sm" id="dealNext">›</button>
    </div>
  </div>
  <div id="dealTrack" class="scroll-container">
    <?php foreach ($deals as $fp):
      $img = $fp['image'] ?: 'assets/images/placeholder.svg';
      $fspath = __DIR__ . '/../public/' . ltrim($img,'/');
      if (!is_file($fspath)) $img = 'assets/images/placeholder.svg';
      $promo = (int)($fp['promo_price'] ?? 0);
      $price = (int)$fp['price'];
      $hasD = $promo>0 && $promo < $price;
      $discount = $hasD ? max(1, round((1 - $promo / max(1,$price))*100)) : 0;
    ?>
      <a href="index.php?action=product&id=<?= (int)$fp['id'] ?>" class="text-decoration-none">
        <div class="product-card-mini position-relative">
          <?php if ($hasD): ?><div class="discount-badge">-<?= $discount ?>%</div><?php endif; ?>
          <img src="<?= htmlspecialchars($img) ?>" class="w-100" alt="">
          <div class="p-3">
            <div class="fw-semibold mb-2" style="height:2.8rem; overflow:hidden;">
              <?= htmlspecialchars($fp['name']) ?>
            </div>
            <?php if ($hasD): ?>
              <div class="price-promo mb-1"><?= number_format($promo,0,',','.') ?>₫</div>
              <div class="price-old"><?= number_format($price,0,',','.') ?>₫</div>
            <?php else: ?>
              <div class="price-original"><?= number_format($price,0,',','.') ?>₫</div>
            <?php endif; ?>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="mb-5">
  <div class="section-header">
    <h2 class="section-title">Sản phẩm nổi bật</h2>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-primary btn-sm" id="featPrev">‹</button>
      <button class="btn btn-outline-primary btn-sm" id="featNext">›</button>
    </div>
  </div>
  <div id="featTrack" class="scroll-container">
    <?php foreach (($featured ?? []) as $fp):
      $img = $fp['image'] ?: 'assets/images/placeholder.svg';
      $fspath = __DIR__ . '/../public/' . ltrim($img,'/');
      if (!is_file($fspath)) $img = 'assets/images/placeholder.svg';
      $pi = \App\Models\Product::priceInfo((int)$fp['id']);
      $promo = $pi['promo_price'] ?? null;
      $hasD = $promo && $promo<(int)$fp['price'];
      $discount = $hasD ? max(1, round((1 - $promo / max(1,(int)$fp['price']))*100)) : 0;
    ?>
      <a href="index.php?action=product&id=<?= (int)$fp['id'] ?>" class="text-decoration-none">
        <div class="product-card-mini position-relative">
          <?php if ($hasD): ?><div class="discount-badge">-<?= $discount ?>%</div><?php endif; ?>
          <img src="<?= htmlspecialchars($img) ?>" class="w-100" alt="">
          <div class="p-3">
            <div class="fw-semibold mb-2" style="height:2.8rem; overflow:hidden;">
              <?= htmlspecialchars($fp['name']) ?>
            </div>
            <?php if ($hasD): ?>
              <div class="price-promo mb-1"><?= number_format((int)$promo,0,',','.') ?>₫</div>
              <div class="price-old"><?= number_format((int)$fp['price'],0,',','.') ?>₫</div>
            <?php else: ?>
              <div class="price-original"><?= number_format((int)$fp['price'],0,',','.') ?>₫</div>
            <?php endif; ?>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (!empty($attributeTypes)): ?>
  <div class="filter-card shadow-lg">
    <div class="d-flex align-items-center mb-3">
      <h3 class="h5 mb-0">Bộ lọc sản phẩm</h3>
      <button class="btn btn-light btn-sm ms-auto d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">▼</button>
    </div>
    <div id="filtersCollapse" class="collapse show">
      <form method="get" action="index.php" class="row g-3">
        <input type="hidden" name="action" value="home">
        <?php if (!empty($searchQuery)): ?>
          <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
        <?php endif; ?>
        <?php if (!empty($catId)): ?>
          <input type="hidden" name="cat" value="<?= (int)$catId ?>">
        <?php endif; ?>
        <?php $selected = array_flip($attrIds ?? []); foreach ($attributeTypes as $t): ?>
          <div class="col-md-3">
            <label class="form-label"><?= htmlspecialchars($t['name']) ?></label>
            <div class="bg-white border rounded p-2" style="max-height:180px; overflow:auto; border-radius:10px;">
              <?php foreach ($t['attributes'] as $a): $id=(int)$a['id']; ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="a<?= $id ?>" name="attrs[]" value="<?= $id ?>" <?= isset($selected[$id])?'checked':'' ?>>
                  <label for="a<?= $id ?>" class="form-check-label"><?= htmlspecialchars($a['name']) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="col-md-12">
          <div class="row g-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label">Khoảng giá</label>
              <div class="input-group">
                <input type="number" class="form-control" name="min_price" placeholder="Từ" value="<?= (int)($paging['min_price'] ?? 0) ?>" min="0">
                <span class="input-group-text">-</span>
                <input type="number" class="form-control" name="max_price" placeholder="Đến" value="<?= (int)($paging['max_price'] ?? 0) ?>" min="0">
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Đánh giá</label>
              <select class="form-select" name="rating_min">
                <option value="">Tất cả</option>
                <?php foreach ([5,4,3] as $rv): ?>
                  <option value="<?= $rv ?>" <?= ((int)($paging['rating_min'] ?? 0) === $rv)? 'selected':'' ?>><?= $rv ?>★ trở lên</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Sắp xếp</label>
              <select class="form-select" name="sort">
                <option value="newest" <?= ($paging['sort'] ?? '')==='newest'?'selected':'' ?>>Mới nhất</option>
                <option value="price_asc" <?= ($paging['sort'] ?? '')==='price_asc'?'selected':'' ?>>Giá tăng dần</option>
                <option value="price_desc" <?= ($paging['sort'] ?? '')==='price_desc'?'selected':'' ?>>Giá giảm dần</option>
              </select>
            </div>
            <div class="col-md-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="promo" id="promo" value="1" <?= !empty($paging['promo'])?'checked':'' ?>>
                <label for="promo" class="form-check-label">Khuyến mãi</label>
              </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
              <button class="btn btn-light flex-fill" type="submit">Tìm kiếm</button>
              <a class="btn btn-outline-light" href="index.php">↻</a>
            </div>
          </div>
        </div>
        <div class="col-12"><div class="alert alert-light mb-0">Tìm thấy <strong><?= (int)($paging['total'] ?? 0) ?></strong> sản phẩm</div></div>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="section-header mt-5"><h2 class="section-title">Sản phẩm mới</h2></div>
<div class="row g-4 mb-5">
  <?php foreach ($products as $p): ?>
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card product-grid-card shadow-sm">
        <?php 
          $imgPath = $p['image'] ?: 'assets/images/placeholder.svg';
          $fsPath = __DIR__ . '/../public/' . ltrim($imgPath, '/');
          if (!is_file($fsPath)) { $imgPath = 'assets/images/placeholder.svg'; }
          $priceInfo = \App\Models\Product::priceInfo((int)$p['id']);
          $promo = $priceInfo['promo_price'] ?? null;
          $hasDiscount = $promo && $promo > 0 && $promo < (int)$p['price'];
          $discount = $hasDiscount ? max(1, round((1 - $promo / max(1,(int)$p['price']))*100)) : 0;
        ?>
        <?php if ($hasDiscount): ?><div class="discount-badge">-<?= $discount ?>%</div><?php endif; ?>
        <div style="overflow:hidden;"><img src="<?= htmlspecialchars($imgPath) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>"></div>
        <div class="card-body p-3">
          <h6 class="card-title mb-3" style="font-weight:600; height:2.8rem; overflow:hidden;"> <?= htmlspecialchars($p['name']) ?> </h6>
          <div class="price-section mb-3">
            <?php if ($hasDiscount): ?>
              <span class="price-promo"><?= number_format((int)$promo,0,',','.') ?>₫</span>
              <span class="price-old"><?= number_format((int)$p['price'],0,',','.') ?>₫</span>
            <?php else: ?>
              <span class="price-original"><?= number_format((int)$p['price'],0,',','.') ?>₫</span>
            <?php endif; ?>
          </div>
          <div class="d-grid gap-2">
            <a class="btn btn-primary btn-sm" href="index.php?action=product&id=<?= (int)$p['id'] ?>">Xem chi tiết</a>
            <form method="post" action="index.php?action=add_to_cart">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn btn-outline-primary btn-sm w-100" type="submit">Thêm vào giỏ</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($products)): ?><div class="col-12"><div class="alert alert-warning">Chưa có sản phẩm phù hợp.</div></div><?php endif; ?>
</div>

<?php if (($paging['pages'] ?? 1) > 1): ?>
<nav>
  <ul class="pagination justify-content-center">
    <?php 
      $build = function($p) use ($searchQuery,$catId,$attrIds,$paging){
        $qs = ['action'=>'home','page'=>$p,'sort'=>$paging['sort']??'newest'];
        if (!empty($searchQuery)) $qs['q']=$searchQuery; if (!empty($catId)) $qs['cat']=$catId; 
        if (!empty($attrIds)) foreach ($attrIds as $a) $qs['attrs[]'][]=$a; 
        return 'index.php?'.http_build_query($qs);
      };
      $page = (int)($paging['page'] ?? 1); $pages=(int)($paging['pages'] ?? 1);
    ?>
    <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="<?= $build(max(1,$page-1)) ?>">‹</a></li>
    <?php for($i=max(1,$page-2); $i<=min($pages,$page+2); $i++): ?>
      <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="<?= $build($i) ?>"><?= $i ?></a></li>
    <?php endfor; ?>
    <li class="page-item <?= $page>=$pages?'disabled':'' ?>"><a class="page-link" href="<?= $build(min($pages,$page+1)) ?>">›</a></li>
  </ul>
</nav>
<?php endif; ?>

<div class="row g-4 mt-4">
  <?php if (!empty($latestPosts)): ?>
  <div class="col-lg-8">
    <div class="card shadow-sm p-4">
      <div class="section-header"><h2 class="h5 mb-0">Tin tức & Ưu đãi</h2></div>
      <div class="row g-3">
        <?php foreach ($latestPosts as $post): ?>
          <div class="col-md-6">
            <a class="text-decoration-none" href="index.php?action=post&id=<?= (int)$post['id'] ?>">
              <div class="card h-100 border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                <img src="<?= htmlspecialchars($post['cover'] ?: 'assets/images/placeholder.svg') ?>" alt="" style="height:180px; object-fit:cover;" class="w-100">
                <div class="card-body">
                  <h6 class="fw-semibold mb-2" style="height:3rem; overflow:hidden;"> <?= htmlspecialchars($post['title']) ?> </h6>
                  <small class="text-muted"> <?= htmlspecialchars($post['created_at']) ?> </small>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <div class="col-lg-4">
    <div class="category-sidebar">
      <div class="section-header"><h2 class="h5 mb-0">Danh mục</h2></div>
      <div class="list-group list-group-flush">
        <?php $i=0; foreach (($categoryList ?? []) as $c): if ($i++>=10) break; ?>
          <a class="list-group-item list-group-item-action category-item border-0" href="index.php?action=home&cat=<?= (int)$c['id'] ?>">
            <span><?= htmlspecialchars($c['name']) ?></span>
            <span>›</span>
          </a>
        <?php endforeach; ?>
        <?php if (empty($categoryList)): ?>
          <div class="text-muted text-center py-3">Chưa có danh mục</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const t = document.getElementById('featTrack');
  const prev = document.getElementById('featPrev');
  const next = document.getElementById('featNext');
  if (t && prev && next) { prev.addEventListener('click', ()=> t.scrollBy({left:-400, behavior:'smooth'})); next.addEventListener('click', ()=> t.scrollBy({left:400, behavior:'smooth'})); }
  const dt = document.getElementById('dealTrack');
  const dp = document.getElementById('dealPrev');
  const dn = document.getElementById('dealNext');
  if (dt && dp && dn) { dp.addEventListener('click', ()=> dt.scrollBy({left:-400, behavior:'smooth'})); dn.addEventListener('click', ()=> dt.scrollBy({left:400, behavior:'smooth'})); }
})();
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
