<?php $title = 'Thuế - Sửa/Thêm nhóm thuế'; ob_start(); ?>
<h1 class="h5 mb-3">Nhóm thuế sản phẩm</h1>
<form method="post" action="index.php?action=admin_tax_category_save" class="card p-3">
  <input type="hidden" name="id" value="<?= (int)($row['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Mã</label>
      <input class="form-control" name="code" required value="<?= htmlspecialchars($row['code'] ?? '') ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Tên</label>
      <input class="form-control" name="name" required value="<?= htmlspecialchars($row['name'] ?? '') ?>">
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_categories">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

