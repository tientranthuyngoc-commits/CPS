<?php
$title = 'Báo cáo sản phẩm';
ob_start();
$productId = (int)($_GET['id'] ?? 0);
$error = $_GET['error'] ?? '';
?>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h5 mb-3">Báo cáo sản phẩm #<?= $productId ?></h1>
          <?php if ($error === 'missing_fields'): ?>
            <div class="alert alert-danger">Vui lòng nhập đầy đủ tiêu đề và nội dung.</div>
          <?php elseif ($error === 'save_failed'): ?>
            <div class="alert alert-danger">Không lưu được báo cáo, thử lại sau.</div>
          <?php endif; ?>
          <form method="post" action="index.php?action=submit_report">
            <input type="hidden" name="product_id" value="<?= $productId ?>">
            <div class="mb-3">
              <label class="form-label">Tiêu đề</label>
              <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nội dung</label>
              <textarea class="form-control" name="content" rows="4" required></textarea>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-danger" type="submit"><i class="bi bi-flag me-1"></i>Gửi báo cáo</button>
              <a class="btn btn-outline-secondary" href="index.php?action=product&id=<?= $productId ?>">Hủy</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
