<?php 
$title = 'Tai khoan cua toi';
ob_start();
?>
<link rel="stylesheet" href="assets/css/account_profile.css">
<div class="py-4">
  <div class="profile-header">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="avatar-container">
          <img src="<?= htmlspecialchars($user['avatar'] ?? ('https://ui-avatars.com/api/?name=' . urlencode($user['username'] ?? 'User') . '&background=0D8ABC&color=fff')) ?>" alt="Avatar" class="avatar">
          <div class="avatar-edit" data-bs-toggle="tooltip" title="Äá»•i áº£nh Ä‘áº¡i diá»‡n"><i class="fas fa-camera"></i></div>
        </div>
      </div>
      <div class="col">
        <h1 class="h3 mb-2 fw-bold"><?= htmlspecialchars($user['full_name'] ?? ($user['username'] ?? 'NgÆ°á»i dÃ¹ng')) ?></h1>
        <p class="mb-1 opacity-75"><i class="fas fa-id-badge me-1"></i>ThÃ nh viÃªn tá»« <?= htmlspecialchars($user['created_at'] ?? date('d/m/Y')) ?></p>
        <p class="mb-0 opacity-75"><i class="fas fa-award me-1"></i>Háº¡ng thÃ nh viÃªn: <span class="badge bg-warning text-dark">VIP</span></p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 mb-4">
      <div class="card form-card"><div class="card-body">
        <div class="nav flex-column nav-pills">
          <a class="nav-link active" href="index.php?action=account"><i class="fas fa-user me-2"></i>ThÃ´ng tin cÃ¡ nhÃ¢n</a>
          <a class="nav-link" href="index.php?action=account_addresses"><i class="fas fa-location-dot me-2"></i>Äá»‹a chá»‰ giao hÃ ng</a>
          <a class="nav-link" href="index.php?action=account_orders"><i class="fas fa-bag-shopping me-2"></i>ÄÆ¡n hÃ ng cá»§a tÃ´i</a>
          <a class="nav-link" href="index.php?action=account_change_password"><i class="fas fa-shield-halved me-2"></i>Äá»•i máº­t kháº©u</a>
          <a class="nav-link" href="index.php?action=account_wishlist"><i class="fas fa-heart me-2"></i>YÃªu thÃ­ch</a>
          <div class="border-top mt-2 pt-2">
            <a class="nav-link text-danger" href="index.php?action=logout"><i class="fas fa-right-from-bracket me-2"></i>ÄÄƒng xuáº¥t</a>
          </div>
        </div>
      </div></div>
      <div class="stats-card mt-3">
        <div class="stats-number"><?= (int)($orderCount ?? 0) ?></div>
        <div class="stats-label">ÄÆ¡n hÃ ng</div>
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
        <div class="card-header bg-transparent border-bottom-0"><h3 class="h5 mb-0 fw-bold d-flex align-items-center"><i class="fas fa-pen-to-square me-2 text-primary"></i>Chá»‰nh sá»­a thÃ´ng tin</h3></div>
        <div class="card-body">
          <form method="post" action="index.php?action=account" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Há» vÃ  tÃªn</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Nháº­p há» vÃ  tÃªn Ä‘áº§y Ä‘á»§"></div>
              <div class="col-md-6"><label class="form-label">TÃªn Ä‘Äƒng nháº­p</label><input class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled style="background:#f8f9fa"><small class="text-muted">TÃªn Ä‘Äƒng nháº­p khÃ´ng thá»ƒ thay Ä‘á»•i</small></div>
              <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="your@email.com" required></div>
              <div class="col-md-6"><label class="form-label">Sá»‘ Ä‘iá»‡n thoáº¡i</label><input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0123 456 789"></div>
              <div class="col-md-6"><label class="form-label">NgÃ y sinh</label><input type="date" name="birthday" class="form-control" value="<?= htmlspecialchars($user['birthday'] ?? '') ?>"></div>
              <div class="col-md-6"><label class="form-label">Giá»›i tÃ­nh</label><select name="gender" class="form-select"><option value="">Chá»n giá»›i tÃ­nh</option><option value="male" <?= ($user['gender'] ?? '')==='male'?'selected':'' ?>>Nam</option><option value="female" <?= ($user['gender'] ?? '')==='female'?'selected':'' ?>>Ná»¯</option><option value="other" <?= ($user['gender'] ?? '')==='other'?'selected':'' ?>>KhÃ¡c</option></select></div>
              <div class="col-12"><label class="form-label">Äá»‹a chá»‰</label><textarea name="address" class="form-control" rows="2" placeholder="Nháº­p Ä‘á»‹a chá»‰ cá»§a báº¡n"><?= htmlspecialchars($user['address'] ?? '') ?></textarea></div>
            </div>
            <div class="mt-4 d-flex gap-2 flex-wrap">
              <button class="btn btn-primary px-4" type="submit" name="update_profile"><i class="fas fa-check me-2"></i>Cáº­p nháº­t thÃ´ng tin</button>
              <a class="btn btn-outline-secondary" href="index.php?action=account_change_password"><i class="fas fa-shield-halved me-2"></i>Äá»•i máº­t kháº©u</a>
              <a class="btn btn-outline-dark" href="index.php?action=account_addresses"><i class="fas fa-location-dot me-2"></i>Quáº£n lÃ½ Ä‘á»‹a chá»‰</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card form-card mt-4">
        <div class="card-header bg-transparent border-bottom-0"><h3 class="h5 mb-0 fw-bold d-flex align-items-center"><i class="fas fa-gear me-2 text-primary"></i>TÃ¹y chá»n tÃ i khoáº£n</h3></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="newsletter" checked><label class="form-check-label" for="newsletter">Nháº­n thÃ´ng bÃ¡o khuyáº¿n mÃ£i qua email</label></div></div>
            <div class="col-md-6"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="sms_notification"><label class="form-check-label" for="sms_notification">Nháº­n thÃ´ng bÃ¡o qua SMS</label></div></div>
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
        input.onchange=function(e){ const f=e.target.files[0]; if(f){ avatarEdit.innerHTML='<div class="spinner-border spinner-border-sm" role="status"></div>'; setTimeout(()=>{ const r=new FileReader(); r.onload=function(ev){ document.querySelector('.avatar').src=ev.target.result; avatarEdit.innerHTML='<i class="fas fa-camera"></i>'; alert('Cáº­p nháº­t áº£nh Ä‘áº¡i diá»‡n thÃ nh cÃ´ng!'); }; r.readAsDataURL(f); }, 800); }};
        input.click();
      });
    }
    const form=document.querySelector('form');
    if(form){ form.addEventListener('submit', function(e){ const email=form.querySelector('input[name="email"]').value; if(!email){ e.preventDefault(); alert('Vui lÃ²ng nháº­p Ä‘á»‹a chá»‰ email!'); return; } const btn=form.querySelector('button[type="submit"]'); btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Äang cáº­p nháº­t...'; btn.disabled=true; }); }
  });
</script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>


