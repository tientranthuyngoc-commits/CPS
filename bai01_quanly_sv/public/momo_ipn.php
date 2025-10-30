<?php
// IPN từ MoMo
use App\Database;

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '[]', true) ?: [];

// resultCode=0 là thành công
if (($data['resultCode'] ?? -1) === 0) {
    $orderId = (string)($data['orderId'] ?? '');
    $transId = (string)($data['transId'] ?? '');
    $ourId = (int)explode('_', $orderId)[0];
    if ($ourId > 0) {
        try {
            $pdo = Database::getInstance()->pdo();
            $pdo->prepare('UPDATE orders SET payment_status="paid", provider_txn=:t WHERE id=:id')->execute([':t'=>$transId, ':id'=>$ourId]);
        } catch (\Throwable $e) {
            @file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | ipn_update_error | ".$e->getMessage()."\n", FILE_APPEND);
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['resultCode'=>0,'message'=>'OK']);

