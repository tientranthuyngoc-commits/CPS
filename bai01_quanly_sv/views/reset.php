<?php $title = 'Đặt lại mật khẩu'; ob_start(); ?>
<h1 class="h4 mb-3">Đặt lại mật khẩu</h1>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" action="index.php?action=reset_submit" class="card p-3 shadow-sm" autocomplete="off">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? ($_POST['token'] ?? '')) ?>">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Mật khẩu mới</label><input type="password" name="password" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Xác nhận</label><input type="password" name="confirm" class="form-control" required></div>
  </div>
  <div class="mt-3"><button class="btn btn-primary" type="submit">Cập nhật</button></div>
</form>
<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>

