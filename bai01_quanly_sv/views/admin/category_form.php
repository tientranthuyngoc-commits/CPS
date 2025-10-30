<?php $title = ($category? 'Sửa' : 'Thêm') . ' danh mục'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>

<form method="post" action="index.php?action=admin_category_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($category['id'] ?? 0) ?>">
  <div class="mb-3">
    <label class="form-label">Tên danh mục</label>
    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($category['name'] ?? '') ?>">
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_categories">Hủy</a>
  </div>
</form>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

