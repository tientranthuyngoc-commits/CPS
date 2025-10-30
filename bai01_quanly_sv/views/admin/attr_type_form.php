<?php $title = ($item? 'Sửa' : 'Thêm') . ' loại thuộc tính'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_attr_type_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="mb-3">
    <label class="form-label">Tên loại</label>
    <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($item['name'] ?? '') ?>">
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_attr_types">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

