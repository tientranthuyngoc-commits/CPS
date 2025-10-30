<?php
// Kết quả cổng demo
use App\Database;

if (session_status()===PHP_SESSION_NONE) session_start();

$id = (int)($_GET['id'] ?? 0);
$status = ($_GET['status'] ?? '') === 'success' ? 'success' : 'fail';
if ($id <= 0) { header('Location: index.php?action=checkout&err=order_not_found'); exit; }

try {
  $pdo = Database::getInstance()->pdo();
  if ($status === 'success') {
    $pdo->prepare('UPDATE orders SET payment_status="paid", provider_txn=:t WHERE id=:id')
        ->execute([':t'=>'demo_'.time(), ':id'=>$id]);
    $_SESSION['cart'] = [];
    header('Location: index.php?action=success&id='.$id);
  } else {
    header('Location: index.php?action=checkout&err=payment_verify');
  }
  exit;
} catch (\Throwable $e) {
  header('Location: index.php?action=checkout&err=exception');
  exit;
}

