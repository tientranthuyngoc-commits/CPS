<?php $title = ($item? 'Sửa' : 'Thêm') . ' mã giảm giá'; ob_start(); ?>
<h1 class="h4 mb-3"><?= $title ?></h1>
<form method="post" action="index.php?action=admin_coupon_save" class="card shadow-sm p-3" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
  <div class="row g-3">
    <div class="col-md-4"><label class="form-label">Mã</label><input class="form-control" name="code" required value="<?= htmlspecialchars($item['code'] ?? '') ?>" placeholder="VD: SALE10"></div>
    <div class="col-md-4"><label class="form-label">Loại</label>
      <select class="form-select" name="type">
        <option value="percent" <?= (($item['type'] ?? 'percent')==='percent')?'selected':'' ?>>Phần trăm (%)</option>
        <option value="fixed" <?= (($item['type'] ?? '')==='fixed')?'selected':'' ?>>Số tiền (₫)</option>
      </select>
    </div>
    <div class="col-md-4"><label class="form-label">Giá trị</label><input type="number" min="1" class="form-control" name="value" value="<?= (int)($item['value'] ?? 0) ?>"></div>
    <div class="col-md-4"><label class="form-label">Đơn tối thiểu (₫)</label><input type="number" min="0" class="form-control" name="min_order" value="<?= (int)($item['min_order'] ?? 0) ?>"></div>
    <div class="col-md-4"><label class="form-label">Từ ngày</label><input type="date" class="form-control" name="valid_from" value="<?= htmlspecialchars($item['valid_from'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Đến ngày</label><input type="date" class="form-control" name="valid_to" value="<?= htmlspecialchars($item['valid_to'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Trạng thái</label>
      <select name="active" class="form-select">
        <option value="1" <?= ((int)($item['active'] ?? 1)===1)?'selected':'' ?>>Bật</option>
        <option value="0" <?= ((int)($item['active'] ?? 1)===0)?'selected':'' ?>>Tắt</option>
      </select>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">Lưu</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_coupons">Hủy</a>
  </div>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

