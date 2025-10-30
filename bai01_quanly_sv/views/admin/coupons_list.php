<?php $title = 'Mã giảm giá'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Mã giảm giá</h1>
  <a class="btn btn-primary" href="index.php?action=admin_coupon_form">Thêm coupon</a>
</div>
<div class="table-responsive card shadow-sm">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>#</th><th>Mã</th><th>Loại</th><th class="text-end">Giá trị</th><th class="text-end">Đơn tối thiểu</th><th>Hiệu lực</th><th>TT</th><th class="text-end">Hành động</th></tr></thead>
    <tbody>
      <?php foreach (($list ?? []) as $c): ?>
      <tr>
        <td><?= (int)$c['id'] ?></td>
        <td><code><?= htmlspecialchars($c['code']) ?></code></td>
        <td><?= htmlspecialchars($c['type']) ?></td>
        <td class="text-end"><?= number_format((int)$c['value'],0,',','.') ?><?= $c['type']==='percent'? '%':'₫' ?></td>
        <td class="text-end"><?= number_format((int)$c['min_order'],0,',','.') ?>₫</td>
        <td><small class="text-muted"><?= htmlspecialchars($c['valid_from'] ?: '-') ?> → <?= htmlspecialchars($c['valid_to'] ?: '-') ?></small></td>
        <td><?= (int)$c['active']? 'Bật':'Tắt' ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_coupon_form&id=<?= (int)$c['id'] ?>">Sửa</a>
          <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_coupon_delete&id=<?= (int)$c['id'] ?>" onclick="return confirm('Xóa mã này?')">Xóa</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?><tr><td colspan="8" class="text-center text-muted">Chưa có mã giảm giá</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

