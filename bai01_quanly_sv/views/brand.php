<?php 
$title = 'Thuong hi?u' . (!empty($brand['name']) ? ': ' . htmlspecialchars($brand['name']) : ''); 
ob_start();`r`n?>`r`n<link rel="stylesheet" href="assets/css/brand.css">

<div class="container py-4">
    <?php if (!empty($brand)): ?>
        <div class="brand-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-2 fw-bold"><?= htmlspecialchars($brand['name']) ?></h1>
                    <?php if (!empty($brand['description'])): ?>
                        <p class="mb-0 opacity-75"><?= htmlspecialchars($brand['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="bg-white bg-opacity-20 rounded-pill px-3 py-2 d-inline-block">
                        <i class="bi bi-grid-3x3-gap me-2"></i>
                        <span class="fw-semibold"><?= number_format((int)($paging['total'] ?? 0),0,',','.') ?></span>
                        <span class="opacity-75">s?n ph?m</span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 mb-0 fw-bold">Thuong hi?u</h1></div>
    <?php endif; ?>

    <div class="card filter-card mb-4"><div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form method="get" action="index.php" class="row g-2 align-items-center">
                    <input type="hidden" name="action" value="brand">
                    <input type="hidden" name="id" value="<?= (int)($_GET['id'] ?? 0) ?>">
                    <div class="col-auto"><label class="form-label mb-0 fw-semibold">S?p x?p:</label></div>
                    <div class="col-auto">
                        <select class="form-select sort-select" name="sort" onchange="this.form.submit()">
                            <option value="newest" <?= ($paging['sort'] ?? '')==='newest'?'selected':'' ?>>M?i nh?t</option>
                            <option value="price_asc" <?= ($paging['sort'] ?? '')==='price_asc'?'selected':'' ?>>Giá: Th?p d?n Cao</option>
                            <option value="price_desc" <?= ($paging['sort'] ?? '')==='price_desc'?'selected':'' ?>>Giá: Cao d?n Th?p</option>
                            <option value="popular" <?= ($paging['sort'] ?? '')==='popular'?'selected':'' ?>>Ph? bi?n nh?t</option>
                            <option value="name_asc" <?= ($paging['sort'] ?? '')==='name_asc'?'selected':'' ?>>Tên: A-Z</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted">
                Hi?n th? <span class="fw-semibold"><?= min((int)($paging['page'] ?? 1) * (int)($paging['limit'] ?? 12), (int)($paging['total'] ?? 0)) ?></span> / <span class="fw-semibold"><?= number_format((int)($paging['total'] ?? 0),0,',','.') ?></span> s?n ph?m
            </div>
        </div>
    </div></div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ðang t?i...</span></div>
        <p class="mt-2 text-muted">Ðang t?i s?n ph?m...</p>
    </div>

    <div class="row g-4" id="productsContainer">
        <?php foreach (($products ?? []) as $p): ?>
            <?php $discount = (int)($p['discount'] ?? 0); $originalPrice = (int)($p['original_price'] ?? $p['price']); $hasDiscount = $discount > 0; $finalPrice = $hasDiscount ? (int)round($originalPrice * (1 - $discount/100)) : (int)$p['price']; $img = $p['image'] ?: 'assets/images/placeholder.svg'; ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card product-card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="card-img-top product-image">
                        <?php if ($hasDiscount): ?><div class="product-badge"><span class="discount-badge">-<?= $discount ?>%</span></div><?php endif; ?>
                        <div class="position-absolute top-0 end-0 p-2" style="z-index:2"><button class="btn btn-light btn-sm rounded-circle shadow-sm" onclick="toggleWishlist(<?= (int)$p['id'] ?>)" data-bs-toggle="tooltip" title="Thêm vào yêu thích"><i class="bi bi-heart"></i></button></div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-semibold mb-2" style="min-height:2.8rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?= htmlspecialchars($p['name']) ?></h6>
                        <div class="mt-auto">
                            <div class="d-flex align-items-center mb-2">
                                <span class="price-current me-2"><?= number_format($finalPrice,0,',','.') ?>?</span>
                                <?php if ($hasDiscount): ?><span class="price-original"><?= number_format($originalPrice,0,',','.') ?>?</span><?php endif; ?>
                            </div>
                            <div class="d-grid gap-2">
                                <a class="btn btn-primary btn-sm" href="index.php?action=product&id=<?= (int)$p['id'] ?>"><i class="bi bi-eye me-1"></i>Xem chi ti?t</a>
                                <button class="btn btn-outline-primary btn-sm" onclick="addToCart(<?= (int)$p['id'] ?>,1)"><i class="bi bi-cart-plus me-1"></i>Thêm gi? hàng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="card"><div class="empty-state"><i class="bi bi-box"></i><h4 class="text-muted mb-3">Chua có s?n ph?m</h4><p class="text-muted mb-4">Hi?n chua có s?n ph?m nào trong thuong hi?u này.</p><a href="index.php?action=products" class="btn btn-primary"><i class="bi bi-arrow-left me-2"></i>Quay l?i danh sách s?n ph?m</a></div></div>
    <?php endif; ?>

    <?php if (($paging['pages'] ?? 1) > 1): ?>
        <?php $buildUrl=function($p){ $qs=['action'=>'brand','id'=>(int)($_GET['id']??0),'page'=>$p,'sort'=>$_GET['sort']??'newest']; return 'index.php?'.http_build_query($qs);}; $page=(int)($paging['page']??1); $pages=(int)($paging['pages']??1); ?>
        <nav class="mt-5" aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="<?= $buildUrl(max(1,$page-1)) ?>"><i class="bi bi-chevron-left"></i></a></li>
                <?php for($i=max(1,$page-2); $i<=min($pages,$page+2); $i++): ?>
                    <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= $page>=$pages?'disabled':'' ?>"><a class="page-link" href="<?= $buildUrl(min($pages,$page+1)) ?>"><i class="bi bi-chevron-right"></i></a></li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function(el){return new bootstrap.Tooltip(el)});
});
function toggleWishlist(id){ console.log('Toggle wishlist', id); }
function addToCart(id,qty){ console.log('Add to cart', id, qty); }
</script>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/layout.php'; 
?>

