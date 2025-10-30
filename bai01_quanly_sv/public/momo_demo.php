<?php
// Trang DEMO MoMo: hiển thị QR giả lập (không gọi API MoMo)
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
$note   = 'MOMO-DEMO-ORDER-'.$id;

// Dữ liệu mã hóa vào QR (chỉ để trình diễn). Người thật quét sẽ không thanh toán được.
$qrData = 'MOMO_DEMO|ORDER:'.$id.'|AMOUNT:'.$amount.'|NOTE:'.$note;
// Dùng dịch vụ tạo ảnh QR công khai
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data='.rawurlencode($qrData);

$title = 'MoMo Demo QR';
ob_start();
?>
<div class="row justify-content-center py-4">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow-sm" style="border-color:#a50064">
      <div class="card-body p-4 text-center">
        <h1 class="h5 mb-2" style="color:#a50064">MoMo Demo (QR giả lập)</h1>
        <p class="text-muted mb-3">Đơn #<?= (int)$id ?> • Số tiền <strong><?= number_format($amount,0,',','.') ?>đ</strong></p>
        <img src="<?= htmlspecialchars($qrUrl) ?>" alt="MoMo Demo QR" style="max-width:100%;height:auto;border-radius:12px;border:1px solid #eee;" onerror="this.replaceWith(document.createTextNode('Không tải được QR. Kiểm tra Internet.'));">
        <div class="mt-3 small text-start">
          <p class="mb-1">Đây là QR DEMO, không liên kết ví MoMo thật.</p>
          <p class="mb-1">Nội dung mã hóa: <code><?= htmlspecialchars($qrData) ?></code></p>
        </div>
        <div class="mt-3 d-flex gap-2 justify-content-center">
          <a class="btn btn-outline-secondary" href="index.php?action=checkout">Quay lại</a>
          <a class="btn btn-success" href="index.php?action=success&id=<?= (int)$id ?>">Giả lập đã thanh toán</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../views/layout.php';

