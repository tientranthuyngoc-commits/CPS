<?php
// Tạo yêu cầu thanh toán MoMo cho đơn hàng
use App\Database;

if (session_status()===PHP_SESSION_NONE) session_start();

function momo_log(string $msg): void { @file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | ".$msg."\n", FILE_APPEND); }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php?action=checkout&err=momo_missing_order'); exit; }

$pdo = Database::getInstance()->pdo();
$st = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
$st->execute([':id'=>$id]);
$order = $st->fetch(\PDO::FETCH_ASSOC);
if (!$order) { header('Location: index.php?action=checkout&err=order_not_found'); exit; }

$amount = (int)($order['total'] ?? 0);
if ($amount <= 0) { header('Location: index.php?action=checkout&err=invalid_amount'); exit; }

$cfg  = require __DIR__ . '/../momo/config.php';
require_once __DIR__ . '/../momo/Momo.php';

// Kiểm tra cấu hình tránh gọi API với khóa mặc định/thiếu
$required = [
  (string)($cfg['partnerCode'] ?? ''),
  (string)($cfg['accessKey'] ?? ''),
  (string)($cfg['secretKey'] ?? ''),
];
if (in_array('', $required, true)
    || str_contains($required[0], 'YOUR_')
    || str_contains($required[1], 'YOUR_')
    || str_contains($required[2], 'YOUR_')) {
    momo_log('config_invalid: '.json_encode([$cfg['partnerCode']??'', $cfg['accessKey']??'', $cfg['secretKey']??'']));
    header('Location: index.php?action=checkout&err=momo_config_invalid');
    exit;
}

$momo = new \Momo\Client($cfg);
// orderId giới hạn độ dài theo khuyến nghị
$orderIdMomo = substr($id . '_' . time(), 0, 36);
$orderInfo = 'Thanh toan don #' . $id;
$res = $momo->createPayment($orderIdMomo, $amount, $orderInfo);

if (!empty($res['payUrl'])) {
    header('Location: ' . $res['payUrl']);
    exit;
}

momo_log('create_failed: '.json_encode($res));
header('Location: index.php?action=checkout&err=momo_create_failed');
exit;
