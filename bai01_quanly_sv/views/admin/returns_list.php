<?php $title = 'Quản lý đổi / trả hàng'; ob_start(); ?>
<style>
.status-badge { padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600; text-transform:uppercase; }
.s-requested { background:#fff3cd; color:#856404; }
.s-approved { background:#d1e7dd; color:#0f5132; }
.s-rejected { background:#f8d7da; color:#842029; }
.s-in_progress { background:#cff4fc; color:#055160; }
.s-finished { background:#e2e3e5; color:#41464b; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1 fw-bold">Đổi / Trả hàng</h1>
    <p class="text-muted mb-0">Xử lý yêu cầu đổi trả của khách</p>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Đơn hàng</th>
            <th>Khách</th>
            <th>Lý do</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th class="text-end">Thao tác</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td>
              <div class="fw-semibold">#<?= (int)$r['order_id'] ?></div>
              <small class="text-muted"><?= htmlspecialchars($r['order_status'] ?? '') ?></small>
            </td>
            <td>
              <div><?= htmlspecialchars($r['customer_name'] ?? ($r['username'] ?? '')) ?></div>
              <small class="text-muted"><?= number_format((int)($r['total'] ?? 0),0,',','.') ?>₫</small>
            </td>
            <td style="max-width:260px; white-space:normal;"><?= nl2br(htmlspecialchars($r['reason'] ?? '')) ?></td>
            <td>
              <?php $s = $r['status'] ?? 'requested'; ?>
              <span class="status-badge s-<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></span>
            </td>
            <td><small class="text-muted"><?= htmlspecialchars($r['created_at'] ?? '') ?></small></td>
            <td class="text-end">
              <form method="post" action="index.php?action=admin_returns_update" class="d-inline-flex gap-1">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <select name="status" class="form-select form-select-sm">
                  <?php foreach (['requested','approved','in_progress','finished','rejected'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $opt === ($r['status'] ?? '') ? 'selected' : '' ?>><?= $opt ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-primary">Cập nhật</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Chưa có yêu cầu đổi/trả</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
