<?php
// Xử lý khi user quay về từ MoMo
use App\Database;

if (session_status()===PHP_SESSION_NONE) session_start();

$resultCode = (int)($_GET['resultCode'] ?? -1);
$partnerCode = $_GET['partnerCode'] ?? '';
$orderId     = $_GET['orderId'] ?? '';
$amount      = (int)($_GET['amount'] ?? 0);
$transId     = $_GET['transId'] ?? '';
// Lưu toàn bộ query để debug khi lỗi
@file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | return_query | ".http_build_query($_GET)."\n", FILE_APPEND);

// orderId của chúng ta đã gắn theo dạng {id}_{timestamp}
$ourId = (int)explode('_', $orderId)[0];

if ($resultCode === 0 && $ourId > 0) {
    try {
        $pdo = Database::getInstance()->pdo();
        $pdo->prepare('UPDATE orders SET payment_status="paid", provider_txn=:t WHERE id=:id')->execute([':t'=>$transId, ':id'=>$ourId]);
        $_SESSION['cart'] = [];
        header('Location: index.php?action=success&id='.$ourId);
        exit;
    } catch (\Throwable $e) {
        @file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | return_update_error | ".$e->getMessage()."\n", FILE_APPEND);
    }
}

// Map một số mã lỗi phổ biến để hiển thị thân thiện hơn
$map = [
  1006 => 'Giao dịch bị hủy bởi người dùng',
  9000 => 'Giao dịch thất bại',
  49   => 'Sai chữ ký/khóa cấu hình',
];
$reason = $map[$resultCode] ?? ('Mã lỗi MoMo: '.$resultCode);
header('Location: index.php?action=checkout&err=momo_payment_failed&code='.(int)$resultCode.'&reason='.urlencode($reason));
exit;
