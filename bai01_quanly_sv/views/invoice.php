<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hóa đơn #<?= (int)($order['id'] ?? 0) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>@media print {.no-print{display:none}}</style>
</head>
<body class="p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Hóa đơn #<?= (int)($order['id'] ?? 0) ?></h1>
    <button class="btn btn-primary no-print" onclick="window.print()">In</button>
  </div>
  <div class="mb-2"><strong>Ngày:</strong> <?= htmlspecialchars($order['created_at'] ?? '') ?></div>
  <div class="mb-2"><strong>Khách hàng:</strong> <?= htmlspecialchars($order['name'] ?? ($order['customer_name'] ?? '')) ?></div>
  <div class="mb-3"><strong>Liên hệ:</strong> <?= htmlspecialchars($order['phone'] ?? '') ?></div>
  <table class="table table-bordered">
    <thead><tr><th>#</th><th>Tên</th><th class="text-end">SL</th><th class="text-end">Giá</th><th class="text-end">Thành tiền</th></tr></thead>
    <tbody>
    <?php $sum=0; foreach (($items ?? []) as $i=>$it): $line=(int)$it['quantity']*(int)$it['price']; $sum+=$line; ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($it['name'] ?? '') ?></td>
        <td class="text-end"><?= (int)$it['quantity'] ?></td>
        <td class="text-end"><?= number_format((int)$it['price'],0,',','.') ?>₫</td>
        <td class="text-end"><?= number_format($line,0,',','.') ?>₫</td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><th colspan="4" class="text-end">Tổng</th><th class="text-end"><?= number_format($sum,0,',','.') ?>₫</th></tr>
    </tfoot>
  </table>
  <div class="text-muted">Địa chỉ: <?= htmlspecialchars($order['address'] ?? '') ?></div>
</body>
</html>

