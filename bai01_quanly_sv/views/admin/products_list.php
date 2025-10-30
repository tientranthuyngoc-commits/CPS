<?php $title = 'Quản lý sản phẩm'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Sản phẩm</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
    <a class="btn btn-primary" href="index.php?action=admin_product_form"><i class="bi bi-plus-circle me-1"></i>Thêm sản phẩm</a>
  </div>
</div>

<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:5%">ID</th>
        <th style="width:8%">Ảnh</th>
        <th>Tên sản phẩm</th>
        <th class="text-end">Giá</th>
        <th class="text-center">SKU</th>
        <th class="text-center">Tồn kho</th>
        <th style="width:8%">Trạng thái</th>
        <th style="width:20%" class="text-end">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $p): ?>
      <tr>
        <td class="fw-semibold text-muted">#<?= (int)$p['id'] ?></td>
        <td>
          <?php if (!empty($p['image'])): ?>
            <img src="<?= htmlspecialchars($p['image']) ?>" alt="" class="rounded" style="height:48px; width:48px; object-fit:cover;" onerror="this.src='assets/images/placeholder.jpg'">
          <?php else: ?>
            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:48px; width:48px;">
              <i class="bi bi-image text-muted"></i>
            </div>
          <?php endif; ?>
        </td>
        <td>
          <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
          <?php if (!empty($p['brand'])): ?>
            <small class="text-muted"><i class="bi bi-star"></i> <?= htmlspecialchars($p['brand']) ?></small>
          <?php endif; ?>
        </td>
        <td class="text-end fw-bold text-success"><?= number_format((int)$p['price'],0,',','.') ?>₫</td>
        <td class="text-center"><small class="text-muted"><?= htmlspecialchars($p['sku'] ?? '-') ?></small></td>
        <td class="text-center">
          <?php if ((int)($p['stock'] ?? 0) > 0): ?>
            <span class="badge bg-success"><?= (int)$p['stock'] ?></span>
          <?php else: ?>
            <span class="badge bg-danger">Hết</span>
          <?php endif; ?>
        </td>
        <td>
          <?php
          $statusClass = ['active'=>'success','out_of_stock'=>'warning','discontinued'=>'danger'][$p['status']] ?? 'secondary';
          $statusText = ['active'=>'Đang bán','out_of_stock'=>'Hết hàng','discontinued'=>'Ngừng kinh doanh'][$p['status']] ?? $p['status'];
          ?>
          <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
        </td>
        <td class="text-end">
          <div class="d-flex gap-1 justify-content-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_product_form&id=<?= (int)$p['id'] ?>" title="Sửa">
              <i class="bi bi-pencil"></i>
            </a>
            <a class="btn btn-sm btn-outline-info" href="index.php?action=admin_product_attrs&id=<?= (int)$p['id'] ?>" title="Thuộc tính">
              <i class="bi bi-tags"></i>
            </a>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_product_delete&id=<?= (int)$p['id'] ?>" title="Xóa" onclick="return confirm('Xóa sản phẩm này?')">
              <i class="bi bi-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr>
          <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
            Chưa có sản phẩm nào
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
