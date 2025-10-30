<?php $title = 'Thuế - Ánh xạ nhóm ↔ thuế suất'; ob_start(); ?>
<h1 class="h5 mb-3">Ánh xạ nhóm thuế sản phẩm ↔ thuế suất</h1>
<div class="card p-3">
  <form method="post" action="index.php?action=admin_tax_mapping_save" class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Nhóm thuế</label>
      <select name="tax_category_id" class="form-select" required>
        <option value="">-- Chọn --</option>
        <?php foreach($cats as $c): $sel = isset($map[$c['id']])? (int)$map[$c['id']] : 0; ?>
          <option value="<?= (int)$c['id'] ?>">[<?= htmlspecialchars($c['code']) ?>] <?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Thuế suất</label>
      <select name="tax_rate_id" class="form-select" required>
        <option value="">-- Chọn --</option>
        <?php foreach($rates as $r): ?>
          <option value="<?= (int)$r['id'] ?>">[<?= htmlspecialchars($r['code']) ?>] <?= htmlspecialchars($r['name']) ?> (<?= (float)$r['rate']*100 ?>% <?= htmlspecialchars($r['type']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary" type="submit">Gán</button>
      <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_rates">Quay lại</a>
    </div>
  </form>
</div>

<div class="mt-4">
  <h2 class="h6">Hiện trạng</h2>
  <table class="table table-sm">
    <thead><tr><th>Nhóm thuế</th><th>Thuế suất</th></tr></thead>
    <tbody>
      <?php foreach($cats as $c): $rid = isset($map[$c['id']])? (int)$map[$c['id']] : 0; $found = null; foreach($rates as $r){ if((int)$r['id']===$rid){ $found=$r; break; } } ?>
        <tr>
          <td>[<?= htmlspecialchars($c['code']) ?>] <?= htmlspecialchars($c['name']) ?></td>
          <td><?= $found? '['.htmlspecialchars($found['code']).'] '.htmlspecialchars($found['name']).' ('.((float)$found['rate']*100).'% '.htmlspecialchars($found['type']).')' : '<span class="text-muted">(chưa gán)</span>' ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

