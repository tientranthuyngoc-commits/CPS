<?php 
$title = 'Chi tiết đơn hàng'; 
ob_start(); 
?>

<style>
  :root { --primary-color:#0d6efd; --success-color:#198754; --warning-color:#ffc107; --danger-color:#dc3545; --info-color:#0dcaf0; --border-radius:8px; }
  .status-badge{font-size:.85rem;padding:.5rem 1rem;border-radius:20px}
  .status-pending{background:var(--warning-color);color:#000}
  .status-paid{background:var(--info-color);color:#000}
  .status-completed{background:var(--success-color);color:#fff}
  .status-cancelled{background:var(--danger-color);color:#fff}
  .status-return_requested{background:#6f42c1;color:#fff}
  .product-img{width:60px;height:60px;object-fit:cover;border-radius:6px}
  .timeline{position:relative;padding-left:2rem}
  .timeline::before{content:'';position:absolute;left:15px;top:0;bottom:0;width:2px;background:#e9ecef}
  .timeline-item{position:relative;margin-bottom:1.5rem}
  .timeline-item::before{content:'';position:absolute;left:-2rem;top:5px;width:12px;height:12px;border-radius:50%;background:var(--primary-color);border:2px solid #fff}
  .action-btn{border-radius:var(--border-radius);font-weight:500}
  .card{border:none;border-radius:var(--border-radius);box-shadow:0 2px 8px rgba(0,0,0,.1)}
  .table th{border-top:none;font-weight:600;color:#495057}
</style>

<div class="py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-1 fw-bold">Chi tiết đơn hàng</h1>
      <p class="text-muted mb-0">Mã đơn hàng: #<?= (int)($order['id'] ?? 0) ?></p>
    </div>
    <a class="btn btn-outline-primary action-btn" href="index.php?action=account_orders"><i class="fas fa-arrow-left me-2"></i>Về danh sách</a>
  </div>

  <?php if (!$order): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <div>Không tìm thấy đơn hàng.</div>
    </div>
  <?php else: ?>
    <?php 
      $statusMap = [
        'pending' => ['text'=>'Chờ xác nhận','class'=>'status-pending'],
        'paid' => ['text'=>'Đang giao','class'=>'status-paid'],
        'completed' => ['text'=>'Hoàn thành','class'=>'status-completed'],
        'cancelled' => ['text'=>'Đã hủy','class'=>'status-cancelled'],
        'return_requested' => ['text'=>'Yêu cầu đổi/trả','class'=>'status-return_requested'],
      ];
      $currentStatus = $statusMap[$order['status']] ?? ['text'=>$order['status'],'class'=>'status-pending'];
    ?>

    <div class="card p-4 mb-4">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3"><strong class="text-muted d-block">Ngày đặt hàng</strong><span class="fs-6"><?= htmlspecialchars($order['created_at']) ?></span></div>
          <div class="mb-3"><strong class="text-muted d-block">Phương thức thanh toán</strong><span class="fs-6"><?= htmlspecialchars($order['payment_method'] ?? 'Chuyển khoản') ?></span></div>
        </div>
        <div class="col-md-6">
          <div class="mb-3"><strong class="text-muted d-block">Trạng thái</strong><span class="status-badge <?= $currentStatus['class'] ?>"><?= $currentStatus['text'] ?></span></div>
          <div class="mb-3"><strong class="text-muted d-block">Tổng tiền</strong><span class="fs-5 fw-bold text-primary"><?= number_format((int)$order['total'],0,',','.') ?>₫</span></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card p-4 mb-4">
          <h3 class="h5 mb-3 fw-bold d-flex align-items-center"><i class="fas fa-cart-shopping me-2 text-primary"></i>Sản phẩm đã đặt</h3>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light"><tr><th>Sản phẩm</th><th class="text-center">Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead>
              <tbody>
              <?php $sum = 0; foreach (($items ?? []) as $it): $lineTotal = (int)$it['quantity'] * (int)$it['price']; $sum += $lineTotal; ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0"><img src="<?= htmlspecialchars($it['image'] ?? 'https://via.placeholder.com/60') ?>" alt="<?= htmlspecialchars($it['name'] ?? '') ?>" class="product-img me-3"></div>
                      <div class="flex-grow-1"><h6 class="mb-1"><?= htmlspecialchars($it['name'] ?? 'Sản phẩm') ?></h6><small class="text-muted">Mã: #<?= (int)$it['product_id'] ?></small></div>
                    </div>
                  </td>
                  <td class="text-center"><?= (int)$it['quantity'] ?></td>
                  <td class="text-end"><?= number_format((int)$it['price'],0,',','.') ?>₫</td>
                  <td class="text-end fw-semibold"><?= number_format($lineTotal,0,',','.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <tfoot class="table-group-divider"><tr><th colspan="3" class="text-end">Tổng cộng</th><th class="text-end text-primary fs-5"><?= number_format($sum,0,',','.') ?>₫</th></tr></tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card p-4 mb-4">
          <h4 class="h6 mb-3 fw-bold">Thao tác</h4>
          <div class="d-grid gap-2">
            <a class="btn btn-outline-secondary action-btn" target="_blank" href="index.php?action=account_order_print&id=<?= (int)$order['id'] ?>"><i class="fas fa-print me-2"></i>In hóa đơn</a>
            <?php if ($order['status'] === 'completed'): ?>
              <button class="btn btn-outline-success action-btn"><i class="fas fa-star me-2"></i>Đánh giá sản phẩm</button>
            <?php endif; ?>
            <?php if (in_array($order['status'], ['pending', 'paid'])): ?>
              <button class="btn btn-outline-danger action-btn"><i class="fas fa-circle-xmark me-2"></i>Hủy đơn hàng</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="card p-4">
          <h4 class="h6 mb-3 fw-bold">Lịch sử đơn hàng</h4>
          <div class="timeline">
            <div class="timeline-item"><div class="fw-semibold">Đơn hàng đã đặt</div><small class="text-muted"><?= htmlspecialchars($order['created_at']) ?></small></div>
            <?php if ($order['status'] === 'paid' || $order['status'] === 'completed'): ?>
              <div class="timeline-item"><div class="fw-semibold">Đã xác nhận thanh toán</div><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at']) + 3600) ?></small></div>
            <?php endif; ?>
            <?php if ($order['status'] === 'completed'): ?>
              <div class="timeline-item"><div class="fw-semibold">Giao hàng thành công</div><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at']) + 86400) ?></small></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card p-4 mt-3">
      <h3 class="h5 mb-3 fw-bold d-flex align-items-center"><i class="fas fa-left-right me-2 text-warning"></i>Yêu cầu đổi/trả hàng</h3>
      <?php if (!empty($returns)): ?>
        <div class="mb-4">
          <h5 class="h6 mb-3">Yêu cầu hiện có</h5>
          <div class="row g-3">
            <?php foreach ($returns as $r): ?>
              <div class="col-md-6">
                <div class="card border"><div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($r['status']) ?></span>
                    <small class="text-muted"><?= htmlspecialchars($r['created_at'] ?? date('d/m/Y')) ?></small>
                  </div>
                  <p class="mb-0"><?= htmlspecialchars($r['reason']) ?></p>
                </div></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($order['status'] === 'completed'): ?>
        <div class="border-top pt-3">
          <h5 class="h6 mb-3">Gửi yêu cầu mới</h5>
          <form method="post" action="index.php?action=account_order_return" class="row g-2">
            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
            <div class="col-md-8"><input class="form-control" name="reason" placeholder="Nhập lý do đổi/trả hàng..." required></div>
            <div class="col-md-4"><button class="btn btn-warning w-100 action-btn"><i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu</button></div>
          </form>
        </div>
      <?php else: ?>
        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Chỉ có thể yêu cầu đổi/trả khi đơn hàng đã hoàn thành.</div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.action-btn').forEach(btn=>{
      btn.addEventListener('click', function(e){
        if(this.textContent.includes('Hủy đơn hàng')){
          if(!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) e.preventDefault();
        }
      });
    });
  });
</script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>

