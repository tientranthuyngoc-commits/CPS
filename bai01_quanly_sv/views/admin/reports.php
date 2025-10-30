<?php 
$title = 'Thống kê & Báo cáo'; 
// Ensure all variables are initialized
$from = $from ?? date('Y-m-01');
$to = $to ?? date('Y-m-d');
$group = $group ?? 'day';
$revenue = $revenue ?? [];
$ordersByStatus = $ordersByStatus ?? [];
$topProducts = $topProducts ?? [];
$stock = $stock ?? [];
$topCustomers = $topCustomers ?? [];
$byCategory = $byCategory ?? [];
$byBrand = $byBrand ?? [];
$byPayment = $byPayment ?? [];
ob_start(); 
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-chart-bar me-2"></i>Thống kê & Báo cáo</h1>
  <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
</div>

<form class="card shadow-sm border-0 p-4 mb-4" method="get" action="index.php">
  <input type="hidden" name="action" value="admin_reports">
  <div class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label small fw-semibold">Từ ngày</label>
      <input type="date" class="form-control form-control-sm" name="from" value="<?= htmlspecialchars($from ?? '') ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-semibold">Đến ngày</label>
      <input type="date" class="form-control form-control-sm" name="to" value="<?= htmlspecialchars($to ?? '') ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label small fw-semibold">Nhóm theo</label>
      <select class="form-select form-select-sm" name="group">
        <option value="day"   <?= ($group??'')==='day'?'selected':'' ?>>Ngày</option>
        <option value="month" <?= ($group??'')==='month'?'selected':'' ?>>Tháng</option>
        <option value="year"  <?= ($group??'')==='year'?'selected':'' ?>>Năm</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Xem</button>
    </div>
    <div class="col-md-2">
      <a class="btn btn-success btn-sm w-100" href="index.php?action=admin_report_tax&from=<?= urlencode($from??'') ?>&to=<?= urlencode($to??'') ?>"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Xuất CSV</a>
    </div>
  </div>
  <div class="mt-2 d-flex flex-wrap gap-1">
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="quick('today')">Hôm nay</button>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="quick('7')">7 ngày</button>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="quick('30')">30 ngày</button>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="quick('month')">Tháng này</button>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="quick('lastmonth')">Tháng trước</button>
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>In</button>
  </div>
