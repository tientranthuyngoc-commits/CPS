<?php
// Hiển thị QR VietQR cho đơn hàng
use App\Database;

if (session_status()===PHP_SESSION_NONE) session_start();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php?action=checkout&err=order_not_found'); exit; }

$pdo = Database::getInstance()->pdo();
$st = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
$st->execute([':id'=>$id]);
$order = $st->fetch(\PDO::FETCH_ASSOC);
if (!$order) { header('Location: index.php?action=checkout&err=order_not_found'); exit; }

$cfg = require __DIR__ . '/../includes/bank_qr.php';
$bank   = strtoupper(trim($cfg['bank_code'] ?? 'VCB'));
$acct   = preg_replace('/[^0-9]/','', (string)($cfg['account_no'] ?? ''));
$name   = trim($cfg['account_name'] ?? '');
$tmpl   = trim($cfg['template'] ?? 'compact2');
$prefix = trim($cfg['note_prefix'] ?? 'ORDER');

$amount = (int)($order['total'] ?? 0);
if ($amount < 0) $amount = 0;
$info = $prefix.'-'.$id;

// URL ảnh VietQR (render bởi trình duyệt, không cần khóa API)
$qrUrl = 'https://img.vietqr.io/image/'.rawurlencode($bank.'-'.$acct.'-'.$tmpl).'.png'
       . '?amount='.(int)$amount
       . '&addInfo='.rawurlencode($info)
       . ($name ? ('&accountName='.rawurlencode($name)) : '');

$title = 'Quét QR chuyển khoản';
ob_start();
?>
<div class="row justify-content-center py-4">
  <div class="col-md-8 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4 text-center">
        <h1 class="h5 mb-3">Quét QR để thanh toán đơn #<?= (int)$id ?></h1>
        <p class="text-muted mb-3">Số tiền: <strong><?= number_format($amount,0,',','.') ?>đ</strong></p>
        <img src="<?= htmlspecialchars($qrUrl) ?>" alt="VietQR" style="max-width:100%;height:auto;border-radius:12px;border:1px solid #eee;" onerror="this.replaceWith(document.createTextNode('Không tải được QR. Kiểm tra Internet.'));">
        <div class="mt-3 text-start small">
          <p class="mb-1">- Ngân hàng: <strong><?= htmlspecialchars($bank) ?></strong></p>
          <p class="mb-1">- STK: <strong><?= htmlspecialchars($acct) ?></strong> (<?= htmlspecialchars($name ?: '...') ?>)</p>
          <p class="mb-1">- Nội dung chuyển khoản: <strong><?= htmlspecialchars($info) ?></strong></p>
          <p class="text-muted">Sau khi chuyển khoản, quay lại trang này và nhấn F5; hoặc bộ phận kiểm duyệt sẽ xác nhận thủ công.</p>
        </div>
        <div class="mt-3 d-flex gap-2 justify-content-center">
          <a class="btn btn-outline-secondary" href="index.php?action=checkout">Quay lại</a>
          <a class="btn btn-primary" href="index.php?action=success&id=<?= (int)$id ?>">Tôi đã thanh toán</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../views/layout.php';

