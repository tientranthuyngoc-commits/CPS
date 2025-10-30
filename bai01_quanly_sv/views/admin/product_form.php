<?php $title = ($product? 'Sửa' : 'Thêm') . ' sản phẩm'; ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0 fw-bold"><i class="bi bi-<?= $product ? 'pencil' : 'plus-circle' ?> me-2"></i><?= $product ? 'Sửa' : 'Thêm' ?> sản phẩm</h1>
  <a class="btn btn-outline-secondary" href="index.php?action=admin_products"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="post" action="index.php?action=admin_product_save" enctype="multipart/form-data" class="card shadow-sm border-0 p-4" autocomplete="off">
  <input type="hidden" name="id" value="<?= (int)($product['id'] ?? 0) ?>">
  <div class="mb-3">
    <label class="form-label">Tên sản phẩm</label>
    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Mô tả</label>
    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
  </div>
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Giá (₫)</label>
      <input type="number" name="price" class="form-control" min="0" step="1" required value="<?= (int)($product['price'] ?? 0) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Đường dẫn ảnh</label>
      <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($product['image'] ?? '') ?>" placeholder="assets/images/xxx.jpg">
      <label class="form-label mt-2">Hoặc tải ảnh chính</label>
      <input type="file" name="image_upload" class="form-control" accept="image/*">
    </div>
  </div>
  <div class="row g-3 mt-1">
    <div class="col-md-6">
      <label class="form-label">Danh mục</label>
      <select name="category_id" class="form-select">
        <option value="0">-- Không chọn --</option>
        <?php foreach (($categories ?? []) as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= ((int)($selectedCategoryId ?? 0) === (int)$c['id'])? 'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Thương hiệu</label>
      <select name="brand_id" class="form-select">
        <option value="0">-- Không chọn --</option>
        <?php foreach (($brands ?? []) as $b): ?>
          <option value="<?= (int)$b['id'] ?>" <?= ((int)($product['brand_id'] ?? 0) === (int)$b['id'])? 'selected':'' ?>><?= htmlspecialchars($b['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="row g-3 mt-1">
    <div class="col-md-4"><label class="form-label">SKU</label><input name="sku" class="form-control" value="<?= htmlspecialchars($product['sku'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Tồn kho</label><input type="number" name="stock" class="form-control" min="0" value="<?= (int)($product['stock'] ?? 0) ?>"></div>
    <div class="col-md-4"><label class="form-label">Trạng thái</label>
      <select name="status" class="form-select">
        <?php foreach (['active'=>'Đang bán','out_of_stock'=>'Hết hàng','discontinued'=>'Ngừng kinh doanh'] as $k=>$v): ?>
          <option value="<?= $k ?>" <?= (($product['status'] ?? 'active')===$k)? 'selected':'' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="row g-3 mt-1">
    <div class="col-md-4">
      <label class="form-label">Gallery 1</label>
      <input type="file" name="gallery1" class="form-control" accept="image/*">
    </div>
    <div class="col-md-4">
      <label class="form-label">Gallery 2</label>
      <input type="file" name="gallery2" class="form-control" accept="image/*">
    </div>
    <div class="col-md-4">
      <label class="form-label">Gallery 3</label>
      <input type="file" name="gallery3" class="form-control" accept="image/*">
    </div>
  </div>
  <div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-1"></i>Lưu sản phẩm</button>
    <a class="btn btn-outline-secondary" href="index.php?action=admin_products"><i class="bi bi-x-circle me-1"></i>Hủy</a>
  </div>
  <div class="row g-3 mt-3">
    <div class="col-md-6">
      <label class="form-label">Nh&oacute;m thuế (kế to&aacute;n)</label>
      <select name="tax_category_id" class="form-select">
        <option value="0">-- Kh&ocirc;ng &aacute;p thuế ri&ecirc;ng --</option>
        <?php foreach(($taxCategories ?? []) as $tc): ?>
          <option value="<?= (int)$tc['id'] ?>" <?= ((int)($selectedTaxCategoryId ?? 0)===(int)$tc['id'])?'selected':'' ?>>[<?= htmlspecialchars($tc['code']) ?>] <?= htmlspecialchars($tc['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <small class="text-muted">D&ugrave;ng &aacute;nh xạ trong menu Thuế &rarr; &Aacute;nh xạ nh&oacute;m &harr; thuế suất.</small>
    </div>
  </div>
</form>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>
