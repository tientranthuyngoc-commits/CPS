<?php $title = 'Khách hàng'; ob_start(); ?>
<h1 class="h4 mb-3">Khách hàng</h1>
<div class="row g-3 mb-3">
  <div class="col-md-8">
    <form method="get" action="index.php" class="d-flex gap-2">
      <input type="hidden" name="action" value="admin_customers">
      <input class="form-control" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Tìm theo tên/điện thoại/địa chỉ">
      <button class="btn btn-primary">Lọc</button>
      <a class="btn btn-outline-secondary" href="index.php?action=admin_customers">Xóa lọc</a>
    </form>
  </div>
  <div class="col-md-4">
    <div class="card p-2 shadow-sm">
      <div class="d-flex justify-content-between"><span>Tổng KH</span><strong><?= (int)($stats['total'] ?? 0) ?></strong></div>
      <div class="d-flex justify-content-between"><span>KH mới 30 ngày</span><strong><?= (int)($stats['new'] ?? 0) ?></strong></div>
      <div class="d-flex justify-content-between"><span>KH thường xuyên (≥3 đơn)</span><strong><?= (int)($stats['frequent'] ?? 0) ?></strong></div>
    </div>
  </div>
</div>

<div class="table-responsive card shadow-sm">
  <table class="table table-hover align-middle mb-0">
    <thead>
      <tr>
        <th>Tên</th>
        <th>Điện thoại</th>
        <th>Địa chỉ</th>
        <th class="text-end">Số đơn</th>
        <th class="text-end">Tổng chi</th>
        <th>Lần đầu</th>
        <th>Lần cuối</th>
        <th class="text-end">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['customer_name']) ?></td>
        <td><?= htmlspecialchars($c['phone']) ?></td>
        <td class="text-truncate" style="max-width:240px;">&zwj;<?= htmlspecialchars($c['address']) ?></td>
        <td class="text-end"><?= (int)$c['orders_count'] ?></td>
        <td class="text-end"><?= number_format((int)$c['total_spent'],0,',','.') ?>₫</td>
        <td><small class="text-muted"><?= htmlspecialchars($c['first_order']) ?></small></td>
        <td><small class="text-muted"><?= htmlspecialchars($c['last_order']) ?></small></td>
        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_customer_detail&phone=<?= urlencode($c['phone']) ?>">Xem</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?><tr><td colspan="8" class="text-center text-muted">Chưa có dữ liệu</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
