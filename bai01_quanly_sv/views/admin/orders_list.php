<?php $title = 'Quản lý đơn hàng'; ob_start(); ?>
<style>
.table-responsive { 
  overflow-x: auto; 
  -webkit-overflow-scrolling: touch;
}
.order-card {
  border: 1px solid #dee2e6;
  border-left: 5px solid;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
  overflow: hidden;
  position: relative;
  margin-bottom: 1.5rem;
  background: #ffffff !important;
}
.order-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
}
.order-separator {
  margin: 2rem 0;
  border: none;
  border-top: 3px solid #e9ecef;
  border-radius: 0;
}
.order-card.pending { 
  border-color: #ffc107;
  background: linear-gradient(to right, rgba(255,193,7,0.03) 0%, transparent 100%);
}
.order-card.confirmed { 
  border-color: #0dcaf0;
  background: linear-gradient(to right, rgba(13,202,240,0.03) 0%, transparent 100%);
}
.order-card.shipping { 
  border-color: #0d6efd;
  background: linear-gradient(to right, rgba(13,110,253,0.03) 0%, transparent 100%);
}
.order-card.completed { 
  border-color: #198754;
  background: linear-gradient(to right, rgba(25,135,84,0.03) 0%, transparent 100%);
}
.order-card.cancelled { 
  border-color: #dc3545;
  background: linear-gradient(to right, rgba(220,53,69,0.03) 0%, transparent 100%);
}
.order-header {
  padding: 1rem;
  border-bottom: 2px solid #e9ecef;
  margin-bottom: 1.25rem;
  background: rgba(0,0,0,0.02);
  border-radius: 0.5rem 0.5rem 0 0;
}
.order-card-body {
  padding: 1.5rem;
}
.order-info {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
  padding: 0.75rem 0;
}
.order-field {
  background: #f8f9fa;
  padding: 0.5rem;
  border-radius: 0.375rem;
}
.order-field strong {
  display: block;
  font-size: 0.75rem;
  color: #6c757d;
  margin-bottom: 0.25rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.status-badge-group {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  align-items: center;
}
.badge-status {
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.875rem;
}
.action-row {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}
.form-label {
  font-size: 0.75rem;
  margin-bottom: 0.5rem;
  color: #495057;
  font-weight: 600;
}
@media (max-width: 768px) {
  .order-info {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
}
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-cart-check me-2"></i>Quản lý đơn hàng</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-success" href="index.php?action=admin_orders_export"><i class="bi bi-download me-1"></i>Xuất CSV</a>
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
  </div>
</div>

<!-- Stats Summary -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small mb-1">Tổng đơn</div>
            <div class="h4 mb-0 fw-bold"><?= count($list) ?></div>
          </div>
          <div class="fs-2 text-primary"><i class="bi bi-cart"></i></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small mb-1">Chờ xác nhận</div>
            <div class="h4 mb-0 fw-bold"><?= count(array_filter($list, fn($o) => $o['status'] === 'pending')) ?></div>
          </div>
          <div class="fs-2 text-warning"><i class="bi bi-clock-history"></i></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-success bg-opacity-10">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small mb-1">Hoàn thành</div>
            <div class="h4 mb-0 fw-bold"><?= count(array_filter($list, fn($o) => $o['status'] === 'completed')) ?></div>
          </div>
          <div class="fs-2 text-success"><i class="bi bi-check-circle"></i></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small mb-1">Chưa thanh toán</div>
            <div class="h4 mb-0 fw-bold"><?= count(array_filter($list, fn($o) => ($o['payment_status'] ?? 'unpaid') === 'unpaid')) ?></div>
          </div>
          <div class="fs-2 text-danger"><i class="bi bi-x-circle"></i></div>
        </div>
      </div>
    </div>
  </div>
</div>

<form method="get" action="index.php" class="card shadow-sm border-0 p-4 mb-4">
  <input type="hidden" name="action" value="admin_orders">
  <h6 class="fw-semibold mb-3"><i class="bi bi-funnel me-2"></i>Bộ lọc & Tìm kiếm</h6>
  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label small fw-semibold mb-1">Tìm kiếm</label>
      <input class="form-control" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Nhập tên, số điện thoại hoặc địa chỉ...">
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-semibold mb-1">Trạng thái đơn</label>
      <select class="form-select" name="status">
        <option value="" <?= (($_GET['status'] ?? '') === '') ? 'selected' : '' ?>>-- Tất cả --</option>
        <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Chờ xác nhận</option>
        <option value="confirmed" <?= (($_GET['status'] ?? '') === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
        <option value="shipping" <?= (($_GET['status'] ?? '') === 'shipping') ? 'selected' : '' ?>>Đang giao</option>
        <option value="completed" <?= (($_GET['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
        <option value="cancelled" <?= (($_GET['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-semibold mb-1">Thanh toán</label>
      <select class="form-select" name="payment_status">
        <option value="" <?= (($_GET['payment_status'] ?? '') === '') ? 'selected' : '' ?>>-- Tất cả --</option>
        <option value="unpaid" <?= (($_GET['payment_status'] ?? '') === 'unpaid') ? 'selected' : '' ?>>Chưa thanh toán</option>
        <option value="pending" <?= (($_GET['payment_status'] ?? '') === 'pending') ? 'selected' : '' ?>>Chờ thanh toán</option>
        <option value="paid" <?= (($_GET['payment_status'] ?? '') === 'paid') ? 'selected' : '' ?>>Đã thanh toán</option>
      </select>
    </div>
    <div class="col-md-2 d-grid gap-2">
      <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Tìm</button>
      <a class="btn btn-outline-secondary" href="index.php?action=admin_orders"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
    </div>
  </div>
</form>

<div class="row g-0">
  <?php foreach ($list as $o): 
    $statusClasses = [
      'pending'=>'warning',
      'confirmed'=>'info',
      'shipping'=>'primary',
      'completed'=>'success',
      'cancelled'=>'danger'
    ];
    $statusTexts = [
      'pending'=>'Chờ xác nhận',
      'confirmed'=>'Đã xác nhận',
      'shipping'=>'Đang giao',
      'completed'=>'Hoàn thành',
      'cancelled'=>'Đã hủy'
    ];
    $payStatus = $o['payment_status'] ?? 'unpaid';
    $payClasses = ['unpaid'=>'danger','pending'=>'warning','paid'=>'success'];
    $payTexts = ['unpaid'=>'Chưa','pending'=>'Chờ','paid'=>'Đã thanh toán'];
    $statusClass = $statusClasses[$o['status']] ?? 'secondary';
    $statusText = $statusTexts[$o['status']] ?? $o['status'];
    $payClass = $payClasses[$payStatus] ?? 'secondary';
    $payText = $payTexts[$payStatus] ?? $payStatus;
  ?>
  <div class="col-12 mb-4">
    <div class="card shadow-lg border-0 order-card <?= $o['status'] ?>">
      <div class="card-body order-card-body">
        <!-- Header -->
        <div class="order-header">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <h5 class="mb-1 fw-bold text-primary">Đơn hàng #<?= (int)$o['id'] ?></h5>
              <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></small>
            </div>
            <div class="status-badge-group">
              <span class="badge bg-<?= $statusClass ?> badge-status"><?= $statusText ?></span>
              <span class="badge bg-<?= $payClass ?> badge-status"><?= $payText ?></span>
            </div>
          </div>
        </div>
        
        <!-- Customer Info -->
        <div class="order-info">
          <div class="order-field">
            <strong>Khách hàng</strong>
            <div class="fw-semibold"><?= htmlspecialchars($o['customer_name']) ?></div>
          </div>
          <div class="order-field">
            <strong>Điện thoại</strong>
            <div><?= htmlspecialchars($o['phone']) ?></div>
          </div>
          <div class="order-field">
            <strong>Tổng tiền</strong>
            <div class="fw-bold text-success fs-5"><?= number_format((int)$o['total'],0,',','.') ?>₫</div>
          </div>
        </div>
        
        <!-- Address -->
        <div class="order-field mb-3">
          <strong><i class="bi bi-geo-alt me-1"></i>Địa chỉ giao hàng</strong>
          <div class="mt-1"><?= htmlspecialchars($o['address']) ?></div>
        </div>
        
        <!-- Actions -->
        <div class="action-row">
          <div class="row g-2">
            <div class="col-12">
              <form method="post" action="index.php?action=admin_order_status">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <label class="form-label">Trạng thái đơn</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                  <option value="pending"   <?= $o['status']==='pending'?'selected':'' ?>>Chờ xác nhận</option>
                  <option value="confirmed" <?= $o['status']==='confirmed'?'selected':'' ?>>Đã xác nhận</option>
                  <option value="shipping"  <?= $o['status']==='shipping'?'selected':'' ?>>Đang giao</option>
                  <option value="completed" <?= $o['status']==='completed'?'selected':'' ?>>Hoàn thành</option>
                  <option value="cancelled" <?= $o['status']==='cancelled'?'selected':'' ?>>Hủy đơn</option>
                </select>
              </form>
            </div>
            <div class="col-12">
              <form method="post" action="index.php?action=admin_order_payment">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <label class="form-label">Trạng thái thanh toán</label>
                <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                  <option value="unpaid" <?= $payStatus==='unpaid'?'selected':'' ?>>Chưa thanh toán</option>
                  <option value="pending" <?= $payStatus==='pending'?'selected':'' ?>>Chờ thanh toán</option>
                  <option value="paid" <?= $payStatus==='paid'?'selected':'' ?>>Đã thanh toán</option>
                </select>
              </form>
            </div>
            <div class="col-12">
              <div class="d-flex gap-2">
                <a class="btn btn-outline-primary flex-fill" href="index.php?action=admin_order_detail&id=<?= (int)$o['id'] ?>">
                  <i class="bi bi-eye me-1"></i>Chi tiết
                </a>
                <a class="btn btn-outline-info" href="index.php?action=admin_order_print&id=<?= (int)$o['id'] ?>" target="_blank">
                  <i class="bi bi-printer"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr class="order-separator">
  </div>
  <?php endforeach; ?>
  
  <?php if (empty($list)): ?>
    <div class="col-12">
      <div class="text-center text-muted py-5">
        <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
        <h5>Chưa có đơn hàng nào</h5>
        <p class="mb-0">Tất cả đơn hàng sẽ hiển thị tại đây</p>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
