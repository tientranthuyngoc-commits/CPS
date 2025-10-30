<?php 
$title = 'Quên mật khẩu'; 
ob_start(); 
?>

<style>
    :root {
        --primary-color: #0d6efd;
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-color: #198754;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --dark-color: #2d3748;
        --light-color: #f8fafc;
        --border-radius: 16px;
        --shadow: 0 20px 60px rgba(0,0,0,0.1);
    }
    .forgot-container{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);position:relative;overflow:hidden}
    .forgot-container::before{content:'';position:absolute;inset:0;background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="20" font-weight="bold">RESET</text></svg>');background-size:300px}
    .forgot-card{background:#fff;border-radius:var(--border-radius);padding:3rem;box-shadow:var(--shadow);position:relative;z-index:2;max-width:480px;width:100%}
    .forgot-header{text-align:center;margin-bottom:2rem}
    .forgot-icon{width:80px;height:80px;background:var(--primary-gradient);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:#fff;font-size:2rem;box-shadow:0 10px 30px rgba(102,126,234,.3)}
    .forgot-title{font-size:1.75rem;font-weight:700;color:var(--dark-color);margin-bottom:.5rem}
    .forgot-subtitle{color:#6b7280;margin-bottom:0}
    .form-group{margin-bottom:1.5rem}
    .form-label{font-weight:600;color:var(--dark-color);margin-bottom:.5rem;display:block}
    .form-control{border-radius:12px;border:2px solid #e5e7eb;padding:.75rem 1rem;font-size:1rem;transition:.3s;width:100%}
    .form-control:focus{outline:none;border-color:var(--primary-color);box-shadow:0 0 0 3px rgba(13,110,253,.1)}
    .form-control.is-invalid{border-color:var(--danger-color);box-shadow:0 0 0 3px rgba(220,53,69,.1)}
    .invalid-feedback{display:block;color:var(--danger-color);font-size:.875rem;margin-top:.5rem}
    .btn-forgot{width:100%;padding:.875rem 2rem;background:var(--primary-gradient);color:#fff;border:none;border-radius:12px;font-size:1rem;font-weight:600;transition:.3s;display:flex;align-items:center;justify-content:center;gap:.5rem}
    .btn-forgot:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(102,126,234,.3)}
    .btn-forgot:disabled{opacity:.7;cursor:not-allowed;transform:none}
    .forgot-footer{text-align:center;margin-top:2rem;padding-top:2rem;border-top:1px solid #e5e7eb}
    .forgot-links{display:flex;justify-content:center;gap:1rem;flex-wrap:wrap}
    .forgot-link{color:var(--primary-color);text-decoration:none;font-weight:500;transition:color .3s;display:flex;align-items:center;gap:.5rem}
    .forgot-link:hover{color:#0a58ca}
    .security-info{background:#f8f9fa;border-radius:12px;padding:1.5rem;margin-top:2rem}
    .security-title{font-size:.875rem;font-weight:600;color:var(--dark-color);margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem}
    .security-list{list-style:none;padding:0;margin:0}
    .security-list li{font-size:.875rem;color:#6b7280;margin-bottom:.5rem;display:flex;align-items:flex-start;gap:.5rem}
    .security-list li:before{content:'✓';color:var(--success-color);font-weight:700;flex-shrink:0}
    .floating-elements{position:absolute;inset:0;pointer-events:none;z-index:1}
    .floating-element{position:absolute;opacity:.1;animation:float-random 6s ease-in-out infinite;color:#fff}
    .floating-element:nth-child(1){top:20%;left:10%;animation-delay:0s}
    .floating-element:nth-child(2){top:60%;right:15%;animation-delay:2s}
    .floating-element:nth-child(3){bottom:30%;left:20%;animation-delay:4s}
    @keyframes float-random{0%,100%{transform:translate(0,0) rotate(0)}33%{transform:translate(30px,-20px) rotate(120deg)}66%{transform:translate(-20px,20px) rotate(240deg)}}
    .alert{border-radius:12px;border:none;padding:1rem 1.5rem;margin-bottom:1.5rem}
    .alert-danger{background:rgba(220,53,69,.1);color:var(--danger-color);border-left:4px solid var(--danger-color)}
    .alert-success{background:rgba(25,135,84,.1);color:var(--success-color);border-left:4px solid var(--success-color)}
    @media (max-width:768px){.forgot-card{padding:2rem;margin:1rem}.forgot-icon{width:60px;height:60px;font-size:1.5rem}.forgot-title{font-size:1.5rem}.forgot-links{flex-direction:column;gap:.75rem}}
    @media (max-width:480px){.forgot-card{padding:1.5rem}.forgot-container{padding:1rem}}
  </style>

<div class="forgot-container">
  <div class="floating-elements">
    <div class="floating-element"><i class="bi bi-key" style="font-size:3rem"></i></div>
    <div class="floating-element"><i class="bi bi-shield-lock" style="font-size:2.5rem"></i></div>
    <div class="floating-element"><i class="bi bi-envelope" style="font-size:2rem"></i></div>
  </div>
  <div class="forgot-card">
    <div class="forgot-header">
      <div class="forgot-icon"><i class="bi bi-key"></i></div>
      <h1 class="forgot-title">Quên mật khẩu</h1>
      <p class="forgot-subtitle">Nhập email để nhận liên kết đặt lại mật khẩu</p>
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
        <input type="email" id="email" name="email" class="form-control" placeholder="Nhập địa chỉ email của bạn" required autofocus>
        <div class="invalid-feedback" id="emailError">Vui lòng nhập địa chỉ email hợp lệ.</div>
      </div>
      <button type="submit" class="btn-forgot" id="submitBtn"><i class="bi bi-send"></i>Gửi liên kết đặt lại</button>
    </form>

    <div class="security-info">
      <div class="security-title"><i class="bi bi-shield-check"></i>Thông tin bảo mật</div>
      <ul class="security-list">
        <li>Liên kết đặt lại mật khẩu sẽ được gửi đến email của bạn</li>
        <li>Liên kết có hiệu lực trong 24 giờ</li>
        <li>Kiểm tra hộp thư spam nếu không nhận được email</li>
        <li>Liên hệ hỗ trợ nếu gặp vấn đề</li>
      </ul>
    </div>

    <div class="forgot-footer">
      <div class="forgot-links">
        <a href="index.php?action=login" class="forgot-link"><i class="bi bi-arrow-left"></i>Quay lại đăng nhập</a>
        <a href="index.php?action=register" class="forgot-link"><i class="bi bi-person-plus"></i>Tạo tài khoản mới</a>
        <a href="index.php" class="forgot-link"><i class="bi bi-house-door"></i>Về trang chủ</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const form=document.getElementById('forgotForm');
  const emailInput=document.getElementById('email');
  const emailError=document.getElementById('emailError');
  const submitBtn=document.getElementById('submitBtn');
  function validateEmail(e){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e); }
  emailInput.addEventListener('input', function(){ const v=this.value.trim(); if(v===""){ this.classList.remove('is-invalid'); emailError.textContent='Vui lòng nhập địa chỉ email.'; } else if(!validateEmail(v)){ this.classList.add('is-invalid'); emailError.textContent='Vui lòng nhập địa chỉ email hợp lệ.'; } else { this.classList.remove('is-invalid'); } });
  form.addEventListener('submit', function(e){ const v=emailInput.value.trim(); let ok=true; if(v===""){ emailInput.classList.add('is-invalid'); emailError.textContent='Vui lòng nhập địa chỉ email.'; ok=false; } else if(!validateEmail(v)){ emailInput.classList.add('is-invalid'); emailError.textContent='Vui lòng nhập địa chỉ email hợp lệ.'; ok=false; } if(!ok){ e.preventDefault(); emailInput.focus(); return; } const original=submitBtn.innerHTML; submitBtn.innerHTML='<div class="spinner-border spinner-border-sm me-2"></div> Đang gửi...'; submitBtn.disabled=true; setTimeout(()=>{ submitBtn.innerHTML=original; submitBtn.disabled=false; },2000); });
  emailInput.focus();
  emailInput.addEventListener('focus', function(){ this.parentElement.style.transform='translateY(-2px)'; });
  emailInput.addEventListener('blur', function(){ this.parentElement.style.transform='translateY(0)'; });
  document.addEventListener('keydown', function(e){ if(e.ctrlKey && e.key==='Enter'){ form.dispatchEvent(new Event('submit')); } });
  emailInput.addEventListener('paste', function(){ setTimeout(()=>{ this.dispatchEvent(new Event('input')); },0); });
});
// Optional service worker
if('serviceWorker' in navigator){ navigator.serviceWorker.register('/sw.js').catch(()=>{}); }
</script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>