</form>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
      <div class="card-body">
        <div class="text-muted small">Tổng đơn hàng</div>
        <div class="h3 mb-0 fw-bold"><?= number_format(array_sum(array_column($revenue??[], 'orders')) ?: 0, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm border-0 bg-success bg-opacity-10">
      <div class="card-body">
        <div class="text-muted small">Tổng doanh thu</div>
        <div class="h3 mb-0 fw-bold text-success"><?= number_format(array_sum(array_column($revenue??[], 'revenue')) ?: 0, 0, ',', '.') ?>₫</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm border-0 bg-info bg-opacity-10">
      <div class="card-body">
        <div class="text-muted small">Đơn hàng trung bình</div>
        <div class="h3 mb-0 fw-bold"><?php 
          $totalOrders = array_sum(array_column($revenue??[], 'orders')) ?: 1;
          $totalRev = array_sum(array_column($revenue??[], 'revenue')) ?: 0;
          echo number_format((int)($totalRev / $totalOrders), 0, ',', '.') . '₫';
        ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Doanh thu theo <?= htmlspecialchars($group ?? 'day') ?></div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th>Kỳ</th><th class="text-end">Đơn</th><th class="text-end">Doanh thu</th></tr></thead>
            <tbody>
            <?php if (!empty($revenue)): ?>
              <?php foreach ($revenue as $r): ?>
                <tr>
                  <td class="small"><?= htmlspecialchars($r['period'] ?? '') ?></td>
                  <td class="text-end small"><?= number_format((int)($r['orders'] ?? 0), 0, ',', '.') ?></td>
                  <td class="text-end fw-semibold"><?= number_format((int)($r['revenue'] ?? 0), 0, ',', '.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Đơn hàng theo trạng thái</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th>Trạng thái</th><th class="text-end">Số lượng</th></tr></thead>
            <tbody>
            <?php if (!empty($ordersByStatus)): ?>
              <?php foreach ($ordersByStatus as $s): ?>
                <tr>
                  <td><?= htmlspecialchars($s['status'] ?? '') ?></td>
                  <td class="text-end fw-semibold"><?= number_format((int)($s['cnt'] ?? 0), 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="2" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Top sản phẩm bán chạy</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th style="width:8%">#</th><th>Sản phẩm</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr></thead>
            <tbody>
            <?php if (!empty($topProducts)): ?>
              <?php $i = 1; foreach ($topProducts as $p): ?>
                <tr>
                  <td class="fw-semibold text-muted"><?= $i++ ?></td>
                  <td class="small"><?= htmlspecialchars($p['name'] ?? '') ?></td>
                  <td class="text-end small"><?= number_format((int)($p['qty'] ?? 0), 0, ',', '.') ?></td>
                  <td class="text-end fw-semibold text-success"><?= number_format((int)($p['amount'] ?? 0), 0, ',', '.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Tồn kho thấp</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th style="width:8%">#</th><th>Sản phẩm</th><th class="text-center">SKU</th><th class="text-end">Tồn</th></tr></thead>
            <tbody>
            <?php if (!empty($stock)): ?>
              <?php $i = 1; foreach ($stock as $p): ?>
                <tr>
                  <td class="fw-semibold text-muted"><?= $i++ ?></td>
                  <td class="small"><?= htmlspecialchars($p['name'] ?? '') ?></td>
                  <td class="text-center small"><?= htmlspecialchars($p['sku'] ?? '-') ?></td>
                  <td class="text-end">
                    <?php if ((int)($p['stock'] ?? 0) > 0): ?>
                      <span class="badge bg-success"><?= number_format((int)$p['stock'], 0, ',', '.') ?></span>
                    <?php else: ?>
                      <span class="badge bg-danger">Hết</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Khách hàng tiềm năng</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th style="width:8%">#</th><th>Tên</th><th>Điện thoại</th><th class="text-end">Số đơn</th><th class="text-end">Tổng chi</th></tr></thead>
            <tbody>
            <?php if (!empty($topCustomers)): ?>
              <?php $i = 1; foreach ($topCustomers as $c): ?>
                <tr>
                  <td class="fw-semibold text-muted"><?= $i++ ?></td>
                  <td class="small"><?= htmlspecialchars($c['name'] ?? '') ?></td>
                  <td class="small"><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                  <td class="text-end small"><?= number_format((int)($c['orders'] ?? 0), 0, ',', '.') ?></td>
                  <td class="text-end fw-semibold text-success"><?= number_format((int)($c['spent'] ?? 0), 0, ',', '.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Doanh thu theo danh mục</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th>Danh mục</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr></thead>
            <tbody>
            <?php if (!empty($byCategory)): ?>
              <?php foreach ($byCategory as $r): ?>
                <tr>
                  <td class="small"><?= htmlspecialchars($r['category_name'] ?? $r['name'] ?? '') ?></td>
                  <td class="text-end small"><?= number_format((int)($r['qty'] ?? 0), 0, ',', '.') ?></td>
                  <td class="text-end fw-semibold text-success"><?= number_format((int)($r['amount'] ?? 0), 0, ',', '.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted py-3">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-0 border-bottom fw-semibold">Doanh thu theo thương hiệu</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light"><tr><th>Thương hiệu</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr></thead>
            <tbody>
            <?php if (!empty($byBrand)): ?>
              <?php foreach ($byBrand as $r): ?>
                <tr>
                  <td class="small"><?= htmlspecialchars($r['brand_name'] ?? $r['name'] ?? '') ?></td>
                  <td class="text-end small"><?= number_format((int)($r['qty'] ?? 0), 0, ',', '.') ?></td>
                  <td class="text-end fw-semibold text-success"><?= number_format((int)($r['amount'] ?? 0), 0, ',', '.') ?>₫</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted py-3">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function quick(p) {
  const today = new Date();
  let from, to;
  if (p === 'today') { from = to = today.toISOString().split('T')[0]; }
  else if (p === '7') { from = new Date(today - 7*86400000).toISOString().split('T')[0]; to = today.toISOString().split('T')[0]; }
  else if (p === '30') { from = new Date(today - 30*86400000).toISOString().split('T')[0]; to = today.toISOString().split('T')[0]; }
  else if (p === 'month') { from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0]; to = today.toISOString().split('T')[0]; }
  else if (p === 'lastmonth') { 
    from = new Date(today.getFullYear(), today.getMonth()-1, 1).toISOString().split('T')[0];
    to = new Date(today.getFullYear(), today.getMonth(), 0).toISOString().split('T')[0];
  }
  window.location.href = `index.php?action=admin_reports&from=${from}&to=${to}`;
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
