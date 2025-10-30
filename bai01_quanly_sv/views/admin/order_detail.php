<?php 
$title = 'Chi tiết đơn hàng'; 
// Ensure variables are initialized
$order = $order ?? null;
$items = $items ?? [];
ob_start(); 
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Chi tiết đơn hàng #<?= (int)($order['id'] ?? 0) ?></h1>
  <a class="btn btn-outline-secondary" href="index.php?action=admin_orders"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<?php if (!$order): ?>
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>Không tìm thấy đơn hàng.
  </div>
<?php else: ?>
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary bg-opacity-10 border-0">
          <h5 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Thông tin khách hàng</h5>
        </div>
        <div class="card-body">
          <div class="mb-2">
            <strong class="text-muted">Tên khách hàng:</strong><br>
            <span><?= htmlspecialchars($order['customer_name'] ?? '') ?></span>
          </div>
          <div class="mb-2">
            <strong class="text-muted">Điện thoại:</strong><br>
            <span><?= htmlspecialchars($order['phone'] ?? '') ?></span>
          </div>
          <div>
            <strong class="text-muted">Địa chỉ:</strong><br>
            <span><?= htmlspecialchars($order['address'] ?? '') ?></span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-success bg-opacity-10 border-0">
          <h5 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>Thông tin đơn hàng</h5>
        </div>
        <div class="card-body">
          <div class="mb-2">
            <strong class="text-muted">Ngày tạo:</strong><br>
            <span><?= !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : '-' ?></span>
          </div>
          <div class="mb-2">
            <strong class="text-muted">Trạng thái:</strong><br>
            <?php
            $statusClasses = ['pending'=>'warning','confirmed'=>'info','shipping'=>'primary','completed'=>'success','cancelled'=>'danger'];
            $statusTexts = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','shipping'=>'Đang giao','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
            $sc = $statusClasses[$order['status']] ?? 'secondary';
            $st = $statusTexts[$order['status']] ?? $order['status'];
            ?>
            <form method="post" action="index.php?action=admin_order_status" class="d-inline">
              <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
              <select name="status" class="form-select form-select-sm d-inline-block" style="width:auto;" onchange="this.form.submit()">
                <option value="pending"   <?= $order['status']==='pending'?'selected':'' ?>>Chờ xác nhận</option>
                <option value="confirmed" <?= $order['status']==='confirmed'?'selected':'' ?>>Đã xác nhận</option>
                <option value="shipping"  <?= $order['status']==='shipping'?'selected':'' ?>>Đang giao</option>
                <option value="completed" <?= $order['status']==='completed'?'selected':'' ?>>Hoàn thành</option>
                <option value="cancelled" <?= $order['status']==='cancelled'?'selected':'' ?>>Hủy</option>
              </select>
            </form>
          </div>
          <div class="mb-2">
            <strong class="text-muted">Thanh toán:</strong><br>
            <?php
            $payStatus = $order['payment_status'] ?? 'unpaid';
            $payClasses = ['unpaid'=>'danger','pending'=>'warning','paid'=>'success'];
            $payTexts = ['unpaid'=>'Chưa','pending'=>'Chờ','paid'=>'Đã'];
            $paysc = $payClasses[$payStatus] ?? 'secondary';
            $payst = $payTexts[$payStatus] ?? $payStatus;
            ?>
            <form method="post" action="index.php?action=admin_order_payment" class="d-inline">
              <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
              <select name="payment_status" class="form-select form-select-sm d-inline-block" style="width:auto;" onchange="this.form.submit()">
                <option value="unpaid" <?= $payStatus==='unpaid'?'selected':'' ?>>Chưa thanh toán</option>
                <option value="pending" <?= $payStatus==='pending'?'selected':'' ?>>Chờ thanh toán</option>
                <option value="paid" <?= $payStatus==='paid'?'selected':'' ?>>Đã thanh toán</option>
              </select>
            </form>
          </div>
          <div>
            <strong class="text-muted">Tổng tiền:</strong><br>
            <h4 class="mb-0 text-success fw-bold"><?= number_format((int)($order['total'] ?? 0),0,',','.') ?>₫</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 border-bottom">
      <h5 class="mb-0 fw-semibold"><i class="bi bi-box-seam me-2"></i>Danh sách sản phẩm</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:8%">ID</th>
              <th>Tên sản phẩm</th>
              <th class="text-center" style="width:10%">SL</th>
              <th class="text-end" style="width:15%">Đơn giá</th>
              <th class="text-end" style="width:15%">Thành tiền</th>
            </tr>
          </thead>
          <tbody>
            <?php $sum = 0; ?>
            <?php if (!empty($items)): ?>
              <?php foreach ($items as $it): $line = (int)$it['quantity']*(int)$it['price']; $sum+=$line; ?>
                <tr>
                  <td class="text-muted">#<?= (int)$it['product_id'] ?></td>
                  <td><?= htmlspecialchars($it['name'] ?? '') ?></td>
                  <td class="text-center"><span class="badge bg-secondary"><?= (int)$it['quantity'] ?></span></td>
                  <td class="text-end"><?= number_format((int)$it['price'],0,',','.') ?>₫</td>
                  <td class="text-end fw-bold text-success"><?= number_format($line,0,',','.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  <i class="bi bi-box-x fs-1 d-block mb-2"></i>
                  Không có sản phẩm
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
          <tfoot class="table-light">
            <tr>
              <td colspan="4" class="text-end fw-semibold">Tổng cộng:</td>
              <td class="text-end">
                <h5 class="mb-0 text-success fw-bold"><?= number_format($sum,0,',','.') ?>₫</h5>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <?php if (!empty($order['tax_total'])): ?>
  <div class="card shadow-sm border-0 bg-light">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><strong>Thuế (tổng cộng):</strong></h6>
        <h5 class="mb-0 text-primary"><?= number_format((int)$order['tax_total'],0,',','.') ?>đ</h5>
      </div>
    </div>
  </div>
  <?php endif; ?>
<?php endif; ?>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
