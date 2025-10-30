<?php $title = $product ? $product['name'] : 'Sản phẩm'; ob_start(); ?>

<?php if (!$product): ?>
  <div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Không tìm thấy sản phẩm.
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
      <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap">
        <?php foreach ($gallery as $g): ?>
          <img src="<?= htmlspecialchars($g) ?>" alt="thumb" style="width:56px;height:56px;object-fit:cover;border:1px solid #e5e7eb;border-radius:6px;cursor:pointer" onclick="changeImage(this,'<?= htmlspecialchars($g) ?>')">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <div>
      <h1 style="margin:0 0 8px 0; font-size:20px;">&nbsp;<?= htmlspecialchars($product['name']) ?></h1>
      <div style="margin:8px 0">
        <?php if ($hasD): ?>
          <span class="price"><?= number_format((int)$promo,0,',','.') ?>₫</span>
          <span style="text-decoration:line-through;color:#6b7280;margin-left:8px;"><?= number_format((int)$product['price'],0,',','.') ?>₫</span>
          <span style="color:#16a34a;margin-left:8px;">-<?= $discount ?>%</span>
        <?php else: ?>
          <span class="price"><?= number_format((int)$product['price'],0,',','.') ?>₫</span>
        <?php endif; ?>
      </div>
      <div style="margin:8px 0;color:#4b5563;white-space:pre-line;">
        <?= nl2br(htmlspecialchars($product['description'] ?? 'Đang cập nhật thông tin...')) ?>
      </div>
      <form method="post" action="index.php?action=add_to_cart" style="margin-top:12px">
        <input type="hidden" name="id" value="<?= $pid ?>">
        <label>Số lượng:</label>
        <input type="number" name="quantity" value="1" min="1" style="width:80px;margin:0 8px">
        <button class="btn primary" type="submit"><i class="fas fa-shopping-cart me-1"></i>Thêm vào giỏ</button>
        <button class="btn" type="button" onclick="buyNow(<?= $pid ?>)">Mua ngay</button>
      </form>
      <div style="margin-top:8px; display:flex; gap:8px; align-items:center;">
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
          <a class="btn btn-outline-danger btn-sm" href="index.php?action=wishlist_remove&id=<?= $pid ?>&redirect=product" style="text-decoration:none;">
            <i class="bi bi-heart-fill me-1"></i>Đã yêu thích
          </a>
        <?php else: ?>
          <?php if (empty($_SESSION['user_id'])): ?>
            <a class="btn btn-outline-danger btn-sm" href="index.php?action=login" style="text-decoration:none;">
              <i class="bi bi-heart me-1"></i>Yêu thích
            </a>
          <?php else: ?>
            <a class="btn btn-outline-danger btn-sm" href="index.php?action=wishlist_add&id=<?= $pid ?>&redirect=product" style="text-decoration:none;">
              <i class="bi bi-heart me-1"></i>Yêu thích
            </a>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <?php if (!empty($specs)): ?>
      <div style="margin-top:16px">
        <strong>Thông số kỹ thuật</strong>
        <div style="margin-top:8px">
          <?php foreach ($specs as $s): ?>
            <div style="display:flex;gap:8px;border-bottom:1px solid #e5e7eb;padding:6px 0">
              <div style="width:160px;color:#374151;"><?= htmlspecialchars($s['type']) ?>:</div>
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
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-top:16px">
    <h2 class="h5" style="margin:0 0 12px 0">Đánh giá & nhận xét</h2>
    <?php if (!empty($avg) && $avg > 0): ?>
      <div style="margin-bottom:8px">Trung bình: <strong><?= $avg ?>/5</strong> (<?= count($ratings) ?> đánh giá)</div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <form method="post" action="index.php?action=product_rate" style="margin:12px 0">
        <input type="hidden" name="product_id" value="<?= $pid ?>">
        <label>Chọn sao</label>
        <select name="rating">
          <?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?> ★</option><?php endfor; ?>
        </select>
        <input name="comment" placeholder="Chia sẻ trải nghiệm của bạn..." style="width:60%;margin:0 8px">
        <button class="btn primary">Gửi</button>
      </form>
    <?php else: ?>
      <div class="alert alert-info">Bạn cần <a href="index.php?action=login">đăng nhập</a> để đánh giá.</div>
    <?php endif; ?>
    <div>
      <?php if (empty($ratings)): ?>
        <div class="text-muted">Chưa có đánh giá nào.</div>
      <?php else: ?>
        <?php foreach ($ratings as $r): ?>
          <div style="border-top:1px solid #e5e7eb;padding:8px 0">
            <div style="color:#f59e0b">
              <?php for($i=1;$i<=5;$i++): ?>
                <i class="fas fa-star<?= $i <= (int)($r['rating'] ?? 0) ? '' : ' text-muted' ?>"></i>
              <?php endfor; ?>
            </div>
            <div style="font-size:12px;color:#6b7280;">&nbsp;<?= htmlspecialchars($r['created_at'] ?? '') ?></div>
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
<style>
/* === Khung chi tiết sản phẩm === */
.product-detail {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  align-items: start;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  margin-top: 12px;
}

/* === Ảnh sản phẩm === */
.product-detail img.cover {
  width: 100%;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  object-fit: cover;
  transition: transform 0.3s ease;
}
.product-detail img.cover:hover {
  transform: scale(1.03);
}

/* === Ảnh thumbnail === */
.product-detail div img[onclick] {
  transition: transform 0.2s ease, border-color 0.2s;
}
.product-detail div img[onclick]:hover {
  transform: scale(1.1);
  border-color: #3b82f6;
}

/* === Giá tiền === */
.price {
  color: #e11d48;
  font-weight: bold;
  font-size: 20px;
}

/* === Nút bấm === */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #f3f4f6;
  color: #111827;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 6px 12px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.25s ease;
}
.btn:hover {
  background: #e5e7eb;
  transform: translateY(-1px);
}
.btn.primary {
  background: #3b82f6;
  color: white;
  border: none;
}
.btn.primary:hover {
  background: #2563eb;
}

/* === Bảng thông số kỹ thuật === */
.product-detail strong {
  font-size: 16px;
  color: #111827;
}
.product-detail .specs div {
  padding: 6px 0;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  gap: 8px;
}

/* === Phần đánh giá === */
.product-detail + div {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

.fa-star {
  color: #fbbf24;
  margin-right: 2px;
}

/* === Responsive === */
@media (max-width: 768px) {
  .product-detail {
    grid-template-columns: 1fr;
  }
  .product-detail img.cover {
    max-height: 300px;
  }
}
</style>

