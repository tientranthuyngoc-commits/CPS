<?php $title = 'Loại thuộc tính'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Loại thuộc tính</h1>
  <a class="btn btn-primary" href="index.php?action=admin_attr_type_form">Thêm loại</a>
</div>
<div class="table-responsive card shadow-sm">
  <table class="table table-hover mb-0 align-middle">
    <thead><tr><th>#</th><th>Tên</th><th class="text-end">Hành động</th></tr></thead>
    <tbody>
      <?php foreach ($list as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_attr_type_form&id=<?= (int)$r['id'] ?>">Sửa</a>
          <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_attr_type_delete&id=<?= (int)$r['id'] ?>" onclick="return confirm('Xóa loại này?')">Xóa</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?><tr><td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

