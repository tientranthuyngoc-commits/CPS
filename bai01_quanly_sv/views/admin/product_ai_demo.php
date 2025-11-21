<?php $title = 'AI tu van san pham'; ob_start(); ?>
<?php require_once __DIR__ . '/../../includes/rbac.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-robot me-2"></i>AI tu van san pham (local)</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin_products"><i class="bi bi-arrow-left me-1"></i>Ve danh sach</a>
  </div>
</div>

<form method="post" class="card p-3 mb-4">
  <div class="mb-3">
    <label class="form-label">Cau hoi cua ban</label>
    <textarea name="question" rows="3" class="form-control" placeholder="Vi du: Can laptop van phong 15 inch gia duoi 15 trieu"><?= htmlspecialchars($_POST['question'] ?? '') ?></textarea>
  </div>
  <button class="btn btn-primary"><i class="bi bi-chat-dots me-1"></i>Hoi AI</button>
</form>

<?php if (isset($answerText)): ?>
  <div class="card p-3 mb-3">
    <h2 class="h6 fw-bold mb-2">Cau tra loi</h2>
    <pre class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars($answerText ?? '') ?></pre>
  </div>

  <?php if (!empty($answerProducts)): ?>
    <div class="card p-3">
      <h2 class="h6 fw-bold mb-2">San pham goi y</h2>
      <ul class="list-group list-group-flush">
        <?php foreach ($answerProducts as $p): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
              <small class="text-muted">
                Gia: <?= number_format($p['price'],0,',','.') ?> ₫
                <?php if (!empty($p['brand'])): ?>
                  &bull; Thuong hieu: <?= htmlspecialchars($p['brand']) ?>
                <?php endif; ?>
              </small>
            </div>
            <span class="badge bg-secondary"><?= ($p['stock'] ?? 0) > 0 ? 'Con hang' : 'Het / it hang' ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
