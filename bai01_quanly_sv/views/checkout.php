<?php
$title = 'Thanh toán';
ob_start();
?>
<link rel="stylesheet" href="assets/css/checkout.css">
<?php
$err = $_GET['err'] ?? '';
if ($err) {
    $msg = 'Có lỗi xảy ra. Vui lòng thử lại.';
    $errorMap = [
        'payos_missing_order' => 'Thiếu mã đơn hàng để tạo liên kết thanh toán.',
        'momo_missing_order'  => 'Thiếu mã đơn hàng để tạo liên kết MoMo.',
        'order_not_found'     => 'Không tìm thấy đơn hàng.',
        'invalid_amount'      => 'Số tiền đơn hàng không hợp lệ.',
        'payos_create_failed' => 'Không tạo được liên kết PayOS. Kiểm tra cấu hình hoặc thử lại sau.',
        'momo_create_failed'  => 'Không tạo được liên kết MoMo. Kiểm tra cấu hình hoặc thử lại sau.',
        'momo_config_invalid' => 'Chưa cấu hình khóa MoMo. Vui lòng cập nhật trong momo/config.php.',
        'momo_payment_failed' => 'Thanh toán MoMo chưa thành công',
        'payment_verify'      => 'Xác minh thanh toán không thành công.',
        'exception'           => 'Lỗi hệ thống khi tạo thanh toán. Vui lòng thử lại.',
        'payos_network'       => 'Máy chủ không thể kết nối đến PayOS. Vui lòng bật mạng ra ngoài hoặc dùng phương thức khác.',
        'payos_ssl'           => 'Lỗi chứng chỉ SSL khi kết nối PayOS.',
        'payos_auth'          => 'PayOS từ chối xác thực. Vui lòng kiểm tra client_id/api_key/checksum_key.',
    ];
    if (isset($errorMap[$err])) {
        $msg = $errorMap[$err];
        if ($err === 'momo_payment_failed' && !empty($_GET['reason'])) {
            $msg .= ' — ' . strip_tags($_GET['reason']);
        }
    }
    echo '
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>' . htmlspecialchars($msg) . '</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>
<div class="container py-4">
    <div class="checkout-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2 fw-bold">
                    <i class="bi bi-credit-card me-3"></i>Thanh toán đơn hàng
                </h1>
                <p class="mb-0 opacity-75">
                    Hoàn tất thông tin để hoàn tất đơn hàng của bạn
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="bg-white bg-opacity-20 rounded-pill px-3 py-2 d-inline-block">
                    <i class="bi bi-cart-check me-2"></i>
                    <span class="fw-semibold"><?= count($items ?? []) ?></span>
                    <span class="opacity-75">sản phẩm</span>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="bi bi-cart-x"></i>
            </div>
            <h3 class="mb-3 fw-bold">Giỏ hàng trống</h3>
            <p class="text-muted mb-4">Bạn chưa có sản phẩm nào để thanh toán</p>
            <a href="index.php" class="btn btn-primary btn-lg">
                <i class="bi bi-bag me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="step-indicator">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Thông tin</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Vận chuyển</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Thanh toán</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-label">Xác nhận</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <form method="post"
                      action="index.php?action=place_order"
                      autocomplete="off"
                      id="checkoutForm">
                    <!-- Thông tin giao hàng -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="bi bi-truck me-2 text-primary"></i>Thông tin giao hàng
                            </h3>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Họ tên <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       name="name"
                                       required
                                       placeholder="Nhập họ tên đầy đủ">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                       class="form-control"
                                       name="phone"
                                       pattern="[0-9]{9,11}"
                                       required
                                       placeholder="Nhập số điện thoại">
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    Địa chỉ giao hàng <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control"
                                          name="address"
                                          rows="3"
                                          placeholder="Nhập địa chỉ chi tiết"
                                          required></textarea>

                                <?php if (!empty($addresses)): ?>
                                    <div class="mt-3">
                                        <label class="form-label text-muted mb-2">
                                            Chọn nhanh địa chỉ đã lưu:
                                        </label>
                                        <div class="address-quick-select">
                                            <?php foreach ($addresses as $a): ?>
                                                <div class="address-item"
                                                     onclick="selectAddress('<?= htmlspecialchars($a['address_line'], ENT_QUOTES) ?>', this)">
                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($a['name'] ?: 'Người nhận') ?>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <?= htmlspecialchars($a['address_line']) ?>
                                                    </div>
                                                    <?php if ((int)$a['is_default'] === 1): ?>
                                                        <span class="badge bg-primary mt-1">Mặc định</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức vận chuyển -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="bi bi-geo-alt me-2 text-primary"></i>Phương thức vận chuyển
                            </h3>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Phương thức</label>
                                <select name="shipping_method"
                                        class="form-select"
                                        onchange="updateShippingFee()">
                                    <option value="standard">
                                        Tiêu chuẩn (30.000₫) - 3–5 ngày
                                    </option>
                                    <option value="express">
                                        Nhanh (50.000₫) - 1–2 ngày
                                    </option>
                                    <option value="pickup">
                                        Lấy tại cửa hàng (0₫)
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Khu vực</label>
                                <select name="shipping_zone"
                                        class="form-select"
                                        onchange="updateShippingFee()">
                                    <?php if (!empty($zones)): ?>
                                        <?php foreach ($zones as $z): ?>
                                            <option value="<?= (int)$z['id'] ?>"
                                                    data-fee="<?= (int)$z['fee'] ?>">
                                                <?= htmlspecialchars($z['name']) ?>
                                                (<?= number_format((int)$z['fee'], 0, ',', '.') ?>₫)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="0">Mặc định</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="bi bi-credit-card me-2 text-primary"></i>Phương thức thanh toán
                            </h3>
                        </div>
                        <div class="payment-options">
                            <div class="payment-method selected"
                                 onclick="selectPaymentMethod('cod', this)">
                                <div class="d-flex align-items-center">
                                    <div class="payment-icon">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            Thanh toán khi nhận hàng (COD)
                                        </div>
                                        <small class="text-muted">
                                            Trả tiền mặt khi nhận được hàng
                                        </small>
                                    </div>
                                </div>
                                <input type="radio"
                                       name="payment_method"
                                       value="cod"
                                       checked
                                       style="display:none">
                            </div>

                            <div class="payment-method"
                                 onclick="selectPaymentMethod('bank_qr', this)">
                                <div class="d-flex align-items-center">
                                    <div class="payment-icon">
                                        <i class="bi bi-qr-code"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            Chuyển khoản VietQR
                                        </div>
                                        <small class="text-muted">
                                            Quét QR code để chuyển khoản
                                        </small>
                                    </div>
                                </div>
                                <input type="radio"
                                       name="payment_method"
                                       value="bank_qr"
                                       style="display:none">
                            </div>

                            <div class="payment-method"
                                 onclick="selectPaymentMethod('momo', this)">
                                <div class="d-flex align-items-center">
                                    <div class="payment-icon bg-warning">
                                        <i class="bi bi-phone"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Ví MoMo</div>
                                        <small class="text-muted">
                                            Thanh toán qua ứng dụng MoMo
                                        </small>
                                    </div>
                                </div>
                                <input type="radio"
                                       name="payment_method"
                                       value="momo"
                                       style="display:none">
                            </div>

                            <div class="payment-method"
                                 onclick="selectPaymentMethod('payos', this)">
                                <div class="d-flex align-items-center">
                                    <div class="payment-icon bg-success">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">PayOS</div>
                                        <small class="text-muted">
                                            Thanh toán qua PayOS
                                        </small>
                                    </div>
                                </div>
                                <input type="radio"
                                       name="payment_method"
                                       value="payos"
                                       style="display:none">
                            </div>
                        </div>
                    </div>

                    <!-- Mã giảm giá -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="bi bi-tag me-2 text-primary"></i>Mã giảm giá
                            </h3>
                        </div>
                        <div class="row g-2">
                            <div class="col-8">
                                <input type="text"
                                       class="form-control"
                                       name="coupon"
                                       placeholder="Nhập mã giảm giá (nếu có)">
                            </div>
                            <div class="col-4">
                                <button type="button"
                                        class="btn btn-outline-primary w-100"
                                        onclick="applyCoupon()">
                                    Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="order-summary">
                    <h3 class="h5 mb-3 fw-bold">
                        <i class="bi bi-receipt me-2"></i>Tóm tắt đơn hàng
                    </h3>
                    <div class="mb-3">
                        <?php foreach ($items as $it): 
                            $lineTotal = (int)$it['price'] * (int)$it['quantity']; ?>
                            <div class="summary-item">
                                <div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($it['name']) ?>
                                    </div>
                                    <small class="text-muted">
                                        x<?= (int)$it['quantity'] ?>
                                    </small>
                                </div>
                                <strong>
                                    <?= number_format($lineTotal, 0, ',', '.') ?> ₫
                                </strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php
                    $subtotal = 0;
                    foreach ($items as $it) {
                        $subtotal += (int)$it['price'] * (int)$it['quantity'];
                    }

                    $tax = 0;
                    try {
                        if (!class_exists('App\\Services\\TaxCalculator')) {
                            require_once __DIR__ . '/../src/Services/TaxCalculator.php';
                        }
                        if (!class_exists('App\\Database')) {
                            require_once __DIR__ . '/../src/Database.php';
                        }
                        $calc = new \App\Services\TaxCalculator();
                        $pdo  = \App\Database::getInstance()->pdo();
                        $st   = $pdo->prepare('SELECT tax_category_id FROM products WHERE id = :id');

                        foreach ($items as $it) {
                            $st->execute([':id' => (int)$it['id']]);
                            $tcId    = (int)$st->fetchColumn();
                            $rateRow = $calc->resolveRate($tcId ?: null);
                            $rate    = (float)($rateRow['rate'] ?? 0);
                            $type    = (string)($rateRow['type'] ?? 'exclusive');
                            $res     = $calc->computeLine((int)$it['price'], (int)$it['quantity'], $rate, $type);
                            $tax    += (int)$res['tax'];
                        }
                    } catch (\Throwable $e) {
                        // giữ $tax = 0 nếu lỗi
                    }

                    $ship  = 30000;
                    $total = $subtotal + $tax + $ship;
                    ?>

                    <div class="summary-item">
                        <span>Tạm tính</span>
                        <strong><?= number_format($subtotal, 0, ',', '.') ?> ₫</strong>
                    </div>
                    <div class="summary-item">
                        <span>Thuế (8%)</span>
                        <strong><?= number_format($tax, 0, ',', '.') ?> ₫</strong>
                    </div>
                    <div class="summary-item">
                        <span>Phí vận chuyển</span>
                        <strong id="shippingFee">
                            <?= number_format($ship, 0, ',', '.') ?> ₫
                        </strong>
                    </div>

                    <div class="summary-total">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small opacity-75">Tổng thanh toán</div>
                                <div class="amount" id="totalAmount">
                                    <?= number_format($total, 0, ',', '.') ?> ₫
                                </div>
                            </div>
                            <i class="bi bi-check-circle-fill fs-2 opacity-75"></i>
                        </div>
                    </div>

                    <button type="submit"
                            form="checkoutForm"
                            class="btn btn-success btn-checkout">
                        <i class="bi bi-credit-card me-2"></i>Đặt hàng ngay
                    </button>

                    <div class="security-badge">
                        <div class="small text-muted">
                            <i class="bi bi-shield-check me-2 text-success"></i>
                            <strong>Thanh toán an toàn & bảo mật</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('checkoutForm');
  if (form) {
    form.addEventListener('submit', function(e){
      const req = form.querySelectorAll('[required]');
      let ok = true;
      req.forEach(f => {
        if (!f.value.trim()) {
          ok = false;
          f.classList.add('is-invalid');
        } else {
          f.classList.remove('is-invalid');
        }
      });
      if (!ok) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
      }
    });
  }
});

