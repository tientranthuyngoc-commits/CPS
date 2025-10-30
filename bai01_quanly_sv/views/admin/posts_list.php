<?php $title = 'Bài viết / Tin tức'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-file-text me-2"></i>Bài viết / Tin tức</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
    <a class="btn btn-primary" href="index.php?action=admin_post_form"><i class="bi bi-plus-circle me-1"></i>Thêm bài viết</a>
  </div>
</div>
<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:5%">ID</th>
        <th>Tiêu đề</th>
        <th style="width:20%">Slug</th>
        <th style="width:12%">Ngày</th>
        <th style="width:15%" class="text-end">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (($list ?? []) as $p): ?>
        <tr>
          <td class="fw-semibold text-muted">#<?= (int)$p['id'] ?></td>
          <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
          <td><small class="text-muted"><?= htmlspecialchars($p['slug']) ?></small></td>
          <td><small class="text-muted"><?= date('d/m/Y', strtotime($p['created_at'])) ?></small></td>
          <td class="text-end">
            <div class="d-flex gap-1 justify-content-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_post_form&id=<?= (int)$p['id'] ?>" title="Sửa"><i class="bi bi-pencil"></i></a>
              <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_post_delete&id=<?= (int)$p['id'] ?>" title="Xóa" onclick="return confirm('Xóa bài viết này?')"><i class="bi bi-trash"></i></a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr>
          <td colspan="5" class="text-center text-muted py-5">
            <i class="bi bi-file-text fs-1 d-block mb-2"></i>
            Chưa có bài viết nào
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

