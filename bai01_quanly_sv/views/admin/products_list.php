<?php $title = 'Quan ly san pham'; ob_start(); ?>
<?php require_once __DIR__ . '/../../includes/rbac.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>San pham</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bang dieu khien</a>
    <a class="btn btn-outline-success" href="index.php?action=admin_product_excel"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Import Excel</a>
    <a class="btn btn-primary" href="index.php?action=admin_product_form"><i class="bi bi-plus-circle me-1"></i>Them san pham</a>
  </div>
</div>

<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:5%">ID</th>
        <th style="width:8%">Anh</th>
        <th>Ten san pham</th>
        <th class="text-end">Gia</th>
        <th class="text-center">SKU</th>
        <th class="text-center">Ton kho</th>
        <th style="width:8%">Trang thai</th>
        <th style="width:20%" class="text-end">Hanh dong</th>
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
        <td class="text-end fw-bold text-success"><?= number_format((int)$p['price'],0,',','.') ?> d</td>
        <td class="text-center"><small class="text-muted"><?= htmlspecialchars($p['sku'] ?? '-') ?></small></td>
        <td class="text-center">
          <?php if ((int)($p['stock'] ?? 0) > 0): ?>
            <span class="badge bg-success"><?= (int)$p['stock'] ?></span>
          <?php else: ?>
            <span class="badge bg-danger">Het</span>
          <?php endif; ?>
        </td>
        <td>
          <?php
          $statusClass = [
            'active'        => 'success',
            'out_of_stock'  => 'warning',
            'discontinued'  => 'danger',
          ][$p['status']] ?? 'secondary';

          $statusText = [
            'active'        => 'Dang ban',
            'out_of_stock'  => 'Het hang',
            'discontinued'  => 'Ngung kinh doanh',
          ][$p['status']] ?? $p['status'];
          ?>
          <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
        </td>
        <td class="text-end">
          <div class="d-flex gap-1 justify-content-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_product_form&id=<?= (int)$p['id'] ?>" title="Sua">
              <i class="bi bi-pencil"></i>
            </a>
            <a class="btn btn-sm btn-outline-info" href="index.php?action=admin_product_attrs&id=<?= (int)$p['id'] ?>" title="Thuoc tinh">
              <i class="bi bi-tags"></i>
            </a>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_product_delete&id=<?= (int)$p['id'] ?>" title="Xoa" onclick="return confirm('Xoa san pham nay?')">
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
            Chua co san pham nao
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
