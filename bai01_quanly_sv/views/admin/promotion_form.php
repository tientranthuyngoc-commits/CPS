<?php $title = ($promotion? 'Sửa' : 'Thêm') . ' khuyến mãi'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>

<form method="post" action="index.php?action=admin_promotion_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($promotion['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Sản phẩm</label>
      <select name="product_id" class="form-select" required>
        <?php foreach (($products ?? []) as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= (!empty($promotion) && (int)$promotion['product_id']===(int)$p['id'])? 'selected':'' ?>>
            <?= htmlspecialchars($p['name']) ?> (<?= number_format((int)$p['price'],0,',','.') ?>₫)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Giá khuyến mãi (₫)</label>
      <input type="number" class="form-control" name="promo_price" min="0" step="1" required value="<?= (int)($promotion['promo_price'] ?? 0) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Trạng thái</label>
      <select name="active" class="form-select">
        <option value="1" <?= empty($promotion) || (int)$promotion['active']? 'selected':'' ?>>Bật</option>
        <option value="0" <?= (!empty($promotion) && !(int)$promotion['active'])? 'selected':'' ?>>Tắt</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Bắt đầu</label>
      <input type="date" name="starts_at" class="form-control" required value="<?= htmlspecialchars($promotion['starts_at'] ?? date('Y-m-d')) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Kết thúc</label>
      <input type="date" name="ends_at" class="form-control" value="<?= htmlspecialchars($promotion['ends_at'] ?? '') ?>">
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_promotions">Hủy</a>
  </div>
</form>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

