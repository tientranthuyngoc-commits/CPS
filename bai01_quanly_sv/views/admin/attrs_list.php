<?php $title = 'Thuộc tính'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Thuộc tính</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-primary" href="index.php?action=admin_attr_form">Thêm thuộc tính</a>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_attr_types">Loại thuộc tính</a>
  </div>
</div>

<form method="get" action="index.php" class="mb-3">
  <input type="hidden" name="action" value="admin_attrs">
  <div class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Lọc theo loại</label>
      <select class="form-select" name="type_id" onchange="this.form.submit()">
        <option value="0">-- Tất cả --</option>
        <?php foreach ($types as $t): ?>
          <option value="<?= (int)$t['id'] ?>" <?= ((int)($_GET['type_id'] ?? 0) === (int)$t['id'])? 'selected':'' ?>><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</form>

<div class="table-responsive card shadow-sm">
  <table class="table table-hover mb-0 align-middle">
    <thead><tr><th>#</th><th>Tên</th><th>Loại</th><th class="text-end">Hành động</th></tr></thead>
    <tbody>
      <?php 
        $typeMap = []; foreach ($types as $t) { $typeMap[$t['id']]=$t['name']; }
        foreach ($list as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($typeMap[$r['type_id']] ?? '') ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_attr_form&id=<?= (int)$r['id'] ?>">Sửa</a>
          <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_attr_delete&id=<?= (int)$r['id'] ?>" onclick="return confirm('Xóa thuộc tính này?')">Xóa</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?><tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

