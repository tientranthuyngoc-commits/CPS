<?php $title = 'Thuế - Thuế suất'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h5 m-0">Thuế suất</h1>
  <a class="btn btn-primary" href="index.php?action=admin_tax_rate_form">Thêm thuế suất</a>
  </div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead><tr><th>ID</th><th>Mã</th><th>Tên</th><th>Tỷ lệ</th><th>Kiểu</th><th>Compound</th><th>Active</th><th></th></tr></thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['code']) ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= (float)$r['rate']*100 ?>%</td>
          <td><?= htmlspecialchars($r['type']) ?></td>
          <td><?= ((int)$r['compound']? 'Yes':'No') ?></td>
          <td><?= ((int)$r['active']? 'Yes':'No') ?></td>
          <td><a class="btn btn-sm btn-outline-secondary" href="index.php?action=admin_tax_rate_form&id=<?= (int)$r['id']?>">Sửa</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">
  <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_categories">Nhóm thuế</a>
  <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_mappings">Ánh xạ nhóm ↔ thuế suất</a>
  <a class="btn btn-outline-secondary" href="index.php?action=admin_report_tax">Xuất CSV tax_journal</a>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

