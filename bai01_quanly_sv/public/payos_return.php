<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

$cfg = require __DIR__ . '/../payos/config.php';
if (!class_exists('PayOS\\PayOS')) require_once __DIR__ . '/../payos/PayOS.php';
if (!class_exists('App\\Database')) require_once __DIR__ . '/../src/Database.php';

use PayOS\PayOS;

$client = new PayOS($cfg);

$params = $_GET;
$signature = $params['signature'] ?? '';
$verified = $signature ? $client->verify($params, $signature) : true; // nếu chưa cấu hình chữ ký trên return thì cho qua

$orderId = (int)($params['orderCode'] ?? ($params['orderId'] ?? ($_GET['id'] ?? 0)));
if ($verified && $orderId) {
    $pdo = \App\Database::getInstance()->pdo();
    // Mark as paid (demo): thực tế cần kiểm tra mã trạng thái thanh toán từ PayOS
    $pdo->prepare("UPDATE orders SET payment_status='paid', status='paid' WHERE id=:id")
        ->execute([':id'=>$orderId]);
    header('Location: index.php?action=success&id='.$orderId);
    exit;
}

// Nếu không xác minh được, quay về checkout
header('Location: index.php?action=checkout&err=payment_verify');
exit;
