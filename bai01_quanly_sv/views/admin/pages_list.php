<?php $title = 'Trang nội dung (Pages)'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Trang nội dung</h1>
  <a class="btn btn-primary" href="index.php?action=admin_page_form">Thêm trang</a>
  </div>
<div class="table-responsive card shadow-sm">
  <table class="table table-hover mb-0 align-middle">
    <thead><tr><th>#</th><th>Tiêu đề</th><th>Slug</th><th>Ngày</th><th class="text-end">Hành động</th></tr></thead>
    <tbody>
      <?php foreach (($list ?? []) as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td><?= htmlspecialchars($p['slug']) ?></td>
          <td><small class="text-muted"><?= htmlspecialchars($p['created_at'] ?? '') ?></small></td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_page_form&id=<?= (int)$p['id'] ?>">Sửa</a>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_page_delete&id=<?= (int)$p['id'] ?>" onclick="return confirm('Xóa trang này?')">Xóa</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?><tr><td colspan="5" class="text-center text-muted">Chưa có trang</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

