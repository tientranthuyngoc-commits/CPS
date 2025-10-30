<?php 
$title = 'Giỏ hàng'; 
ob_start(); 
?>

<style>
  :root { 
    --border: #e5e7eb; 
    --muted: #6b7280; 
    --danger: #dc3545; 
    --success: #198754; 
    --primary: #0d6efd;
    --primary-dark: #6728dbff;
    --light: #f8f9fa;
    --dark: #212529;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }

  .cart-wrap {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
    align-items: start;
  }

  @media (max-width: 992px) { 
    .cart-wrap {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
  }

  .cart-header {
    background: linear-gradient(135deg, #fff, #f8fafc);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary);
  }

  .cart-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .cart-header h1::before {
    content: "🛒";
    font-size: 1.5rem;
  }

  .table {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
  }

  .table thead th {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    font-weight: 600;
    padding: 1rem 1.25rem;
    border: none;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .table tbody td {
    padding: 1.25rem;
    vertical-align: middle;
    border-color: var(--border);
    transition: all 0.2s ease;
  }

  .table tbody tr:hover td {
    background: rgba(13, 110, 253, 0.02);
    transform: translateY(-1px);
  }

  .item-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border: 2px solid var(--border);
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .item-img:hover {
    transform: scale(1.05);
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
  }

  .qty-box {
    display: inline-flex;
    align-items: center;
    border: 2px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .qty-box:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
  }

  .qty-box input {
    width: 70px;
    height: 42px;
    border: 0;
    text-align: center;
    font-weight: 600;
    font-size: 1rem;
    color: var(--dark);
    background: transparent;
    outline: none;
  }

  .qty-btn {
    width: 42px;
    height: 42px;
    border: 0;
    background: var(--light);
    color: var(--dark);
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .qty-btn:hover:not(:disabled) {
    background: var(--primary);
    color: #fff;
    transform: scale(1.05);
  }

  .qty-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
  }

  .sum-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 1rem;
    transition: all 0.2s ease;
  }

  .sum-row:hover {
    background: rgba(0, 0, 0, 0.02);
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    border-radius: 6px;
  }

  .sum-row:last-child {
    border-bottom: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary);
    background: linear-gradient(135deg, #f8fafc, #fff);
    padding: 1.25rem 0.5rem;
    border-radius: 8px;
    margin-top: 0.5rem;
  }

  .badge-stock {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .badge-in {
    background: linear-gradient(135deg, #d1fae5, #10b981);
    color: #065f46;
    border: 1px solid #a7f3d0;
  }

  .badge-out {
    background: linear-gradient(135deg, #fee2e2, #ef4444);
    color: #7f1d1d;
    border: 1px solid #fecaca;
  }

  .cart-summary {
    background: linear-gradient(135deg, #fff, #f8fafc);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    position: sticky;
    top: 2rem;
    border-top: 4px solid var(--primary);
  }

  .cart-summary h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .cart-summary h3::before {
    content: "💰";
    font-size: 1.25rem;
  }

  .btn-checkout {
    width: 100%;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    background: linear-gradient(135deg, var(--success), #16a34a);
    border: none;
    border-radius: 12px;
    color: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
    margin-top: 1.5rem;
  }

  .btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(25, 135, 84, 0.4);
    background: linear-gradient(135deg, #16a34a, #15803d);
  }

  .btn-continue {
    width: 100%;
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    background: transparent;
    border: 2px solid var(--primary);
    border-radius: 12px;
    color: var(--primary);
    transition: all 0.3s ease;
    margin-top: 0.75rem;
  }

  .btn-continue:hover {
    background: var(--primary);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
  }

  .empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 16px;
    box-shadow: var(--shadow);
    border: 2px dashed var(--border);
  }

  .empty-cart-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.5;
  }

  .empty-cart h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 1rem;
  }

  /* Animation for cart items */
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .table tbody tr {
    animation: slideIn 0.3s ease-out;
  }

  /* Responsive improvements */
  @media (max-width: 768px) {
    .cart-header {
      padding: 1.25rem;
      margin-bottom: 1rem;
    }

    .cart-header h1 {
      font-size: 1.5rem;
    }

    .table thead th {
      padding: 0.75rem 1rem;
      font-size: 0.8rem;
    }

    .table tbody td {
      padding: 1rem;
    }

    .cart-summary {
      padding: 1.5rem;
    }

    .item-img {
      width: 60px;
      height: 60px;
    }

    .qty-box input {
      width: 50px;
      height: 36px;
    }

    .qty-btn {
      width: 36px;
      height: 36px;
    }
  }

  @media (max-width: 576px) {
    .cart-wrap {
      gap: 1rem;
    }

    .table {
      font-size: 0.9rem;
    }

    .sum-row:last-child {
      font-size: 1.1rem;
    }

    .btn-checkout,
    .btn-continue {
      padding: 0.875rem 1.5rem;
      font-size: 1rem;
    }
  }

  /* Loading state */
  .loading {
    opacity: 0.6;
    pointer-events: none;
  }

  .loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-top: 2px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
  }
</style>
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
<?php endif; ?>

<script>
function chgQty(id, delta){
  const el = document.getElementById('qty_'+id);
  if(!el) return;
  const min = parseInt(el.min||'1');
  const max = parseInt(el.max||'999');
  let v = parseInt(el.value||'1') + delta;
  if(v<min) v=min; if(v>max) v=max; el.value = v;
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>

