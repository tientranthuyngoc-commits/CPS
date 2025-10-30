<?php $title = ($item? 'Sửa' : 'Thêm') . ' bài viết'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_post_save" enctype="multipart/form-data" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-8">
      <label class="form-label">Tiêu đề</label>
      <input class="form-control" name="title" required value="<?= htmlspecialchars($item['title'] ?? '') ?>">
      <label class="form-label mt-2">Slug</label>
      <input class="form-control" name="slug" value="<?= htmlspecialchars($item['slug'] ?? '') ?>">
      <label class="form-label mt-2">Nội dung</label>
      <textarea name="content" class="form-control" rows="10"><?= htmlspecialchars($item['content'] ?? '') ?></textarea>
    </div>
    <div class="col-md-4">
      <label class="form-label">Ảnh cover (URL)</label>
      <input class="form-control" name="cover" value="<?= htmlspecialchars($item['cover'] ?? '') ?>" placeholder="uploads/posts/...">
      <label class="form-label mt-2">Hoặc tải lên</label>
      <input type="file" name="upload" class="form-control" accept="image/*">
      <?php if (!empty($item['cover'])): ?>
        <img src="<?= htmlspecialchars($item['cover']) ?>" class="mt-2 w-100" style="border-radius:8px;">
      <?php endif; ?>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_posts">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

