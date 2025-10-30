<?php $title = 'Tài khoản người dùng'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-people me-2"></i>Tài khoản người dùng</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?action=admin"><i class="bi bi-arrow-left me-1"></i>Bảng điều khiển</a>
    <a class="btn btn-primary" href="index.php?action=admin_user_form"><i class="bi bi-plus-circle me-1"></i>Thêm tài khoản</a>
  </div>
</div>
<div class="table-responsive card shadow-sm border-0">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        <th style="width:5%">ID</th>
        <th>Tài khoản</th>
        <th>Email</th>
        <th>Điện thoại</th>
        <th class="text-center">Quyền</th>
        <th class="text-center">Trạng thái</th>
        <th style="width:25%" class="text-end">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (($list ?? []) as $u): ?>
      <tr>
        <td class="fw-semibold text-muted">#<?= (int)$u['id'] ?></td>
        <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
        <td><small><?= htmlspecialchars($u['email']) ?></small></td>
        <td><small class="text-muted"><?= htmlspecialchars($u['phone']) ?></small></td>
        <td class="text-center">
          <?php if ($u['role'] === 'admin'): ?>
            <span class="badge bg-danger"><i class="bi bi-shield"></i> Admin</span>
          <?php else: ?>
            <span class="badge bg-secondary">User</span>
          <?php endif; ?>
        </td>
        <td class="text-center">
          <?= ((int)$u['is_active']===1? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-secondary">Khóa</span>') ?>
        </td>
        <td class="text-end">
          <div class="d-flex gap-1 justify-content-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_user_form&id=<?= (int)$u['id'] ?>" title="Sửa"><i class="bi bi-pencil"></i></a>
            <a class="btn btn-sm btn-outline-<?= (int)$u['is_active'] ? 'warning' : 'success' ?>" href="index.php?action=admin_user_toggle&id=<?= (int)$u['id'] ?>&a=<?= (int)$u['is_active']?0:1 ?>" title="<?= (int)$u['is_active']? 'Khóa':'Mở' ?>">
              <i class="bi bi-<?= (int)$u['is_active'] ? 'lock' : 'unlock' ?>"></i>
            </a>
            <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_user_delete&id=<?= (int)$u['id'] ?>" title="Xóa" onclick="return confirm('Xóa tài khoản này?')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?>
        <tr>
          <td colspan="7" class="text-center text-muted py-5">
            <i class="bi bi-people fs-1 d-block mb-2"></i>
            Chưa có tài khoản nào
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>

