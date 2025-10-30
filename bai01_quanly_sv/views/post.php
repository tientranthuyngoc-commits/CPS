<?php $title = $post ? $post['title'] : 'Bài viết'; ob_start(); ?>
<?php if (!$post): ?>
  <div class="alert alert-warning">Không tìm thấy bài viết.</div>
<?php else: ?>
  <article class="card shadow-sm p-3">
    <?php if (!empty($post['cover'])): ?>
      <img src="<?= htmlspecialchars($post['cover']) ?>" alt="" class="w-100 mb-3" style="max-height:360px; object-fit:cover; border-radius:8px;">
    <?php endif; ?>
    <h1 class="h4 mb-2"><?= htmlspecialchars($post['title']) ?></h1>
    <div class="text-muted mb-3"><small><?= htmlspecialchars($post['created_at']) ?></small></div>
    <div class="content"><?= nl2br(htmlspecialchars($post['content'] ?? '')) ?></div>
  </article>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

