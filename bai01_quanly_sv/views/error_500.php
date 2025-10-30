<?php $title = 'Lỗi hệ thống'; ob_start(); ?>
<div class="text-center py-5">
  <h1 class="display-6">Có lỗi xảy ra</h1>
  <p class="lead">Vui lòng thử lại sau. Nếu lỗi tiếp diễn, liên hệ quản trị.</p>
  <a class="btn btn-primary" href="index.php">Về trang chủ</a>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

