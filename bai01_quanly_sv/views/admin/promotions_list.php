<?php $title = 'Khuyến mãi'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-percent me-2"></i>Khuyến mãi</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
    <a class="btn btn-primary" href="index.php?action=admin_promotion_form"><i class="bi bi-plus-circle me-1"></i>Thêm khuyến mãi</a>
  </div>
</div>

<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:5%">ID</th>
        <th>Sản phẩm</th>
        <th class="text-end">Giá gốc</th>
        <th class="text-end">Giá KM</th>
        <th style="width:20%">Hiệu lực</th>
        <th style="width:8%" class="text-center">TT</th>
        <th style="width:15%" class="text-end">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $r): ?>
      <tr>
        <td class="fw-semibold text-muted">#<?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['product_name']) ?></td>
        <td class="text-end"><del class="text-muted"><?= number_format((int)$r['base_price'],0,',','.') ?>₫</del></td>
        <td class="text-end fw-bold text-danger"><?= number_format((int)$r['promo_price'],0,',','.') ?>₫</td>
        <td><small class="text-muted"><?= date('d/m/Y', strtotime($r['starts_at'])) ?> → <?= !empty($r['ends_at']) ? date('d/m/Y', strtotime($r['ends_at'])) : '—' ?></small></td>
        <td class="text-center">
          <?php if ((int)$r['active']): ?>
            <span class="badge bg-success">Bật</span>
          <?php else: ?>
            <span class="badge bg-secondary">Tắt</span>
          <?php endif; ?>
        </td>
        <td class="text-end">
          <div class="d-flex gap-1 justify-content-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_promotion_form&id=<?= (int)$r['id'] ?>" title="Sửa"><i class="bi bi-pencil"></i></a>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_promotion_delete&id=<?= (int)$r['id'] ?>" title="Xóa" onclick="return confirm('Xóa khuyến mãi này?')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr>
          <td colspan="7" class="text-center text-muted py-5">
            <i class="bi bi-percent fs-1 d-block mb-2"></i>
            Chưa có khuyến mãi nào
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

