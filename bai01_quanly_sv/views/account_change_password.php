<?php
$title = 'Đổi mật khẩu';
ob_start();
?>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h1 class="h4 mb-3">Đổi mật khẩu</h1>
          <p class="text-muted mb-4">Cập nhật mật khẩu để bảo vệ tài khoản của bạn.</p>
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <?php if (!empty($msg)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
          <?php endif; ?>
          <form method="post" action="index.php?action=account_change_password_submit">
            <div class="mb-3">
              <label class="form-label" for="current_password">Mật khẩu hiện tại</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="new_password">Mật khẩu mới</label>
              <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
            </div>
            <div class="mb-4">
              <label class="form-label" for="confirm_password">Xác nhận mật khẩu mới</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-primary" type="submit"><i class="bi bi-shield-lock me-1"></i>Đổi mật khẩu</button>
              <a class="btn btn-outline-secondary" href="index.php?action=account"><i class="bi bi-arrow-left me-1"></i>Trở lại hồ sơ</a>
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
