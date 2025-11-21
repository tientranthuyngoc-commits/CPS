<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hóa đơn #<?= (int)($order['id'] ?? 0) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--invoice-bg:#f7f9fc;--card:#fff}
    body{background:var(--invoice-bg);padding:24px}
    .invoice-card{background:var(--card);border-radius:12px;padding:24px;box-shadow:0 6px 24px rgba(17,24,39,.06)}
    .company-name{font-weight:700;font-size:1.25rem}
    .muted{color:#6c757d}
    .table thead th{border-bottom:2px solid #e9ecef}
    .amount{font-weight:700}
    .small-muted{font-size:.9rem;color:#6c757d}
    @media print{
      body{background:#fff}
      .no-print{display:none}
      .invoice-card{box-shadow:none;border-radius:0;padding:0}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="invoice-card">
      <div class="row align-items-center mb-4">
        <div class="col-md-6">
          <div class="company-name">Cửa hàng Demo</div>
          <div class="small-muted">Địa chỉ: 123 Đường Ví dụ, Quận Demo, TP. Hometown</div>
          <div class="small-muted">Email: support@example.com • Điện thoại: 0123 456 789</div>
        </div>
        <div class="col-md-6 text-md-end">
          <h4 class="mb-0">HÓA ĐƠN</h4>
          <div class="muted">Mã hóa đơn: <strong>#<?= (int)($order['id'] ?? 0) ?></strong></div>
          <div class="muted">Ngày: <strong><?= htmlspecialchars($order['created_at'] ?? '') ?></strong></div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-6">
          <h6 class="mb-1">Thông tin khách hàng</h6>
          <div><?= htmlspecialchars($order['customer_name'] ?? ($order['name'] ?? '')) ?></div>
          <div class="small-muted"><?= htmlspecialchars($order['email'] ?? '') ?></div>
          <div class="small-muted">Điện thoại: <?= htmlspecialchars($order['phone'] ?? '') ?></div>
          <div class="small-muted">Địa chỉ: <?= nl2br(htmlspecialchars($order['address'] ?? '')) ?></div>
        </div>
        <div class="col-md-6 text-md-end">
          <h6 class="mb-1">Thanh toán</h6>
          <div class="small-muted">Phương thức: <strong><?= htmlspecialchars($order['payment_method'] ?? '') ?></strong></div>
          <div class="small-muted">Trạng thái: <strong><?= htmlspecialchars($order['status'] ?? '') ?></strong></div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="width:5%">#</th>
              <th>Tên sản phẩm</th>
              <th class="text-end">Đơn giá</th>
              <th class="text-end">SL</th>
              <th class="text-end">Thành tiền</th>
            </tr>
          </thead>
          <tbody>
          <?php $subtotal = 0; foreach (($items ?? []) as $i => $it): $qty = (int)($it['quantity'] ?? 0); $price = (int)($it['price'] ?? 0); $line = $qty * $price; $subtotal += $line; ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($it['name'] ?? '') ?>
                <?php if (!empty($it['sku'])): ?><div class="small-muted">SKU: <?= htmlspecialchars($it['sku']) ?></div><?php endif; ?>
              </td>
              <td class="text-end"><?= number_format($price,0,',','.') ?>₫</td>
              <td class="text-end"><?= $qty ?></td>
              <td class="text-end"><?= number_format($line,0,',','.') ?>₫</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="row justify-content-end">
        <div class="col-md-5">
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td class="small-muted">Tổng hàng</td>
                <td class="text-end amount"><?= number_format($subtotal,0,',','.') ?>₫</td>
              </tr>
              <tr>
                <td class="small-muted">Phí vận chuyển</td>
                <td class="text-end amount"><?= number_format((int)($order['shipping_fee'] ?? 0),0,',','.') ?>₫</td>
              </tr>
              <tr>
                <td class="small-muted">Thuế</td>
                <td class="text-end amount"><?= number_format((int)($order['tax_total'] ?? ($order['tax'] ?? 0)),0,',','.') ?>₫</td>
              </tr>
              <?php if ((int)($order['discount_total'] ?? 0) > 0): ?>
              <tr>
                <td class="small-muted">Giảm giá</td>
                <td class="text-end amount">-<?= number_format((int)$order['discount_total'],0,',','.') ?>₫</td>
              </tr>
              <?php endif; ?>
              <tr class="border-top">
                <td class="small-muted">Tổng thanh toán</td>
                <?php $grand = $subtotal + (int)($order['shipping_fee'] ?? 0) + (int)($order['tax_total'] ?? ($order['tax'] ?? 0)) - (int)($order['discount_total'] ?? 0); ?>
                <td class="text-end amount h5 text-success"><?= number_format($grand,0,',','.') ?>₫</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-8 small-muted">Ghi chú: <?= htmlspecialchars($order['note'] ?? '-') ?></div>
        <div class="col-md-4 text-md-end no-print">
          <button class="btn btn-secondary me-2" onclick="window.print()"><i class="fas fa-print me-1"></i>In</button>
          <a class="btn btn-primary" href="index.php?action=account_order_detail&id=<?= (int)($order['id'] ?? 0) ?>">Chi tiết đơn hàng</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

