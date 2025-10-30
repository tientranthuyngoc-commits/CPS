<?php $title = 'Thuế - Nhóm thuế sản phẩm'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 m-0">Nhóm thuế sản phẩm</h1>
  <a class="btn btn-primary" href="index.php?action=admin_tax_category_form">Thêm nhóm</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead><tr><th>ID</th><th>Mã</th><th>Tên</th><th></th></tr></thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['code']) ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><a class="btn btn-sm btn-outline-secondary" href="index.php?action=admin_tax_category_form&id=<?= (int)$r['id']?>">Sửa</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">
  <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_rates">Thuế suất</a>
  <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_mappings">Ánh xạ nhóm ↔ thuế suất</a>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

