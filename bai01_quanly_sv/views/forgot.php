<?php 
$title = 'Quen mat khau'; 
ob_start(); 
?>
<link rel="stylesheet" href="assets/css/forgot.css">

<div class="forgot-container">
  <div class="floating-elements">
    <div class="floating-element"><i class="bi bi-key" style="font-size:3rem"></i></div>
    <div class="floating-element"><i class="bi bi-shield-lock" style="font-size:2.5rem"></i></div>
    <div class="floating-element"><i class="bi bi-envelope" style="font-size:2rem"></i></div>
  </div>
  <div class="forgot-card">
    <div class="forgot-header">
      <div class="forgot-icon"><i class="bi bi-key"></i></div>
      <h1 class="forgot-title">Quen mat khau</h1>
      <p class="forgot-subtitle">Nhap email de nhan lien ket dat lai mat khau</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><div><?= htmlspecialchars($error) ?></div></div>
    <?php endif; ?>
    <?php if (!empty($msg)): ?>
      <div class="alert alert-success d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><div><?= htmlspecialchars($msg) ?></div></div>
    <?php endif; ?>

    <form method="post" action="index.php?action=forgot_submit" autocomplete="off" id="forgotForm">
      <div class="form-group">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Nhap dia chi email cua ban" required autofocus>
        <div class="invalid-feedback" id="emailError">Vui long nhap dia chi email hop le.</div>
      </div>
      <button type="submit" class="btn-forgot" id="submitBtn"><i class="bi bi-send"></i>Gui lien ket dat lai</button>
    </form>

    <div class="security-info">
      <div class="security-title"><i class="bi bi-shield-check"></i>Thong tin bao mat</div>
      <ul class="security-list">
        <li>Lien ket dat lai mat khau se duoc gui den email cua ban.</li>
        <li>Lien ket chi hieu luc trong 24 gio.</li>
        <li>Kiem tra hop thu spam neu khong nhan duoc email.</li>
        <li>Lien he ho tro neu gap van de.</li>
      </ul>
    </div>

    <div class="forgot-footer">
      <div class="forgot-links">
        <a href="index.php?action=login" class="forgot-link"><i class="bi bi-arrow-left"></i>Quay lai dang nhap</a>
        <a href="index.php?action=register" class="forgot-link"><i class="bi bi-person-plus"></i>Tao tai khoan moi</a>
        <a href="index.php" class="forgot-link"><i class="bi bi-house-door"></i>Ve trang chu</a>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/forgot.js"></script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>
