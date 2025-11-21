<?php 
$title = 'Chi tiet don hang'; 
ob_start(); 
?>
<link rel="stylesheet" href="assets/css/account_order_detail.css">

<div class="py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-1 fw-bold">Chi ti���t �`��n hA�ng</h1>
      <p class="text-muted mb-0">MA� �`��n hA�ng: #<?= (int)($order['id'] ?? 0) ?></p>
    </div>
    <a class="btn btn-outline-primary action-btn" href="index.php?action=account_orders"><i class="fas fa-arrow-left me-2"></i>V��? danh sA�ch</a>
  </div>

  <?php if (!$order): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <div>KhA'ng tA�m th���y �`��n hA�ng.</div>
    </div>
  <?php else: ?>
    <?php 
      $statusMap = [
        'pending' => ['text'=>'Ch��? xA�c nh��-n','class'=>'status-pending'],
        'paid' => ['text'=>'�?ang giao','class'=>'status-paid'],
        'completed' => ['text'=>'HoA�n thA�nh','class'=>'status-completed'],
        'cancelled' => ['text'=>'�?A� h��y','class'=>'status-cancelled'],
        'return_requested' => ['text'=>'YA�u c��u �`��i/tr���','class'=>'status-return_requested'],
      ];
      $currentStatus = $statusMap[$order['status']] ?? ['text'=>$order['status'],'class'=>'status-pending'];
    ?>

    <div class="card p-4 mb-4">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3"><strong class="text-muted d-block">NgA�y �`���t hA�ng</strong><span class="fs-6"><?= htmlspecialchars($order['created_at']) ?></span></div>
          <div class="mb-3"><strong class="text-muted d-block">Ph����ng th��cc thanh toA�n</strong><span class="fs-6"><?= htmlspecialchars($order['payment_method'] ?? 'Chuy���n kho���n') ?></span></div>
        </div>
        <div class="col-md-6">
          <div class="mb-3"><strong class="text-muted d-block">Tr���ng thA�i</strong><span class="status-badge <?= $currentStatus['class'] ?>"><?= $currentStatus['text'] ?></span></div>
          <div class="mb-3"><strong class="text-muted d-block">T��ng ti��?n</strong><span class="fs-5 fw-bold text-primary"><?= number_format((int)$order['total'],0,',','.') ?>�,�</span></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card p-4 mb-4">
          <h3 class="h5 mb-3 fw-bold d-flex align-items-center"><i class="fas fa-cart-shopping me-2 text-primary"></i>S���n ph��cm �`A� �`���t</h3>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light"><tr><th>S���n ph��cm</th><th class="text-center">S��` l�����ng</th><th class="text-end">�?��n giA�</th><th class="text-end">ThA�nh ti��?n</th></tr></thead>
              <tbody>
              <?php $sum = 0; foreach (($items ?? []) as $it): $lineTotal = (int)$it['quantity'] * (int)$it['price']; $sum += $lineTotal; ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0"><img src="<?= htmlspecialchars($it['image'] ?? 'https://via.placeholder.com/60') ?>" alt="<?= htmlspecialchars($it['name'] ?? '') ?>" class="product-img me-3"></div>
                      <div class="flex-grow-1"><h6 class="mb-1"><?= htmlspecialchars($it['name'] ?? 'S���n ph��cm') ?></h6><small class="text-muted">MA�: #<?= (int)$it['product_id'] ?></small></div>
                    </div>
                  </td>
                  <td class="text-center"><?= (int)$it['quantity'] ?></td>
                  <td class="text-end"><?= number_format((int)$it['price'],0,',','.') ?>�,�</td>
                  <td class="text-end fw-semibold"><?= number_format($lineTotal,0,',','.') ?>�,�</td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <tfoot class="table-group-divider"><tr><th colspan="3" class="text-end">T��ng c��Tng</th><th class="text-end text-primary fs-5"><?= number_format($sum,0,',','.') ?>�,�</th></tr></tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card p-4 mb-4">
          <h4 class="h6 mb-3 fw-bold">Thao tA�c</h4>
          <div class="d-grid gap-2">
            <a class="btn btn-outline-secondary action-btn" target="_blank" href="index.php?action=account_order_print&id=<?= (int)$order['id'] ?>"><i class="fas fa-print me-2"></i>In hA3a �`��n</a>
            <?php if ($order['status'] === 'completed'): ?>
              <button class="btn btn-outline-success action-btn"><i class="fas fa-star me-2"></i>�?A�nh giA� s���n ph��cm</button>
            <?php endif; ?>
            <?php if (in_array($order['status'], ['pending', 'paid'])): ?>
              <button class="btn btn-outline-danger action-btn"><i class="fas fa-circle-xmark me-2"></i>H��y �`��n hA�ng</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="card p-4">
          <h4 class="h6 mb-3 fw-bold">L��<ch s��- �`��n hA�ng</h4>
          <div class="timeline">
            <div class="timeline-item"><div class="fw-semibold">�?��n hA�ng �`A� �`���t</div><small class="text-muted"><?= htmlspecialchars($order['created_at']) ?></small></div>
            <?php if ($order['status'] === 'paid' || $order['status'] === 'completed'): ?>
              <div class="timeline-item"><div class="fw-semibold">�?A� xA�c nh��-n thanh toA�n</div><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at']) + 3600) ?></small></div>
            <?php endif; ?>
            <?php if ($order['status'] === 'completed'): ?>
              <div class="timeline-item"><div class="fw-semibold">Giao hA�ng thA�nh cA'ng</div><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at']) + 86400) ?></small></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card p-4 mt-3">
      <h3 class="h5 mb-3 fw-bold d-flex align-items-center"><i class="fas fa-left-right me-2 text-warning"></i>YA�u c��u �`��i/tr��� hA�ng</h3>
      <?php if (!empty($returns)): ?>
        <div class="mb-4">
          <h5 class="h6 mb-3">YA�u c��u hi���n cA3</h5>
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
          <h5 class="h6 mb-3">G��-i yA�u c��u m��>i</h5>
          <form method="post" action="index.php?action=account_order_return" class="row g-2">
            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
            <div class="col-md-8"><input class="form-control" name="reason" placeholder="Nh��-p lA� do �`��i/tr��� hA�ng..." required></div>
            <div class="col-md-4"><button class="btn btn-warning w-100 action-btn"><i class="fas fa-paper-plane me-2"></i>G��-i yA�u c��u</button></div>
          </form>
        </div>
      <?php else: ?>
        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Ch��% cA3 th��� yA�u c��u �`��i/tr��� khi �`��n hA�ng �`A� hoA�n thA�nh.</div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<script src="assets/js/account_order_detail.js"></script>

<?php $content = ob_get_clean(); require __DIR__.'/layout.php'; ?>
