<?php $title = ($item? 'Sửa' : 'Thêm') . ' thương hiệu'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_brand_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Tên</label><input class="form-control" name="name" required value="<?= htmlspecialchars($item['name'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?= htmlspecialchars($item['slug'] ?? '') ?>"></div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_brands">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

