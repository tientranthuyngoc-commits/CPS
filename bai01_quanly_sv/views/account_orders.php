<?php 
$title = 'Đơn hàng của tôi'; 
ob_start(); 
?>
<link rel="stylesheet" href="assets/css/account_orders.css">

<div class="py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 fw-bold">Đơn hàng của tôi</h1>
      <p class="text-muted mb-0">Theo dõi và quản lý đơn hàng của bạn</p>
    </div>
    <span class="badge bg-primary rounded-pill fs-6"><?= count($orders ?? []) ?> đơn hàng</span>
  </div>

  <div class="card p-3 mb-4">
    <div class="d-flex flex-wrap gap-2">
      <button class="btn btn-outline-primary filter-btn active" data-filter="all">Tất cả</button>
      <button class="btn btn-outline-primary filter-btn" data-filter="pending">Chờ xác nhận</button>
      <button class="btn btn-outline-primary filter-btn" data-filter="paid">Đang giao</button>
      <button class="btn btn-outline-primary filter-btn" data-filter="completed">Hoàn thành</button>
      <button class="btn btn-outline-primary filter-btn" data-filter="cancelled">Đã hủy</button>
    </div>
  </div>

  <?php if (!empty($orders)): ?>
    <div class="row g-3" id="orders-container">
      <?php 
        $statusMap = [
          'pending' => ['text'=>'Chờ xác nhận','class'=>'status-pending'],
          'paid' => ['text'=>'Đang giao','class'=>'status-paid'],
          'completed' => ['text'=>'Hoàn thành','class'=>'status-completed'],
          'cancelled' => ['text'=>'Đã hủy','class'=>'status-cancelled'],
          'return_requested' => ['text'=>'Yêu cầu đổi/trả','class'=>'status-return_requested'],
        ];
      ?>
      <?php foreach ($orders as $o): $currentStatus = $statusMap[$o['status']] ?? ['text'=>$o['status'],'class'=>'status-pending']; $itemCount = $o['item_count'] ?? 1; ?>
        <div class="col-12" data-status="<?= htmlspecialchars($o['status']) ?>">
          <div class="card order-card p-4">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="d-flex align-items-start mb-2">
                  <div class="me-3"><i class="fas fa-receipt text-primary fs-4"></i></div>
                  <div>
                    <h5 class="order-id mb-1">Đơn hàng #<?= (int)$o['id'] ?></h5>
                    <div class="d-flex align-items-center text-muted mb-2">
                      <i class="fas fa-calendar me-1"></i>
                      <small><?= htmlspecialchars($o['created_at']) ?></small>
                    </div>
                    <span class="status-badge <?= $currentStatus['class'] ?>"><?= $currentStatus['text'] ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3 text-center">
                <div class="mb-1"><small class="text-muted">Số lượng</small></div>
                <div class="fw-semibold"><?= (int)$itemCount ?> sản phẩm</div>
              </div>
              <div class="col-md-3 text-end">
                <div class="order-total mb-2"><?= number_format((int)$o['total'],0,',','.') ?>₫</div>
                <div class="d-flex gap-2 justify-content-end">
                  <a class="btn btn-sm btn-outline-primary" href="index.php?action=account_order_detail&id=<?= (int)$o['id'] ?>"><i class="fas fa-eye me-1"></i>Chi tiết</a>
                  <a class="btn btn-sm btn-outline-secondary" target="_blank" href="index.php?action=account_order_print&id=<?= (int)$o['id'] ?>"><i class="fas fa-print me-1"></i>In</a>
                </div>
              </div>
            </div>
            <div class="row mt-3 d-md-none">
              <div class="col-12"><div class="text-muted"><small><i class="fas fa-box me-1"></i><?= (int)$itemCount ?> sản phẩm</small></div></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="card"><div class="empty-state">
      <i class="fas fa-cart-shopping"></i>
      <h4 class="text-muted mb-3">Chưa có đơn hàng</h4>
      <p class="text-muted mb-4">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
      <a href="index.php?action=home" class="btn btn-primary btn-lg"><i class="fas fa-bag-shopping me-2"></i>Mua sắm ngay</a>
    </div></div>
  <?php endif; ?>

  <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="d-flex justify-content-center mt-4">
      <nav><ul class="pagination">
        <?php for($i=1;$i<=$totalPages;$i++): ?>
          <li class="page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>"><a class="page-link" href="index.php?action=account_orders&page=<?= $i ?>"><?= $i ?></a></li>
        <?php endfor; ?>
      </ul></nav>
    </div>
  <?php endif; ?>
</div>

<script src="assets/js/account_orders.js"></script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>
