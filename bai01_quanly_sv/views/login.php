<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$title = 'Dang nhap';
if (empty($error) && !empty($_GET['blocked'])) {
  $reason = trim((string)($_GET['reason'] ?? '')) ?: 'Tai khoan da bi khoa.';
  $error = 'Tai khoan cua ban da bi khoa. Ly do: ' . $reason;
}
if (empty($error) && !empty($_SESSION['locked_message'])) {
  $error = $_SESSION['locked_message'];
  unset($_SESSION['locked_message']);
}
ob_start();
?>
<div class="row justify-content-center py-5">
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 text-center mb-3">Dang nhap</h1>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?action=login_submit" autocomplete="off">
          <div class="mb-3">
            <label class="form-label">Tai khoan</label>
            <input type="text" name="username" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Mat khau</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Dang nhap</button>
            <a class="btn btn-outline-secondary" href="index.php">Ve trang chu</a>
          </div>
          <div class="text-center my-3 text-muted">hoac</div>
          <div class="d-grid gap-2">
            <a class="btn btn-light border" href="index.php?action=oauth_google_start">
              <img src="https://developers.google.com/identity/images/g-logo.png" alt="" style="width:18px; height:18px; margin-right:6px;"> Dang nhap bang Google
            </a>
            <a class="btn" style="background:#1877F2;color:#fff;border-color:#1877F2" href="index.php?action=oauth_facebook_start">
              <i class="fab fa-facebook-f me-2"></i> Dang nhap bang Facebook
            </a>
          </div>
          <div class="d-flex justify-content-between mt-3">
            <a href="index.php?action=forgot">Quen mat khau?</a>
            <a href="index.php?action=register">Tao tai khoan moi</a>
          </div>
          <p class="mt-3 text-muted small">Tai khoan quan tri mac dinh: admin / admin123</p>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
