<?php $title = '404 - Không tìm thấy'; ob_start(); ?>
<div class="text-center py-5">
  <h1 class="display-5">404</h1>
  <p class="lead">Trang bạn tìm không tồn tại.</p>
  <a class="btn btn-primary" href="index.php">Về trang chủ</a>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

