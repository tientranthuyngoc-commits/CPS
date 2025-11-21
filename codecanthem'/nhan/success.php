<?php $title = 'Đặt hàng thành công'; ob_start(); ?>
<?php
  $id = (int)($_GET['id'] ?? 0);
  $order = null;
  try {
    if (!class_exists('App\\Database')) require_once __DIR__ . '/../src/Database.php';
    $pdo = \App\Database::getInstance()->pdo();
    $st = $pdo->prepare('SELECT * FROM orders WHERE id=:id');
    $st->execute([':id'=>$id]);
    $order = $st->fetch(\PDO::FETCH_ASSOC) ?: null;
  } catch (\Throwable $e) {}
?>

<style>
  .success-icon { width:64px; height:64px; background:var(--bs-success); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:32px; margin:0 auto 1.5rem; animation:scale-in .5s ease-out; }
  @keyframes scale-in { from { transform:scale(0); opacity:0; } to { transform:scale(1); opacity:1; } }
  .success-alert { animation:slide-up .5s ease-out .2s both; }
  @keyframes slide-up { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }
</style>

<div class="text-center mb-4">
  <div class="success-icon"><i class="fas fa-check"></i></div>
  <h1 class="h4 mb-2">Đặt hàng thành công</h1>
  <p class="text-muted">Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi</p>
</div>

<?php if ($order): ?>
  <div class="card p-4 shadow-sm success-alert">
    <div class="d-flex justify-content-between mb-3">
      <span class="text-muted">Mã đơn hàng</span>
      <strong class="text-primary">#<?= (int)$order['id'] ?></strong>
    </div>
    <div class="d-flex justify-content-between mb-3">
      <span class="text-muted">Phương thức thanh toán</span>
      <strong><?= htmlspecialchars($order['payment_method'] ?? '') ?></strong>
    </div>
    <div class="d-flex justify-content-between mb-3">
      <span class="text-muted">Phí vận chuyển</span>
      <strong><?= number_format((int)($order['shipping_fee'] ?? 0),0,',','.') ?>₫</strong>
    </div>
    <div class="d-flex justify-content-between mb-3">
      <span class="text-muted">Thuế (tổng)</span>
      <strong><?= number_format((int)($order['tax_total'] ?? ($order['tax'] ?? 0)),0,',','.') ?>₫</strong>
    </div>
    <?php if ((int)($order['discount_total'] ?? 0) > 0): ?>
      <div class="d-flex justify-content-between mb-3">
        <span class="text-muted">Giảm giá</span>
        <strong class="text-danger">-<?= number_format((int)$order['discount_total'],0,',','.') ?>₫</strong>
      </div>
    <?php endif; ?>
    <hr>
    <div class="d-flex justify-content-between align-items-center">
      <span>Tổng thanh toán</span>
      <strong class="h4 mb-0 text-success"><?= number_format((int)($order['total'] ?? 0),0,',','.') ?>₫</strong>
    </div>
  </div>

  <div class="text-center mt-4">
    <a class="btn btn-outline-info me-2" href="index.php?action=account_order_print&id=<?= (int)$order['id'] ?>" target="_blank">
      <i class="fas fa-file-invoice me-1"></i>Xem hóa đơn
    </a>
    <a class="btn btn-outline-primary me-2" href="index.php?action=account_order_detail&id=<?= (int)$order['id'] ?>">
      <i class="fas fa-info-circle me-1"></i>Chi tiết đơn hàng
    </a>
    <a class="btn btn-primary" href="index.php">
      <i class="fas fa-home me-1"></i>Về trang chủ
    </a>
  </div>
<?php else: ?>
  <div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle me-2"></i>Không tìm thấy đơn hàng #<?= $id ?>.
  </div>
  <p class="mt-3"><a class="btn btn-primary" href="index.php">Về trang chủ</a></p>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
