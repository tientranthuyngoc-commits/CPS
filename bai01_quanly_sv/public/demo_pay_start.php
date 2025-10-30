<?php
// Cổng thanh toán giả lập (demo): cho phép chọn Thành công/Thất bại
use App\Database;

if (session_status()===PHP_SESSION_NONE) session_start();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php?action=checkout&err=order_not_found'); exit; }

$pdo = Database::getInstance()->pdo();
$st = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
$st->execute([':id'=>$id]);
$order = $st->fetch(\PDO::FETCH_ASSOC);
if (!$order) { header('Location: index.php?action=checkout&err=order_not_found'); exit; }

$amount = (int)($order['total'] ?? 0);
$title = 'Cổng Demo';
ob_start();
?>
<div class="row justify-content-center py-5">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4 text-center">
        <h1 class="h5 mb-3">Cổng thanh toán giả lập</h1>
        <p class="mb-1">Đơn hàng: <strong>#<?= (int)$id ?></strong></p>
        <p class="mb-3">Số tiền: <strong><?= number_format($amount,0,',','.') ?>đ</strong></p>
        <div class="d-flex gap-2 justify-content-center">
          <a class="btn btn-success" href="index.php?action=demo_pay_return&id=<?= (int)$id ?>&status=success">Thành công</a>
          <a class="btn btn-outline-danger" href="index.php?action=demo_pay_return&id=<?= (int)$id ?>&status=fail">Thất bại</a>
          <a class="btn btn-secondary" href="index.php?action=checkout">Hủy</a>
        </div>
      </div>
    </div>
  </div>
  </div>
<?php $content = ob_get_clean(); require __DIR__ . '/../views/layout.php';

