<?php $title = ($item? 'Sửa' : 'Thêm') . ' trang nội dung'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_page_save" class="card shadow-sm p-3">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-8">
      <label class="form-label">Tiêu đề</label>
      <input class="form-control" name="title" required value="<?= htmlspecialchars($item['title'] ?? '') ?>">
      <label class="form-label mt-2">Nội dung</label>
      <textarea name="content" class="form-control" rows="12"><?= htmlspecialchars($item['content'] ?? '') ?></textarea>
    </div>
    <div class="col-md-4">
      <label class="form-label">Slug</label>
      <input class="form-control" name="slug" value="<?= htmlspecialchars($item['slug'] ?? '') ?>" placeholder="gioi-thieu">
      <div class="form-text">Xem công khai: index.php?action=page&slug=<em>slug</em></div>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_pages">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

