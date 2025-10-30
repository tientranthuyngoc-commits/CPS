<?php 
$title = 'Tài khoản của tôi'; 
ob_start(); 
?>

<style>
  :root { --primary-color:#0d6efd; --success-color:#198754; --warning-color:#ffc107; --danger-color:#dc3545; --light-bg:#f8f9fa; --border-radius:8px; }
  .profile-header{background:linear-gradient(135deg,var(--primary-color),#0a58ca);color:#fff;border-radius:var(--border-radius);padding:2rem;margin-bottom:2rem}
  .avatar-container{position:relative;display:inline-block}
  .avatar{width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.15)}
  .avatar-edit{position:absolute;bottom:5px;right:5px;width:32px;height:32px;border-radius:50%;background:var(--primary-color);color:#fff;display:flex;align-items:center;justify-content:center;border:2px solid #fff;cursor:pointer}
  .nav-pills .nav-link{border-radius:var(--border-radius);margin-bottom:.5rem;font-weight:500;color:#495057;transition:.3s}
  .nav-pills .nav-link.active{background:var(--primary-color);color:#fff}
  .nav-pills .nav-link:hover:not(.active){background:#e9ecef}
  .form-card{border:none;border-radius:var(--border-radius);box-shadow:0 2px 8px rgba(0,0,0,.1);transition:box-shadow .3s}
  .form-card:hover{box-shadow:0 4px 15px rgba(0,0,0,.15)}
  .form-label{font-weight:600;color:#495057;margin-bottom:.5rem}
  .form-control{border-radius:var(--border-radius);border:1px solid #dee2e6;transition:.3s}
  .form-control:focus{border-color:var(--primary-color);box-shadow:0 0 0 .25rem rgba(13,110,253,.15)}
  .stats-card{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;border-radius:var(--border-radius);padding:1.5rem;text-align:center}
  .stats-number{font-size:2rem;font-weight:700;margin-bottom:.25rem}
  .stats-label{font-size:.875rem;opacity:.9}
  .opacity-75{opacity:.75}
  .fw-bold{font-weight:700}
</style>

<div class="py-4">
  <div class="profile-header">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="avatar-container">
          <img src="<?= htmlspecialchars($user['avatar'] ?? ('https://ui-avatars.com/api/?name=' . urlencode($user['username'] ?? 'User') . '&background=0D8ABC&color=fff')) ?>" alt="Avatar" class="avatar">
          <div class="avatar-edit" data-bs-toggle="tooltip" title="Đổi ảnh đại diện"><i class="fas fa-camera"></i></div>
        </div>
      </div>
      <div class="col">
        <h1 class="h3 mb-2 fw-bold"><?= htmlspecialchars($user['full_name'] ?? ($user['username'] ?? 'Người dùng')) ?></h1>
        <p class="mb-1 opacity-75"><i class="fas fa-id-badge me-1"></i>Thành viên từ <?= htmlspecialchars($user['created_at'] ?? date('d/m/Y')) ?></p>
        <p class="mb-0 opacity-75"><i class="fas fa-award me-1"></i>Hạng thành viên: <span class="badge bg-warning text-dark">VIP</span></p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 mb-4">
      <div class="card form-card"><div class="card-body">
        <div class="nav flex-column nav-pills">
          <a class="nav-link active" href="index.php?action=account"><i class="fas fa-user me-2"></i>Thông tin cá nhân</a>
          <a class="nav-link" href="index.php?action=account_addresses"><i class="fas fa-location-dot me-2"></i>Địa chỉ giao hàng</a>
          <a class="nav-link" href="index.php?action=account_orders"><i class="fas fa-bag-shopping me-2"></i>Đơn hàng của tôi</a>
          <a class="nav-link" href="index.php?action=account_change_password"><i class="fas fa-shield-halved me-2"></i>Đổi mật khẩu</a>
          <a class="nav-link" href="index.php?action=account_wishlist"><i class="fas fa-heart me-2"></i>Yêu thích</a>
          <div class="border-top mt-2 pt-2">
            <a class="nav-link text-danger" href="index.php?action=logout"><i class="fas fa-right-from-bracket me-2"></i>Đăng xuất</a>
          </div>
        </div>
      </div></div>
      <div class="stats-card mt-3">
        <div class="stats-number"><?= (int)($orderCount ?? 0) ?></div>
        <div class="stats-label">Đơn hàng</div>
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
        <div class="card-header bg-transparent border-bottom-0"><h3 class="h5 mb-0 fw-bold d-flex align-items-center"><i class="fas fa-pen-to-square me-2 text-primary"></i>Chỉnh sửa thông tin</h3></div>
        <div class="card-body">
          <form method="post" action="index.php?action=account" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Họ và tên</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Nhập họ và tên đầy đủ"></div>
              <div class="col-md-6"><label class="form-label">Tên đăng nhập</label><input class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled style="background:#f8f9fa"><small class="text-muted">Tên đăng nhập không thể thay đổi</small></div>
              <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="your@email.com" required></div>
              <div class="col-md-6"><label class="form-label">Số điện thoại</label><input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0123 456 789"></div>
              <div class="col-md-6"><label class="form-label">Ngày sinh</label><input type="date" name="birthday" class="form-control" value="<?= htmlspecialchars($user['birthday'] ?? '') ?>"></div>
              <div class="col-md-6"><label class="form-label">Giới tính</label><select name="gender" class="form-select"><option value="">Chọn giới tính</option><option value="male" <?= ($user['gender'] ?? '')==='male'?'selected':'' ?>>Nam</option><option value="female" <?= ($user['gender'] ?? '')==='female'?'selected':'' ?>>Nữ</option><option value="other" <?= ($user['gender'] ?? '')==='other'?'selected':'' ?>>Khác</option></select></div>
              <div class="col-12"><label class="form-label">Địa chỉ</label><textarea name="address" class="form-control" rows="2" placeholder="Nhập địa chỉ của bạn"><?= htmlspecialchars($user['address'] ?? '') ?></textarea></div>
            </div>
            <div class="mt-4 d-flex gap-2 flex-wrap">
              <button class="btn btn-primary px-4" type="submit" name="update_profile"><i class="fas fa-check me-2"></i>Cập nhật thông tin</button>
              <a class="btn btn-outline-secondary" href="index.php?action=account_change_password"><i class="fas fa-shield-halved me-2"></i>Đổi mật khẩu</a>
              <a class="btn btn-outline-dark" href="index.php?action=account_addresses"><i class="fas fa-location-dot me-2"></i>Quản lý địa chỉ</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card form-card mt-4">
        <div class="card-header bg-transparent border-bottom-0"><h3 class="h5 mb-0 fw-bold d-flex align-items-center"><i class="fas fa-gear me-2 text-primary"></i>Tùy chọn tài khoản</h3></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="newsletter" checked><label class="form-check-label" for="newsletter">Nhận thông báo khuyến mãi qua email</label></div></div>
            <div class="col-md-6"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="sms_notification"><label class="form-check-label" for="sms_notification">Nhận thông báo qua SMS</label></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el){return new bootstrap.Tooltip(el)});
    const avatarEdit=document.querySelector('.avatar-edit');
    if(avatarEdit){
      avatarEdit.addEventListener('click', function(){
        const input=document.createElement('input'); input.type='file'; input.accept='image/*';
        input.onchange=function(e){ const f=e.target.files[0]; if(f){ avatarEdit.innerHTML='<div class="spinner-border spinner-border-sm" role="status"></div>'; setTimeout(()=>{ const r=new FileReader(); r.onload=function(ev){ document.querySelector('.avatar').src=ev.target.result; avatarEdit.innerHTML='<i class="fas fa-camera"></i>'; alert('Cập nhật ảnh đại diện thành công!'); }; r.readAsDataURL(f); }, 800); }};
        input.click();
      });
    }
    const form=document.querySelector('form');
    if(form){ form.addEventListener('submit', function(e){ const email=form.querySelector('input[name="email"]').value; if(!email){ e.preventDefault(); alert('Vui lòng nhập địa chỉ email!'); return; } const btn=form.querySelector('button[type="submit"]'); btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Đang cập nhật...'; btn.disabled=true; }); }
  });
</script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>

