<?php $title = 'Gán thuộc tính cho sản phẩm'; ob_start(); ?>
<h1 class="h4 mb-3">Gán thuộc tính: <?= htmlspecialchars($product['name'] ?? '') ?></h1>
<form method="post" action="index.php?action=admin_product_attrs_save" class="card shadow-sm p-3">
  <input type="hidden" name="id" value="<?= (int)($product['id'] ?? 0) ?>">
  <div class="row g-3">
    <?php foreach ($types as $t): ?>
      <div class="col-md-4">
        <h6><?= htmlspecialchars($t['name']) ?></h6>
        <div class="border rounded p-2" style="max-height:220px; overflow:auto;">
          <?php foreach ($t['attributes'] as $a): $aid=(int)$a['id']; ?>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="attrs[]" id="at<?= $aid ?>" value="<?= $aid ?>" <?= isset($cur[$aid])?'checked':'' ?>>
              <label class="form-check-label" for="at<?= $aid ?>"><?= htmlspecialchars($a['name']) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_products">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

