<?php $title = 'Đăng ký'; ob_start(); ?>
<h1 class="h4 mb-3">Tạo tài khoản</h1>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" action="index.php?action=register_submit" class="card p-3 shadow-sm" autocomplete="off">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Tài khoản</label><input name="username" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Mật khẩu</label><input type="password" name="password" class="form-control" required></div>
    <div class="col-md-6"><label class="form-label">Xác nhận mật khẩu</label><input type="password" name="confirm" class="form-control" required></div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Đăng ký</button>
    <a class="btn btn-outline-secondary" href="index.php?action=login">Đăng nhập</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>