function selectAddress(address, el) {
  document.querySelectorAll('.address-item').forEach(i => i.classList.remove('selected'));
  el.classList.add('selected');
  const t = document.querySelector('[name="address"]');
  if (t) t.value = address;
}

function selectPaymentMethod(method, el) {
  document.querySelectorAll('.payment-method').forEach(i => i.classList.remove('selected'));
  el.classList.add('selected');
  const r = document.querySelector('[name="payment_method"][value="' + method + '"]');
  if (r) r.checked = true;
}

function updateShippingFee() {
  const m = document.querySelector('[name="shipping_method"]').value;
  let fee = 30000;
  if (m === 'express') fee = 50000;
  else if (m === 'pickup') fee = 0;

  const feeEl = document.getElementById('shippingFee');
  if (feeEl) {
    feeEl.textContent = fee === 0
      ? 'Miễn phí'
      : fee.toLocaleString('vi-VN') + ' ₫';
  }

  updateTotalAmount();
}

function updateTotalAmount() {
  // Placeholder: nếu sau này bạn muốn tính lại total theo fee động, thêm code ở đây
}

function applyCoupon() {
  const c = document.querySelector('[name="coupon"]').value.trim();
  if (!c) {
    alert('Vui lòng nhập mã giảm giá!');
    return;
  }
  alert('Đang kiểm tra mã giảm giá: ' + c);
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
