<?php $title = 'Yêu thích'; ob_start(); ?>
<h1 class="h4 mb-3">Sản phẩm yêu thích</h1>
<?php if (empty($items)): ?>
  <div class="alert alert-info">Danh sách yêu thích trống. <a href="index.php">Tiếp tục mua sắm</a></div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($items as $p): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm" style="border-radius:12px; overflow:hidden;">
          <img src="<?= htmlspecialchars($p['image'] ?: 'assets/images/placeholder.svg') ?>" class="card-img-top" style="height:200px; object-fit:cover;">
          <div class="card-body p-3">
            <h6 class="card-title mb-2" style="font-weight:600; height:2.4rem; overflow:hidden;">
              <?= htmlspecialchars($p['name']) ?>
            </h6>
            <div class="mb-2"><span class="h6" style="font-weight:700; color:#333;"><?= number_format((int)$p['price'],0,',','.') ?>₫</span></div>
            <div class="d-grid gap-2">
              <a class="btn btn-primary btn-sm" href="index.php?action=product&id=<?= (int)$p['id'] ?>">Xem chi tiết</a>
              <a class="btn btn-outline-danger btn-sm" href="index.php?action=wishlist_remove&id=<?= (int)$p['id'] ?>">Bỏ khỏi yêu thích</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

