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

<h1 class="h4 mb-3">Đặt hàng thành công</h1>
<?php if ($order): ?>
  <div class="card p-3 shadow-sm">
    <div class="d-flex justify-content-between"><span>Mã đơn hàng</span><strong>#<?= (int)$order['id'] ?></strong></div>
    <div class="d-flex justify-content-between"><span>Phương thức thanh toán</span><strong><?= htmlspecialchars($order['payment_method'] ?? '') ?></strong></div>
    <div class="d-flex justify-content-between"><span>Phí vận chuyển</span><strong><?= number_format((int)($order['shipping_fee'] ?? 0),0,',','.') ?>₫</strong></div>
    <div class="d-flex justify-content-between"><span>Thuế (tổng)</span><strong><?= number_format((int)($order['tax_total'] ?? ($order['tax'] ?? 0)),0,',','.') ?>₫</strong></div>
    <?php if ((int)($order['discount_total'] ?? 0) > 0): ?>
      <div class="d-flex justify-content-between"><span>Giảm giá</span><strong>-<?= number_format((int)$order['discount_total'],0,',','.') ?>₫</strong></div>
    <?php endif; ?>
    <hr>
    <div class="d-flex justify-content-between"><span>Tổng thanh toán</span><strong class="h5 mb-0"><?= number_format((int)($order['total'] ?? 0),0,',','.') ?>₫</strong></div>
  </div>
<?php else: ?>
  <p>Không tìm thấy đơn hàng #<?= $id ?>.</p>
<?php endif; ?>

<p class="mt-3"><a class="btn btn-primary" href="index.php">Về trang chủ</a></p>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
