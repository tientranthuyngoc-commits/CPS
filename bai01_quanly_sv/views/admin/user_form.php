<?php $title = ($item? 'Sửa' : 'Thêm') . ' tài khoản'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_user_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-4"><label class="form-label">Tài khoản</label><input class="form-control" name="username" required value="<?= htmlspecialchars($item['username'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Điện thoại</label><input class="form-control" name="phone" value="<?= htmlspecialchars($item['phone'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Mật khẩu <?= !empty($item)? '(để trống nếu giữ nguyên)':'' ?></label><input class="form-control" type="password" name="password"></div>
    <div class="col-md-4"><label class="form-label">Quyền</label>
      <select name="role" class="form-select">
        <option value="user" <?= (($item['role'] ?? 'user')==='user')?'selected':'' ?>>user</option>
        <option value="admin" <?= (($item['role'] ?? 'user')==='admin')?'selected':'' ?>>admin</option>
      </select>
    </div>
    <div class="col-md-4"><label class="form-label">Trạng thái</label>
      <select name="is_active" class="form-select">
        <option value="1" <?= ((int)($item['is_active'] ?? 1)===1)?'selected':'' ?>>Mở</option>
        <option value="0" <?= ((int)($item['is_active'] ?? 1)===0)?'selected':'' ?>>Khóa</option>
      </select>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_users">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

