<?php $title = 'Địa chỉ giao hàng'; ob_start(); ?>
<h1 class="h4 mb-3">Địa chỉ giao hàng</h1>
<div class="row g-3">
  <div class="col-lg-6">
    <div class="card p-3 shadow-sm">
      <h2 class="h6">Thêm địa chỉ</h2>
      <form method="post" action="index.php?action=account_addresses">
        <div class="mb-2"><label class="form-label">Tên người nhận</label><input name="name" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Điện thoại</label><input name="phone" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Địa chỉ</label><textarea name="address_line" class="form-control" required></textarea></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="is_default" value="1" id="def"><label for="def" class="form-check-label">Đặt làm mặc định</label></div>
        <div class="mt-2"><button class="btn btn-primary">Lưu</button></div>
      </form>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card p-3 shadow-sm">
      <h2 class="h6">Danh sách</h2>
      <div class="list-group">
        <?php foreach (($addresses ?? []) as $a): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div><strong><?= htmlspecialchars($a['name'] ?: 'Người nhận') ?></strong> <?= htmlspecialchars($a['phone'] ?: '') ?></div>
              <div class="text-muted small"><?= htmlspecialchars($a['address_line']) ?></div>
              <?php if ((int)$a['is_default']===1): ?><span class="badge bg-success">Mặc định</span><?php endif; ?>
            </div>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=account_addresses&delete=<?= (int)$a['id'] ?>" onclick="return confirm('Xóa địa chỉ này?')">Xóa</a>
          </div>
        <?php endforeach; ?>
        <?php if (empty($addresses)): ?><div class="text-muted">Chưa có địa chỉ.</div><?php endif; ?>
      </div>
    </div>
  </div>
  </div>
<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>

