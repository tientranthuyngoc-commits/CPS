<?php $title = ($item? 'Sửa' : 'Thêm') . ' banner'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_banner_save" enctype="multipart/form-data" class="card shadow-sm p-3">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Ảnh (URL)</label>
      <input type="text" class="form-control" name="image" value="<?= htmlspecialchars($item['image'] ?? '') ?>" placeholder="uploads/banners/xxx.jpg">
      <div class="form-text">Hoặc tải lên bên dưới</div>
      <input type="file" name="upload" class="form-control mt-1" accept="image/*">
    </div>
    <div class="col-md-6">
      <label class="form-label">Tiêu đề</label>
      <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>">
      <label class="form-label mt-2">Link</label>
      <input type="text" class="form-control" name="link" value="<?= htmlspecialchars($item['link'] ?? '') ?>">
      <div class="row g-2 mt-2">
        <div class="col-6">
          <label class="form-label">Sort</label>
          <input type="number" class="form-control" name="sort" value="<?= (int)($item['sort'] ?? 0) ?>">
        </div>
        <div class="col-6">
          <label class="form-label">Trạng thái</label>
          <select class="form-select" name="active">
            <option value="1" <?= (empty($item) || (int)$item['active'])?'selected':'' ?>>Bật</option>
            <option value="0" <?= (!empty($item) && !(int)$item['active'])?'selected':'' ?>>Tắt</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_banners">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

