<?php $title = htmlspecialchars(($page['title'] ?? 'Trang')); ob_start(); ?>
<div class="container my-4">
  <h1 class="h3 mb-3"><?= htmlspecialchars($page['title'] ?? 'Trang') ?></h1>
  <article class="card shadow-sm p-3">
    <div><?= nl2br(htmlspecialchars($page['content'] ?? 'Nội dung đang cập nhật...')) ?></div>
  </article>
  <div class="mt-3">
    <a class="btn btn-outline-secondary" href="index.php">Về trang chủ</a>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

