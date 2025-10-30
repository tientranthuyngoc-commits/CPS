<?php $title = ($item? 'Sửa' : 'Thêm') . ' thuộc tính'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_attr_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Loại</label>
      <select name="type_id" class="form-select" required>
        <?php foreach ($types as $t): ?>
          <option value="<?= (int)$t['id'] ?>" <?= (!empty($item) && (int)$item['type_id']===(int)$t['id'])? 'selected':'' ?>><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Tên</label>
      <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($item['name'] ?? '') ?>">
    </div>
  </div>
  <div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_attrs">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

