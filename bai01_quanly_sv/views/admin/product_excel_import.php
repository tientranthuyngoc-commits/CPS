<?php $title = 'Import Excel san pham'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Import / AI Index san pham</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin_products"><i class="bi bi-arrow-left me-1"></i>Ve danh sach</a>
  </div>
</div>

<?php foreach (($messages ?? []) as $m): ?>
  <div class="alert alert-success"><?= htmlspecialchars($m) ?></div>
<?php endforeach; ?>
<?php foreach (($errors ?? []) as $e): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="card-title mb-3 fw-bold">Upload file Excel</h5>
    <p class="text-muted mb-3">
      File mau: cot A = SKU, B = Ten san pham, C = Gia, D = Ton kho, E = Mo ta.
      He thong se goi AI de tao summary + keywords cho moi dong.
    </p>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Chon file Excel (.xlsx, .xls)</label>
        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
      </div>
      <button class="btn btn-primary">
        <i class="bi bi-upload me-1"></i>Import & AI Index
      </button>
    </form>
  </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
