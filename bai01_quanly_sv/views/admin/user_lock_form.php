<?php
$title = 'Khóa tài khoản';
ob_start();
?>
<h1 class="h4 mb-3">Khóa tài khoản</h1>
<div class="card shadow-sm p-4">
  <p class="mb-3">Tài khoản: <strong><?= htmlspecialchars($user['username'] ?? '') ?></strong> (<?= htmlspecialchars($user['email'] ?? 'không có email') ?>)</p>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" action="index.php?action=admin_user_lock_save">
    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
    <div class="mb-3">
      <label class="form-label">Lý do khóa</label>
      <textarea name="reason" class="form-control" rows=4 required placeholder="Nhập lý do cụ thể để người dùng biết vì sao họ bị khóa"><?= htmlspecialchars($user['block_reason'] ?? '') ?></textarea>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-warning"><i class="bi bi-lock me-1"></i>Khóa tài khoản</button>
      <a class="btn btn-outline-secondary" href="index.php?action=admin_users">Hủy</a>
    </div>
  </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
