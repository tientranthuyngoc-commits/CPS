<?php 
$title = 'Đổi mật khẩu'; 
ob_start(); 
?>

<style>
  :root { --primary-color:#0d6efd; --success-color:#198754; --warning-color:#ffc107; --danger-color:#dc3545; --light-bg:#f8f9fa; --border-radius:8px; }
  .profile-header{background:linear-gradient(135deg,var(--primary-color),#0a58ca);color:#fff;border-radius:var(--border-radius);padding:2rem;margin-bottom:2rem}
  .nav-pills .nav-link{border-radius:var(--border-radius);margin-bottom:.5rem;font-weight:500;color:#495057;transition:.3s}
  .nav-pills .nav-link.active{background:var(--primary-color);color:#fff}
  .nav-pills .nav-link:hover:not(.active){background:#e9ecef}
  .form-card{border:none;border-radius:var(--border-radius);box-shadow:0 2px 8px rgba(0,0,0,.1);transition:box-shadow .3s}
  .form-card:hover{box-shadow:0 4px 15px rgba(0,0,0,.15)}
  .form-label{font-weight:600;color:#495057;margin-bottom:.5rem}
  .form-control{border-radius:var(--border-radius);border:1px solid #dee2e6;transition:.3s}
  .form-control:focus{border-color:var(--primary-color);box-shadow:0 0 0 .25rem rgba(13,110,253,.15)}
  .password-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#6c757d;transition:.3s}
  .password-toggle:hover{color:var(--primary-color)}
  .password-requirements{font-size:.875rem;color:#6c757d}
  .password-requirement{display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem}
  .password-requirement.met{color:var(--success-color)}
  .password-requirement i{font-size:.75rem}
</style>

<div class="py-4">
  <div class="profile-header">
    <h1 class="h3 mb-2 fw-bold">Đổi mật khẩu</h1>
    <p class="mb-0 opacity-75">Cập nhật mật khẩu mới để bảo vệ tài khoản của bạn</p>
  </div>

  <div class="row">
    <div class="col-lg-3 mb-4">
      <div class="card form-card">
        <div class="card-body">
          <div class="nav flex-column nav-pills">
            <a class="nav-link" href="index.php?action=account"><i class="fas fa-user me-2"></i>Thông tin cá nhân</a>
            <a class="nav-link" href="index.php?action=account_addresses"><i class="fas fa-location-dot me-2"></i>Địa chỉ giao hàng</a>
            <a class="nav-link" href="index.php?action=account_orders"><i class="fas fa-bag-shopping me-2"></i>Đơn hàng của tôi</a>
            <a class="nav-link active" href="index.php?action=account_change_password"><i class="fas fa-shield-halved me-2"></i>Đổi mật khẩu</a>
            <a class="nav-link" href="index.php?action=wishlist"><i class="fas fa-heart me-2"></i>Yêu thích</a>
            <div class="border-top mt-2 pt-2">
              <a class="nav-link text-danger" href="index.php?action=logout"><i class="fas fa-right-from-bracket me-2"></i>Đăng xuất</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-9">
      <?php if (!empty($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="fas fa-check-circle me-2"></i><div><?= htmlspecialchars($msg) ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i><div><?= htmlspecialchars($error) ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="card form-card">
        <div class="card-header bg-transparent border-bottom-0">
          <h3 class="h5 mb-0 fw-bold d-flex align-items-center">
            <i class="fas fa-shield-halved me-2 text-primary"></i>Thay đổi mật khẩu
          </h3>
        </div>
        <div class="card-body">
          <form method="post" action="index.php?action=account_change_password_submit" autocomplete="off" id="changePasswordForm">
            <div class="row g-3">
              <div class="col-md-12">
                <div class="position-relative">
                  <label class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                  <input type="password" name="current_password" class="form-control" required>
                  <i class="fas fa-eye password-toggle" data-target="current_password"></i>
                </div>
              </div>
              <div class="col-md-6">
                <div class="position-relative">
                  <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                  <input type="password" name="new_password" class="form-control" required minlength="6">
                  <i class="fas fa-eye password-toggle" data-target="new_password"></i>
                </div>
              </div>
              <div class="col-md-6">
                <div class="position-relative">
                  <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                  <input type="password" name="confirm_password" class="form-control" required>
                  <i class="fas fa-eye password-toggle" data-target="confirm_password"></i>
                </div>
              </div>
            </div>

            <div class="mt-3 password-requirements">
              <div class="password-requirement" data-requirement="length"><i class="fas fa-circle"></i>Tối thiểu 6 ký tự</div>
              <div class="password-requirement" data-requirement="match"><i class="fas fa-circle"></i>Mật khẩu xác nhận phải khớp</div>
              <div class="password-requirement" data-requirement="different"><i class="fas fa-circle"></i>Mật khẩu mới phải khác mật khẩu hiện tại</div>
            </div>

            <div class="mt-4">
              <button class="btn btn-primary px-4" type="submit" id="submitBtn">
                <i class="fas fa-check me-2"></i>Cập nhật mật khẩu
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password toggle visibility
  document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', function() {
      const input = document.querySelector(`input[name="${this.dataset.target}"]`);
      if (input.type === 'password') {
        input.type = 'text';
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
      }
    });
  });

  // Password validation
  const form = document.getElementById('changePasswordForm');
  const newPassword = form.querySelector('input[name="new_password"]');
  const confirmPassword = form.querySelector('input[name="confirm_password"]');
  const currentPassword = form.querySelector('input[name="current_password"]');
  const submitBtn = document.getElementById('submitBtn');

  function updateRequirement(name, met) {
    const req = document.querySelector(`[data-requirement="${name}"]`);
    if (met) {
      req.classList.add('met');
      req.querySelector('i').className = 'fas fa-check-circle';
    } else {
      req.classList.remove('met');
      req.querySelector('i').className = 'fas fa-circle';
    }
  }

  function validatePassword() {
    const lengthValid = newPassword.value.length >= 6;
    const matchValid = newPassword.value && newPassword.value === confirmPassword.value;
    const differentValid = currentPassword.value && newPassword.value && currentPassword.value !== newPassword.value;

    updateRequirement('length', lengthValid);
    updateRequirement('match', matchValid);
    updateRequirement('different', differentValid);

    return lengthValid && matchValid && differentValid;
  }

  [newPassword, confirmPassword, currentPassword].forEach(input => {
    input.addEventListener('input', validatePassword);
  });

  form.addEventListener('submit', function(e) {
    if (!validatePassword()) {
      e.preventDefault();
      alert('Vui lòng kiểm tra lại các yêu cầu về mật khẩu.');
      return;
    }
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang cập nhật...';
    submitBtn.disabled = true;
  });
});
</script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>