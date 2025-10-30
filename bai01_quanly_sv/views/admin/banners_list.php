<?php $title = 'Banners'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-image me-2"></i>Banners</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
    <a class="btn btn-primary" href="index.php?action=admin_banner_form"><i class="bi bi-plus-circle me-1"></i>Thêm banner</a>
  </div>
</div>
<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:5%">ID</th>
        <th style="width:15%">Ảnh</th>
        <th>Tiêu đề</th>
        <th>Link</th>
        <th class="text-center">Sort</th>
        <th class="text-center">TT</th>
        <th style="width:15%" class="text-end">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($list as $b): ?>
      <tr>
        <td class="fw-semibold text-muted">#<?= (int)$b['id'] ?></td>
        <td>
          <?php if (!empty($b['image'])): ?>
            <img src="<?= htmlspecialchars($b['image']) ?>" alt="" class="rounded" style="height:60px; width:100px; object-fit:cover;" onerror="this.src='assets/images/placeholder.jpg'">
          <?php else: ?>
            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:60px; width:100px;">
              <i class="bi bi-image text-muted"></i>
            </div>
          <?php endif; ?>
        </td>
        <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
        <td><small class="text-muted text-truncate d-block" style="max-width:200px;"><?= htmlspecialchars($b['link']) ?></small></td>
        <td class="text-center"><span class="badge bg-secondary"><?= (int)$b['sort'] ?></span></td>
        <td class="text-center">
          <?php if ((int)$b['active']): ?>
            <span class="badge bg-success">Bật</span>
          <?php else: ?>
            <span class="badge bg-secondary">Tắt</span>
          <?php endif; ?>
        </td>
        <td class="text-end">
          <div class="d-flex gap-1 justify-content-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_banner_form&id=<?= (int)$b['id'] ?>" title="Sửa"><i class="bi bi-pencil"></i></a>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_banner_delete&id=<?= (int)$b['id'] ?>" title="Xóa" onclick="return confirm('Xóa banner này?')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr>
          <td colspan="7" class="text-center text-muted py-5">
            <i class="bi bi-image fs-1 d-block mb-2"></i>
            Chưa có banner nào
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

