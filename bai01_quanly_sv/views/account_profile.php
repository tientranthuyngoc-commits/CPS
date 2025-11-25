<?php 
$title = 'Tai khoan cua toi';
ob_start();
$avatarPath = $user['avatar'] ?? ($_SESSION['avatar'] ?? null);
$avatarUrl = $avatarPath ? ($avatarPath . '?v=' . time()) : ('https://ui-avatars.com/api/?name=' . urlencode($user['username'] ?? 'User') . '&background=0D8ABC&color=fff');
?>
<link rel="stylesheet" href="assets/css/account_profile.css">
<div class="py-4">
  <div class="profile-header">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="avatar-container">
          <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="avatar">
          <div class="avatar-edit" data-bs-toggle="tooltip" title="Doi anh dai dien">
            <i class="fas fa-camera"></i>
          </div>
          <input type="file" id="avatarInput" accept="image/*" hidden>
        </div>
      </div>
      <div class="col">
        <h1 class="h3 mb-2 fw-bold">
          <?= htmlspecialchars($user['full_name'] ?? ($user['username'] ?? 'Nguoi dung')) ?>
        </h1>
        <p class="mb-1 opacity-75">
          <i class="fas fa-id-badge me-1"></i>
          Thanh vien tu <?= htmlspecialchars($user['created_at'] ?? date('d/m/Y')) ?>
        </p>
        <p class="mb-0 opacity-75">
          <i class="fas fa-award me-1"></i>
          Hang thanh vien: <span class="badge bg-warning text-dark">VIP</span>
        </p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 mb-4">
      <div class="card form-card">
        <div class="card-body">
          <div class="nav flex-column nav-pills">
            <a class="nav-link active" href="index.php?action=account">
              <i class="fas fa-user me-2"></i>Thong tin ca nhan
            </a>
            <a class="nav-link" href="index.php?action=account_addresses">
              <i class="fas fa-location-dot me-2"></i>Dia chi giao hang
            </a>
            <a class="nav-link" href="index.php?action=account_orders">
              <i class="fas fa-bag-shopping me-2"></i>Don hang cua toi
            </a>
            <a class="nav-link" href="index.php?action=account_change_password">
              <i class="fas fa-shield-halved me-2"></i>Doi mat khau
            </a>
            <a class="nav-link" href="index.php?action=wishlist">
              <i class="fas fa-heart me-2"></i>Yeu thich
            </a>
            <div class="border-top mt-2 pt-2">
              <a class="nav-link text-danger" href="index.php?action=logout">
                <i class="fas fa-right-from-bracket me-2"></i>Dang xuat
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="stats-card mt-3">
        <div class="stats-number"><?= (int)($orderCount ?? 0) ?></div>
        <div class="stats-label">Don hang</div>
      </div>
    </div>

    <div class="col-lg-9">
      <?php if (!empty($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          <div><?= htmlspecialchars($msg) ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <div><?= htmlspecialchars($error) ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="card form-card">
        <div class="card-header bg-transparent border-bottom-0">
          <h3 class="h5 mb-0 fw-bold d-flex align-items-center">
            <i class="fas fa-pen-to-square me-2 text-primary"></i>
            Chinh sua thong tin
          </h3>
        </div>
        <div class="card-body">
          <form method="post" action="index.php?action=account" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Ho va ten</label>
                <input
                  type="text"
                  name="full_name"
                  class="form-control"
                  value="<?= htmlspecialchars($user['full_name'] ?? '') ?>"
                  placeholder="Nhap ho va ten day du"
                >
              </div>
              <div class="col-md-6">
                <label class="form-label">Ten dang nhap</label>
                <input
                  class="form-control"
                  value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                  disabled
                  style="background:#f8f9fa"
                >
                <small class="text-muted">Ten dang nhap khong the thay doi</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">
                  Email <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  name="email"
                  class="form-control"
                  value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                  placeholder="your@email.com"
                  required
                >
              </div>
              <div class="col-md-6">
                <label class="form-label">So dien thoai</label>
                <input
                  type="tel"
                  name="phone"
                  class="form-control"
                  value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                  placeholder="0123 456 789"
                >
              </div>
              <div class="col-md-6">
                <label class="form-label">Ngay sinh</label>
                <input
                  type="date"
                  name="birthday"
                  class="form-control"
                  value="<?= htmlspecialchars($user['birthday'] ?? '') ?>"
                >
              </div>
              <div class="col-md-6">
                <label class="form-label">Gioi tinh</label>
                <select name="gender" class="form-select">
                  <option value="">Chon gioi tinh</option>
                  <option value="male"   <?= ($user['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Nam</option>
                  <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nu</option>
                  <option value="other"  <?= ($user['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>Khac</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Dia chi</label>
                <textarea
                  name="address"
                  class="form-control"
                  rows="2"
                  placeholder="Nhap dia chi cua ban"
                ><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="mt-4 d-flex gap-2 flex-wrap">
              <button class="btn btn-primary px-4" type="submit" name="update_profile">
                <i class="fas fa-check me-2"></i>Cap nhat thong tin
              </button>
              <a class="btn btn-outline-secondary" href="index.php?action=account_change_password">
                <i class="fas fa-shield-halved me-2"></i>Doi mat khau
              </a>
              <a class="btn btn-outline-dark" href="index.php?action=account_addresses">
                <i class="fas fa-location-dot me-2"></i>Quan ly dia chi
              </a>
            </div>
          </form>
        </div>
      </div>

      <div class="card form-card mt-4">
        <div class="card-header bg-transparent border-bottom-0">
          <h3 class="h5 mb-0 fw-bold d-flex align-items-center">
            <i class="fas fa-gear me-2 text-primary"></i>
            Tuy chon tai khoan
          </h3>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="newsletter" checked>
                <label class="form-check-label" for="newsletter">
                  Nhan thong bao khuyen mai qua email
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="sms_notification">
                <label class="form-check-label" for="sms_notification">
                  Nhan thong bao qua SMS
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/account_profile.js"></script>

<?php 
$content = ob_get_clean(); 
require __DIR__.'/layout.php'; 
?>
