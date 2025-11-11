<?php
$title = "Báo cáo sản phẩm";
ob_start();

// Lấy id sản phẩm từ GET (nếu không có thì mặc định = 0)
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<h2>Báo cáo vi phạm đối với sản phẩm</h2>

<form method="post" action="index.php?action=submit_report" style="max-width:600px">
  <input type="hidden" name="product_id" value="<?= htmlspecialchars($productId) ?>">

  <label>Tiêu đề báo cáo *</label>
  <input name="title" required class="form-control" placeholder="Ví dụ: Hàng giả / Giá sai / Thông tin sai">

  <label>Nội dung *</label>
  <textarea name="content" required class="form-control" rows="5" placeholder="Hãy mô tả vấn đề bạn gặp phải..."></textarea>

  <button class="btn primary" style="margin-top:10px">Gửi báo cáo</button>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
