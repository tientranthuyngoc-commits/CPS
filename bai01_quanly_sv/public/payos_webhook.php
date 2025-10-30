<?php
// Webhook PayOS: cấu hình endpoint này trong trang quản trị PayOS
declare(strict_types=1);

$cfg = require __DIR__ . '/../payos/config.php';
if (!class_exists('PayOS\\PayOS')) require_once __DIR__ . '/../payos/PayOS.php';
if (!class_exists('App\\Database')) require_once __DIR__ . '/../src/Database.php';

use PayOS\PayOS;

// Đọc JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];
$signature = $data['signature'] ?? '';

$client = new PayOS($cfg);
$verified = $signature ? $client->verify($data, $signature) : false;

http_response_code(200);
header('Content-Type: application/json');

if (!$verified) {
    echo json_encode(['ok'=>false,'message'=>'invalid signature']);
    exit;
}

$orderId = (int)($data['orderCode'] ?? 0);
$status  = (string)($data['status'] ?? ''); // e.g. 'PAID','CANCELLED'...
if ($orderId>0) {
    $pdo = \App\Database::getInstance()->pdo();
    if (strtoupper($status)==='PAID') {
        $pdo->prepare("UPDATE orders SET payment_status='paid', status='paid' WHERE id=:id")->execute([':id'=>$orderId]);
    } elseif (strtoupper($status)==='CANCELLED') {
        $pdo->prepare("UPDATE orders SET payment_status='unpaid', status='cancelled' WHERE id=:id")->execute([':id'=>$orderId]);
    }
}

echo json_encode(['ok'=>true]);
exit;
