<?php $title = 'Thương hiệu'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-star me-2"></i>Thương hiệu</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
    <a class="btn btn-primary" href="index.php?action=admin_brand_form"><i class="bi bi-plus-circle me-1"></i>Thêm thương hiệu</a>
  </div>
</div>
<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr><th style="width:10%">ID</th><th>Tên thương hiệu</th><th>Slug</th><th class="text-end" style="width:20%">Hành động</th></tr>
    </thead>
    <tbody>
      <?php foreach (($list ?? []) as $b): ?>
        <tr>
          <td class="fw-semibold text-muted">#<?= (int)$b['id'] ?></td>
          <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
          <td><small class="text-muted"><?= htmlspecialchars($b['slug']) ?></small></td>
          <td class="text-end">
            <div class="d-flex gap-1 justify-content-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_brand_form&id=<?= (int)$b['id'] ?>" title="Sửa"><i class="bi bi-pencil"></i></a>
              <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_brand_delete&id=<?= (int)$b['id'] ?>" title="Xóa" onclick="return confirm('Xóa thương hiệu này?')"><i class="bi bi-trash"></i></a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr>
          <td colspan="4" class="text-center text-muted py-5">
            <i class="bi bi-star fs-1 d-block mb-2"></i>
            Chưa có thương hiệu nào
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

