<?php $title = 'Ðang nh?p'; ob_start(); ?>
<div class="row justify-content-center py-5">
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h4 text-center mb-3">Ðang nh?p</h1>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?action=login_submit" autocomplete="off">
          <div class="mb-3">
            <label class="form-label">Tï¿½i kho?n</label>
            <input type="text" name="username" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">M?t kh?u</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Ðang nh?p</button>
            <a class="btn btn-outline-secondary" href="index.php">V? trang ch?</a>
          </div>
          <div class="text-center my-3 text-muted">ho?c</div>
          <div class="d-grid gap-2">
            <a class="btn btn-light border" href="index.php?action=oauth_google_start">
              <img src="https://developers.google.com/identity/images/g-logo.png" alt="" style="width:18px; height:18px; margin-right:6px;"> Ðang nh?p b?ng Google
            </a>
            <a class="btn" style="background:#1877F2;color:#fff;border-color:#1877F2" href="index.php?action=oauth_facebook_start">
              <i class="fab fa-facebook-f me-2"></i> Ðang nh?p b?ng Facebook
            </a>
          </div>
          <div class="d-flex justify-content-between mt-3">
            <a href="index.php?action=forgot">Quï¿½n M?t kh?u?</a>
            <a href="index.php?action=register">T?o tï¿½i kho?n m?i</a>
          </div>
          <p class="mt-3 text-muted small">Tï¿½i kho?n quï¿½n tr? m?c d?nh: admin / admin123</p>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

