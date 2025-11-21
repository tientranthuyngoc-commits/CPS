<?php
$title = $product ? $product['name'] : 'San pham';
ob_start();
?>
<link rel="stylesheet" href="assets/css/product_detail.css">

<?php if (!$product): ?>
  <div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Khong tim thay san pham.
  </div>
<?php else: ?>
  <?php
    $pid = (int)$product['id'];
    $pdo = \App\Database::getInstance()->pdo();
    $imgs = $pdo->prepare('SELECT image FROM product_images WHERE product_id = :p ORDER BY sort');
    $imgs->execute([':p'=>$pid]);
    $gallery = $imgs->fetchAll(PDO::FETCH_COLUMN) ?: [];
    if (empty($gallery)) $gallery[] = $product['image'] ?: 'assets/images/placeholder.svg';
    $pi = \App\Models\Product::priceInfo($pid);
    $promo = $pi['promo_price'] ?? null;
    $hasD = $promo && $promo < (int)$product['price'];
    $discount = $hasD ? max(1, round((1 - $promo / max(1,(int)$product['price']))*100)) : 0;
    $attrs = $pdo->prepare('SELECT at.name AS type, a.name AS name FROM product_attributes pa JOIN attributes a ON pa.attribute_id = a.id JOIN attribute_types at ON a.type_id = at.id WHERE pa.product_id = :p ORDER BY at.id, a.id');
    $attrs->execute([':p'=>$pid]);
    $specs = $attrs->fetchAll(PDO::FETCH_ASSOC) ?: [];
  ?>
  <div class="product-detail">
    <div>
      <img id="mainImg" class="cover" src="<?= htmlspecialchars($gallery[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
      <?php if (count($gallery) > 1): ?>
      <div class="thumb-row">
        <?php foreach ($gallery as $g): ?>
          <img src="<?= htmlspecialchars($g) ?>" alt="thumb" class="thumb" onclick="changeImage(this,'<?= htmlspecialchars($g) ?>')">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <div>
      <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
      <div class="price-box">
        <?php if ($hasD): ?>
          <span class="price promo"><?= number_format((int)$promo,0,',','.') ?>₫</span>
          <span class="price-old"><?= number_format((int)$product['price'],0,',','.') ?>₫</span>
          <span class="price-off">-<?= $discount ?>%</span>
        <?php else: ?>
          <span class="price"><?= number_format((int)$product['price'],0,',','.') ?>₫</span>
        <?php endif; ?>
      </div>
      <div class="product-description">
        <?= nl2br(htmlspecialchars($product['description'] ?? 'Dang cap nhat thong tin...')) ?>
      </div>
      <?php if (!empty($_GET['reported'])): ?>
        <div class="alert alert-success mt-2">Da gui bao cao san pham. Cam on ban!</div>
      <?php elseif (!empty($_GET['error']) && $_GET['error']==='save_failed'): ?>
        <div class="alert alert-danger mt-2">Khong luu duoc bao cao, vui long thu lai.</div>
      <?php endif; ?>
      <form class="purchase-actions" method="post" action="index.php?action=add_to_cart">
        <input type="hidden" name="id" value="<?= $pid ?>">
        <label for="qty">So luong:</label>
        <input id="qty" type="number" name="quantity" value="1" min="1" class="qty-input">
        <button class="btn primary" type="submit"><i class="fas fa-shopping-cart me-1"></i>Them vao gio</button>
        <button class="btn" type="button" onclick="buyNow(<?= $pid ?>)">Mua ngay</button>
      </form>
      <div class="actions-row">
        <?php 
        $isInWishlist = false;
        if (!empty($_SESSION['user_id'])) {
          $pdo = \App\Database::getInstance()->pdo();
          $check = $pdo->prepare('SELECT 1 FROM wishlists WHERE user_id=:u AND product_id=:p');
          $check->execute([':u'=>(int)$_SESSION['user_id'], ':p'=>$pid]);
          $isInWishlist = $check->fetchColumn() !== false;
        }
        ?>
        <?php if ($isInWishlist): ?>
          <a class="btn btn-outline-danger btn-sm" href="index.php?action=wishlist_remove&id=<?= $pid ?>&redirect=product">
            <i class="bi bi-heart-fill me-1"></i>Da yeu thich
          </a>
        <?php else: ?>
          <?php if (empty($_SESSION['user_id'])): ?>
            <a class="btn btn-outline-danger btn-sm" href="index.php?action=login">
              <i class="bi bi-heart me-1"></i>Yeu thich
            </a>
          <?php else: ?>
            <a class="btn btn-outline-danger btn-sm" href="index.php?action=wishlist_add&id=<?= $pid ?>&redirect=product">
              <i class="bi bi-heart me-1"></i>Yeu thich
            </a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="mt-3">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <a class="btn btn-outline-danger btn-sm" href="index.php?action=report_product&id=<?= $pid ?>">
            <i class="bi bi-flag me-1"></i>Bao cao san pham
          </a>
        <?php else: ?>
          <a class="btn btn-outline-danger btn-sm" href="index.php?action=login">
            <i class="bi bi-flag me-1"></i>Dang nhap de bao cao
          </a>
        <?php endif; ?>
      </div>

      <?php if (!empty($specs)): ?>
      <div class="specs-card">
        <strong>Thong so ky thuat</strong>
        <div class="specs-list">
          <?php foreach ($specs as $s): ?>
            <div class="spec-row">
              <div class="spec-type"><?= htmlspecialchars($s['type']) ?>:</div>
              <div><?= htmlspecialchars($s['name']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php
    $rs = $pdo->prepare('SELECT rating, comment, created_at FROM ratings WHERE product_id = :p AND approved = 1 ORDER BY id DESC');
    $rs->execute([':p'=>$pid]);
    $ratings = $rs->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $avg = 0; if ($ratings) { $sum=0; foreach ($ratings as $r) { $sum += (int)($r['rating'] ?? 0); } if (count($ratings) > 0) { $avg = round($sum / count($ratings), 1); } }
  ?>
  <div class="rating-card">
    <h2 class="h5 mb-3">Danh gia & nhan xet</h2>
    <?php if (!empty($avg) && $avg > 0): ?>
      <div class="mb-2">Trung binh: <strong><?= $avg ?>/5</strong> (<?= count($ratings) ?> danh gia)</div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <form class="rating-form" method="post" action="index.php?action=product_rate">
        <input type="hidden" name="product_id" value="<?= $pid ?>">
        <label>Chon sao</label>
        <select name="rating">
          <?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?> ★</option><?php endfor; ?>
        </select>
        <input name="comment" class="rating-input" placeholder="Chia se trai nghiem...">
        <button class="btn primary">Gui</button>
      </form>
    <?php else: ?>
      <div class="alert alert-info">Ban can <a href="index.php?action=login">dang nhap</a> de danh gia.</div>
    <?php endif; ?>
    <div class="rating-list">
      <?php if (empty($ratings)): ?>
        <div class="text-muted">Chua co danh gia nao.</div>
      <?php else: ?>
        <?php foreach ($ratings as $r): ?>
          <div class="rating-item">
            <div class="rating-stars">
              <?php for($i=1;$i<=5;$i++): ?>
                <i class="fa<?= $i <= (int)$r['rating'] ? 's' : 'r' ?> fa-star"></i>
              <?php endfor; ?>
            </div>
            <div class="rating-date"><?= htmlspecialchars($r['created_at'] ?? '') ?></div>
            <div><?= htmlspecialchars($r['comment'] ?? '') ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<script>
  function changeImage(el, src){
    document.getElementById('mainImg').src = src;
  }
  function buyNow(productId) {
    const quantity = document.querySelector('input[name="quantity"]').value || 1;
    window.location.href = 'index.php?action=checkout_from&id=' + productId + '&qty=' + quantity;
  }
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
