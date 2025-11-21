<?php
$title = 'Thanh toan';
ob_start();
?>
<link rel="stylesheet" href="assets/css/checkout.css">
<?php
$err = $_GET['err'] ?? '';
if ($err) {
    $msg = 'CÃ³ lá»—i xáº£y ra. Vui lÃ²ng thá»­ láº¡i.';
    $errorMap = [
        'payos_missing_order' => 'Thiáº¿u mÃ£ Ä‘Æ¡n hÃ ng Ä‘á»ƒ táº¡o liÃªn káº¿t thanh toÃ¡n.',
        'momo_missing_order' => 'Thiáº¿u mÃ£ Ä‘Æ¡n hÃ ng Ä‘á»ƒ táº¡o liÃªn káº¿t MoMo.',
        'order_not_found' => 'KhÃ´ng tÃ¬m tháº¥y Ä‘Æ¡n hÃ ng.',
        'invalid_amount' => 'Sá»‘ tiá»n Ä‘Æ¡n hÃ ng khÃ´ng há»£p lá»‡.',
        'payos_create_failed' => 'KhÃ´ng táº¡o Ä‘Æ°á»£c liÃªn káº¿t PayOS. Kiá»ƒm tra cáº¥u hÃ¬nh hoáº·c thá»­ láº¡i sau.',
        'momo_create_failed' => 'KhÃ´ng táº¡o Ä‘Æ°á»£c liÃªn káº¿t MoMo. Kiá»ƒm tra cáº¥u hÃ¬nh hoáº·c thá»­ láº¡i sau.',
        'momo_config_invalid' => 'ChÆ°a cáº¥u hÃ¬nh khÃ³a MoMo. Vui lÃ²ng cáº­p nháº­t trong momo/config.php.',
        'momo_payment_failed' => 'Thanh toÃ¡n MoMo chÆ°a thÃ nh cÃ´ng',
        'payment_verify' => 'XÃ¡c minh thanh toÃ¡n khÃ´ng thÃ nh cÃ´ng.',
        'exception' => 'Lá»—i há»‡ thá»‘ng khi táº¡o thanh toÃ¡n. Vui lÃ²ng thá»­ láº¡i.',
        'payos_network' => 'MÃ¡y chá»§ khÃ´ng thá»ƒ káº¿t ná»‘i Ä‘áº¿n PayOS. Vui lÃ²ng báº­t máº¡ng ra ngoÃ i hoáº·c dÃ¹ng phÆ°Æ¡ng thá»©c khÃ¡c.',
        'payos_ssl' => 'Lá»—i chá»©ng chá»‰ SSL khi káº¿t ná»‘i PayOS.',
        'payos_auth' => 'PayOS tá»« chá»‘i xÃ¡c thá»±c. Vui lÃ²ng kiá»ƒm tra client_id/api_key/checksum_key.'
    ];
    if (isset($errorMap[$err])) {
        $msg = $errorMap[$err];
        if ($err === 'momo_payment_failed' && !empty($_GET['reason'])) {
            $msg .= ' â€” ' . strip_tags($_GET['reason']);
        }
    }
    echo '<div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>' . htmlspecialchars($msg) . '</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n<div class="container py-4">
    <div class="checkout-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2 fw-bold"><i class="bi bi-credit-card me-3"></i>Thanh toÃ¡n Ä‘Æ¡n hÃ ng</h1>
                <p class="mb-0 opacity-75">HoÃ n táº¥t thÃ´ng tin Ä‘á»ƒ hoÃ n táº¥t Ä‘Æ¡n hÃ ng cá»§a báº¡n</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="bg-white bg-opacity-20 rounded-pill px-3 py-2 d-inline-block">
                    <i class="bi bi-cart-check me-2"></i>
                    <span class="fw-semibold"><?= count($items ?? []) ?></span>
                    <span class="opacity-75">sáº£n pháº©m</span>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($items)): ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n        <div class="empty-cart">
            <div class="empty-cart-icon"><i class="bi bi-cart-x"></i></div>
            <h3 class="mb-3 fw-bold">Giá» hÃ ng trá»‘ng</h3>
            <p class="text-muted mb-4">Báº¡n chÆ°a cÃ³ sáº£n pháº©m nÃ o Ä‘á»ƒ thanh toÃ¡n</p>
            <a href="index.php" class="btn btn-primary btn-lg"><i class="bi bi-bag me-2"></i>Tiáº¿p tá»¥c mua sáº¯m</a>
        </div>
    <?php else: ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n        <div class="step-indicator">
            <div class="step active"><div class="step-number">1</div><div class="step-label">ThÃ´ng tin</div></div>
            <div class="step"><div class="step-number">2</div><div class="step-label">Váº­n chuyá»ƒn</div></div>
            <div class="step"><div class="step-number">3</div><div class="step-label">Thanh toÃ¡n</div></div>
            <div class="step"><div class="step-number">4</div><div class="step-label">XÃ¡c nháº­n</div></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <form method="post" action="index.php?action=place_order" autocomplete="off" id="checkoutForm">
                    <div class="form-section">
                        <div class="section-header"><h3 class="section-title"><i class="bi bi-truck me-2 text-primary"></i>ThÃ´ng tin giao hÃ ng</h3></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Há» tÃªn <span class="text-danger">*</span></label><input type="text" class="form-control" name="name" required placeholder="Nháº­p há» tÃªn Ä‘áº§y Ä‘á»§"></div>
                            <div class="col-md-6"><label class="form-label">Äiá»‡n thoáº¡i <span class="text-danger">*</span></label><input type="tel" class="form-control" name="phone" pattern="[0-9]{9,11}" required placeholder="Nháº­p sá»‘ Ä‘iá»‡n thoáº¡i"></div>
                            <div class="col-12"><label class="form-label">Äá»‹a chá»‰ giao hÃ ng <span class="text-danger">*</span></label><textarea class="form-control" name="address" rows="3" placeholder="Nháº­p Ä‘á»‹a chá»‰ chi tiáº¿t" required></textarea>
                                <?php if (!empty($addresses)): ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                                <div class="mt-3"><label class="form-label text-muted mb-2">Chá»n nhanh Ä‘á»‹a chá»‰ Ä‘Ã£ lÆ°u:</label><div class="address-quick-select">
                                    <?php foreach ($addresses as $a): ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                                        <div class="address-item" onclick="selectAddress('<?= htmlspecialchars($a['address_line'], ENT_QUOTES) ?>', this)">
                                            <div class="fw-semibold"><?= htmlspecialchars($a['name'] ?: 'NgÆ°á»i nháº­n') ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($a['address_line']) ?></div>
                                            <?php if ((int)$a['is_default'] === 1): ?><span class="badge bg-primary mt-1">Máº·c Ä‘á»‹nh</span><?php endif; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                                        </div>
                                    <?php endforeach; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                                </div></div>
                                <?php endif; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header"><h3 class="section-title"><i class="bi bi-geo-alt me-2 text-primary"></i>PhÆ°Æ¡ng thá»©c váº­n chuyá»ƒn</h3></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">PhÆ°Æ¡ng thá»©c</label><select name="shipping_method" class="form-select" onchange="updateShippingFee()"><option value="standard">TiÃªu chuáº©n (30.000Ä‘) - 3-5 ngÃ y</option><option value="express">Nhanh (50.000Ä‘) - 1-2 ngÃ y</option><option value="pickup">Láº¥y táº¡i cá»­a hÃ ng (0Ä‘)</option></select></div>
                            <div class="col-md-6"><label class="form-label">Khu vá»±c</label><select name="shipping_zone" class="form-select" onchange="updateShippingFee()">
                                <?php if (!empty($zones)): foreach ($zones as $z): ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                                    <option value="<?= (int)$z['id'] ?>" data-fee="<?= (int)$z['fee'] ?>"><?= htmlspecialchars($z['name']) ?> (<?= number_format((int)$z['fee'],0,',','.') ?>Ä‘)</option>
                                <?php endforeach; else: ?><option value="0">Máº·c Ä‘á»‹nh</option><?php endif; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                            </select></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header"><h3 class="section-title"><i class="bi bi-credit-card me-2 text-primary"></i>PhÆ°Æ¡ng thá»©c thanh toÃ¡n</h3></div>
                        <div class="payment-options">
                            <div class="payment-method selected" onclick="selectPaymentMethod('cod', this)"><div class="d-flex align-items-center"><div class="payment-icon"><i class="bi bi-cash"></i></div><div><div class="fw-semibold">Thanh toÃ¡n khi nháº­n hÃ ng (COD)</div><small class="text-muted">Tráº£ tiá»n máº·t khi nháº­n Ä‘Æ°á»£c hÃ ng</small></div></div><input type="radio" name="payment_method" value="cod" checked style="display:none"></div>
                            <div class="payment-method" onclick="selectPaymentMethod('bank_qr', this)"><div class="d-flex align-items-center"><div class="payment-icon"><i class="bi bi-qr-code"></i></div><div><div class="fw-semibold">Chuyá»ƒn khoáº£n VietQR</div><small class="text-muted">QuÃ©t QR code Ä‘á»ƒ chuyá»ƒn khoáº£n</small></div></div><input type="radio" name="payment_method" value="bank_qr" style="display:none"></div>
                            <div class="payment-method" onclick="selectPaymentMethod('momo', this)"><div class="d-flex align-items-center"><div class="payment-icon bg-warning"><i class="bi bi-phone"></i></div><div><div class="fw-semibold">VÃ­ MoMo</div><small class="text-muted">Thanh toÃ¡n qua á»©ng dá»¥ng MoMo</small></div></div><input type="radio" name="payment_method" value="momo" style="display:none"></div>
                            <div class="payment-method" onclick="selectPaymentMethod('payos', this)"><div class="d-flex align-items-center"><div class="payment-icon bg-success"><i class="bi bi-wallet2"></i></div><div><div class="fw-semibold">PayOS</div><small class="text-muted">Thanh toÃ¡n qua PayOS</small></div></div><input type="radio" name="payment_method" value="payos" style="display:none"></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-header"><h3 class="section-title"><i class="bi bi-tag me-2 text-primary"></i>MÃ£ giáº£m giÃ¡</h3></div>
                        <div class="row g-2"><div class="col-8"><input type="text" class="form-control" name="coupon" placeholder="Nháº­p mÃ£ giáº£m giÃ¡ (náº¿u cÃ³)"></div><div class="col-4"><button type="button" class="btn btn-outline-primary w-100" onclick="applyCoupon()">Ãp dá»¥ng</button></div></div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="order-summary">
                    <h3 class="h5 mb-3 fw-bold"><i class="bi bi-receipt me-2"></i>TÃ³m táº¯t Ä‘Æ¡n hÃ ng</h3>
                    <div class="mb-3">
                        <?php foreach ($items as $it): $lineTotal = (int)$it['price'] * (int)$it['quantity']; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                            <div class="summary-item"><div><div class="fw-semibold"><?= htmlspecialchars($it['name']) ?></div><small class="text-muted">x<?= (int)$it['quantity'] ?></small></div><strong><?= number_format($lineTotal,0,',','.') ?>â‚«</strong></div>
                        <?php endforeach; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                    </div>
                    <?php
                    $subtotal = 0; foreach ($items as $it) { $subtotal += (int)$it['price'] * (int)$it['quantity']; }
                    $tax = 0;
                    try {
                        if (!class_exists('App\\Services\\TaxCalculator')) require_once __DIR__ . '/../src/Services/TaxCalculator.php';
                        if (!class_exists('App\\Database')) require_once __DIR__ . '/../src/Database.php';
                        $calc = new \App\Services\TaxCalculator();
                        $pdo = \App\Database::getInstance()->pdo();
                        $st = $pdo->prepare('SELECT tax_category_id FROM products WHERE id = :id');
                        foreach ($items as $it) {
                            $st->execute([':id'=>(int)$it['id']]);
                            $tcId = (int)$st->fetchColumn();
                            $rateRow = $calc->resolveRate($tcId ?: null);
                            $rate = (float)($rateRow['rate'] ?? 0);
                            $type = (string)($rateRow['type'] ?? 'exclusive');
                            $res = $calc->computeLine((int)$it['price'], (int)$it['quantity'], $rate, $type);
                            $tax += (int)$res['tax'];
                        }
                    } catch (\Throwable $e) { /* keep $tax=0 */ }
                    $ship = 30000; $total = $subtotal + $tax + $ship;
                    ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n                    <div class="summary-item"><span>Táº¡m tÃ­nh</span><strong><?= number_format($subtotal,0,',','.') ?>â‚«</strong></div>
                    <div class="summary-item"><span>Thuáº¿ (8%)</span><strong><?= number_format($tax,0,',','.') ?>â‚«</strong></div>
                    <div class="summary-item"><span>PhÃ­ váº­n chuyá»ƒn</span><strong id="shippingFee"><?= number_format($ship,0,',','.') ?>â‚«</strong></div>
                    <div class="summary-total"><div class="d-flex justify-content-between align-items-center"><div><div class="small opacity-75">Tá»•ng thanh toÃ¡n</div><div class="amount" id="totalAmount"><?= number_format($total,0,',','.') ?>â‚«</div></div><i class="bi bi-check-circle-fill fs-2 opacity-75"></i></div></div>
                    <button type="submit" form="checkoutForm" class="btn btn-success btn-checkout"><i class="bi bi-credit-card me-2"></i>Äáº·t hÃ ng ngay</button>
                    <div class="security-badge"><div class="small text-muted"><i class="bi bi-shield-check me-2 text-success"></i><strong>Thanh toÃ¡n an toÃ n & báº£o máº­t</strong></div></div>
                </div>
            </div>
        </div>
    <?php endif; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const form=document.getElementById('checkoutForm');
  if(form){ form.addEventListener('submit', function(e){ const req=form.querySelectorAll('[required]'); let ok=true; req.forEach(f=>{ if(!f.value.trim()){ ok=false; f.classList.add('is-invalid'); } else { f.classList.remove('is-invalid'); } }); if(!ok){ e.preventDefault(); alert('Vui lÃ²ng Ä‘iá»n Ä‘áº§y Ä‘á»§ thÃ´ng tin báº¯t buá»™c!'); } }); }
});
function selectAddress(address, el){ document.querySelectorAll('.address-item').forEach(i=>i.classList.remove('selected')); el.classList.add('selected'); const t=document.querySelector('[name="address"]'); if(t) t.value=address; }
function selectPaymentMethod(method, el){ document.querySelectorAll('.payment-method').forEach(i=>i.classList.remove('selected')); el.classList.add('selected'); const r=document.querySelector(`[name="payment_method"][value="${method}"]`); if(r) r.checked=true; }
function updateShippingFee(){ const m=document.querySelector('[name="shipping_method"]').value; let fee=30000; if(m==='express') fee=50000; else if(m==='pickup') fee=0; document.getElementById('shippingFee').textContent = fee===0? 'Miá»…n phÃ­' : fee.toLocaleString('vi-VN')+'â‚«'; updateTotalAmount(); }
function updateTotalAmount(){ /* placeholder for dynamic total calc */ }
function applyCoupon(){ const c=document.querySelector('[name="coupon"]').value.trim(); if(!c){ alert('Vui lÃ²ng nháº­p mÃ£ giáº£m giÃ¡!'); return; } alert('Äang kiá»ƒm tra mÃ£ giáº£m giÃ¡: '+c); }
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>`r`n<link rel="stylesheet" href="assets/css/checkout.css">`r`n
