<?php $title = 'Thuế - Sửa/Thêm thuế suất'; ob_start(); ?>
<h1 class="h5 mb-3">Thuế suất</h1>
<form method="post" action="index.php?action=admin_tax_rate_save" class="card p-3">
  <input type="hidden" name="id" value="<?= (int)($rate['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Mã</label>
      <input class="form-control" name="code" required value="<?= htmlspecialchars($rate['code'] ?? '') ?>">
    </div>
    <div class="col-md-5">
      <label class="form-label">Tên</label>
      <input class="form-control" name="name" required value="<?= htmlspecialchars($rate['name'] ?? '') ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label">Tỷ lệ (%)</label>
      <input type="number" step="0.01" min="0" class="form-control" name="rate" value="<?= isset($rate['rate'])? (float)$rate['rate']*100 : 10 ?>">
      <small class="text-muted">Ví dụ VAT10 = 10</small>
    </div>
    <div class="col-md-2">
      <label class="form-label">Kiểu tính</label>
      <select class="form-select" name="type">
        <?php $t = strtolower($rate['type'] ?? 'exclusive'); ?>
        <option value="exclusive" <?= $t==='exclusive'?'selected':'' ?>>Exclusive</option>
        <option value="inclusive" <?= $t==='inclusive'?'selected':'' ?>>Inclusive</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Compound</label>
      <?php $cp = (int)($rate['compound'] ?? 0); ?>
      <select class="form-select" name="compound">
        <option value="0" <?= $cp? '':'selected' ?>>No</option>
        <option value="1" <?= $cp? 'selected':'' ?>>Yes</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Kích hoạt</label>
      <?php $ac = (int)($rate['active'] ?? 1); ?>
      <select class="form-select" name="active">
        <option value="1" <?= $ac? 'selected':'' ?>>Yes</option>
        <option value="0" <?= $ac? '':'selected' ?>>No</option>
      </select>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_tax_rates">Hủy</a>
  </div>
  <script>
    // Chuyển % sang số thập phân khi submit
    document.querySelector('form').addEventListener('submit', function(){
      const rateInput = this.querySelector('[name=rate]');
      const v = parseFloat(rateInput.value||'0');
      // tạm chuyển % thành phần thập phân qua input hidden
      const h = document.createElement('input'); h.type='hidden'; h.name='rate'; h.value = (v/100).toString();
      rateInput.disabled = true; this.appendChild(h);
    });
  </script>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

