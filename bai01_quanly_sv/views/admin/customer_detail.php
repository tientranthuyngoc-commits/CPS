<?php $title = 'Chi tiết khách hàng'; ob_start(); ?>
<h1 class="h4 mb-3">Khách hàng</h1>

<?php if (empty($customer)): ?>
  <div class="alert alert-warning">Không tìm thấy khách hàng.</div>
<?php else: ?>
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div><strong>Tên: </strong><?= htmlspecialchars($customer['customer_name']) ?></div>
      <div><strong>Điện thoại: </strong><?= htmlspecialchars($customer['phone']) ?></div>
      <div><strong>Địa chỉ: </strong><?= htmlspecialchars($customer['address']) ?></div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-white border-0"><strong>Lịch sử đơn hàng</strong></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th class="text-end">Tổng</th><th>Trạng thái</th><th>Ngày</th><th class="text-end">Chi tiết</th></tr></thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td><?= (int)$o['id'] ?></td>
                <td class="text-end"><?= number_format((int)$o['total'],0,',','.') ?>₫</td>
                <td><?= htmlspecialchars($o['status']) ?></td>
                <td><small class="text-muted"><?= htmlspecialchars($o['created_at']) ?></small></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_order_detail&id=<?= (int)$o['id'] ?>">Xem</a></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><tr><td colspan="5" class="text-center text-muted">Chưa có đơn</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="mt-3"><a class="btn btn-outline-secondary" href="index.php?action=admin_customers">Quay lại</a></div>
<?php endif; ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

