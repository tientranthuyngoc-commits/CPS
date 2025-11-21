<?php 
$title = 'Gio hang'; 
ob_start(); 
?>
<link rel="stylesheet" href="assets/css/cart.css">
<?php 
$subtotal = 0; $totalItems = 0; 
foreach ($items as $it) { $subtotal += (int)$it['price'] * (int)$it['quantity']; $totalItems += (int)$it['quantity']; }
?>
<div class="cart-header">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h1>Giỏ hàng</h1>
      <small class="text-muted">Bạn có <?= (int)count($items) ?> sản phẩm trong giỏ</small>
    </div>
    <div class="text-end">
      <div class="fw-bold" style="font-size:20px;"><?= number_format($subtotal,0,',','.') ?>₫</div>
      <small class="text-muted">Tạm tính</small>
    </div>
  </div>
  <div class="mt-2 d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php"><i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm</a>
  </div>
  </div>

<?php if (empty($items)): ?>
  <div class="alert alert-info">Giỏ hàng trống. <a href="index.php" class="alert-link">Mua sắm ngay</a>.</div>
<?php else: ?>
  <div class="cart-wrap">
    <form method="post" action="index.php?action=update_cart" class="card p-3">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th style="width:90px">Sản phẩm</th>
              <th>Tên</th>
              <th class="text-end" style="width:140px">Đơn giá</th>
              <th class="text-center" style="width:160px">Số lượng</th>
              <th class="text-end" style="width:160px">Thành tiền</th>
              <th style="width:60px"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): $line = (int)$it['price'] * (int)$it['quantity']; $inStock = !empty($it['stock']) && (int)$it['stock']>0; ?>
            <tr>
              <td><img class="item-img" src="<?= htmlspecialchars($it['image'] ?? 'assets/images/placeholder.svg') ?>" alt="<?= htmlspecialchars($it['name']) ?>"></td>
              <td>
                <div class="fw-semibold mb-1"><?= htmlspecialchars($it['name']) ?></div>
                <small class="text-muted">Mã SP: #<?= (int)$it['id'] ?></small>
                <div class="mt-1">
                  <?php if ($inStock): ?><span class="badge-stock badge-in">Còn hàng</span><?php else: ?><span class="badge-stock badge-out">Hết hàng</span><?php endif; ?>
                </div>
              </td>
              <td class="text-end"><span class="text-muted"><?= number_format((int)$it['price'],0,',','.') ?>₫</span></td>
              <td class="text-center">
                <div class="qty-box">
                  <?php $max = (int)($it['stock'] ?? 999); ?>
                  <button type="button" class="qty-btn" onclick="chgQty(<?= (int)$it['id'] ?>,-1)" <?= ((int)$it['quantity']<=1)?'disabled':'' ?>>−</button>
                  <input type="number" class="form-control" name="qty[<?= (int)$it['id'] ?>]" id="qty_<?= (int)$it['id'] ?>" value="<?= (int)$it['quantity'] ?>" min="1" max="<?= $max ?>">
                  <button type="button" class="qty-btn" onclick="chgQty(<?= (int)$it['id'] ?>,1)" <?= ((int)$it['quantity']>=$max)?'disabled':'' ?>>+</button>
                </div>
              </td>
              <td class="text-end fw-semibold"><?= number_format($line,0,',','.') ?>₫</td>
              <td class="text-end"><a class="btn btn-sm btn-outline-danger" href="index.php?action=remove_from_cart&id=<?= (int)$it['id'] ?>" onclick="return confirm('Xóa sản phẩm này?')"><i class="bi bi-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex gap-2 mt-2">
        <button class="btn btn-primary"><i class="bi bi-arrow-clockwise me-1"></i>Cập nhật giỏ hàng</button>
        <a class="btn btn-outline-primary" href="index.php"><i class="bi bi-bag me-1"></i>Mua thêm</a>
      </div>
    </form>

    <div class="card p-3">
      <h6 class="mb-2"><i class="bi bi-receipt me-1"></i>Tóm tắt đơn hàng</h6>
      <?php 
        $taxRate = 0.08; 
        $tax = (int)round($subtotal * $taxRate); 
        $ship = ($subtotal >= 500000 ? 0 : ($subtotal>0 ? 30000 : 0)); 
        $total = $subtotal + $tax + $ship; 
      ?>
      <div class="sum-row"><span>Tạm tính (<?= (int)count($items) ?> sản phẩm)</span><strong><?= number_format($subtotal,0,',','.') ?>₫</strong></div>
      <div class="sum-row"><span>Thuế VAT (8%)</span><strong><?= number_format($tax,0,',','.') ?>₫</strong></div>
      <div class="sum-row"><span>Phí vận chuyển</span><strong><?= $ship===0? '<span class="text-success">Miễn phí</span>' : number_format($ship,0,',','.') . '₫' ?></strong></div>
      <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top:2px solid var(--border)">
        <div class="text-muted">Tổng thanh toán</div>
        <div class="h5 mb-0"><?= number_format($total,0,',','.') ?>₫</div>
      </div>
      <a class="btn btn-success w-100 mt-3" href="index.php?action=checkout"><i class="bi bi-credit-card me-1"></i>Tiến hành thanh toán</a>
    </div>
  </div>
<?php endif; ?>`n<link rel="stylesheet" href="assets/css/cart.css">`n`n<script>
function chgQty(id, delta){
  const el = document.getElementById('qty_'+id);
  if(!el) return;
  const min = parseInt(el.min||'1');
  const max = parseInt(el.max||'999');
  let v = parseInt(el.value||'1') + delta;
  if(v<min) v=min; if(v>max) v=max; el.value = v;
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>`n<link rel="stylesheet" href="assets/css/cart.css">`n`n
