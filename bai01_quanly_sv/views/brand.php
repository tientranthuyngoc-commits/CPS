<?php 
$title = 'Thương hiệu' . (!empty($brand['name']) ? ': ' . htmlspecialchars($brand['name']) : ''); 
ob_start(); 
?>

<style>
    :root { --primary-color:#0d6efd; --success-color:#198754; --warning-color:#ffc107; --danger-color:#dc3545; --light-bg:#f8f9fa; --border-radius:12px; --shadow:0 4px 12px rgba(0,0,0,0.1) }
    .brand-header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;border-radius:var(--border-radius);padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden}
    .brand-header::before{content:'';position:absolute;inset:0;background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="20" font-weight="bold">BRAND</text></svg>');background-size:200px}
    .product-card{border:none;border-radius:var(--border-radius);transition:.3s;box-shadow:0 2px 8px rgba(0,0,0,.08);position:relative;overflow:hidden}
    .product-card:hover{transform:translateY(-5px);box-shadow:var(--shadow)}
    .product-image{height:220px;object-fit:cover;transition:transform .3s}
    .product-card:hover .product-image{transform:scale(1.05)}
    .product-badge{position:absolute;top:10px;left:10px;z-index:2}
    .price-current{font-size:1.1rem;font-weight:700;color:#2c3e50}
    .price-original{font-size:.9rem;color:#6c757d;text-decoration:line-through}
    .discount-badge{background:linear-gradient(45deg,var(--danger-color),#e74c3c);color:#fff;padding:.25rem .5rem;border-radius:4px;font-size:.75rem;font-weight:600}
    .filter-card{border:none;border-radius:var(--border-radius);box-shadow:0 2px 8px rgba(0,0,0,.1)}
    .sort-select{border-radius:var(--border-radius);border:1px solid #dee2e6;transition:.3s}
    .sort-select:focus{border-color:var(--primary-color);box-shadow:0 0 0 .25rem rgba(13,110,253,.15)}
    .pagination .page-link{border-radius:var(--border-radius);margin:0 2px;border:none;color:#495057;font-weight:500}
    .pagination .page-item.active .page-link{background:var(--primary-color);border-color:var(--primary-color)}
    .empty-state{text-align:center;padding:3rem 1rem}
    .empty-state i{font-size:4rem;color:#dee2e6;margin-bottom:1rem}
    .loading-spinner{display:none;text-align:center;padding:2rem}
    .opacity-75{opacity:.75}
</style>

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
                        <span class="opacity-75">sản phẩm</span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 mb-0 fw-bold">Thương hiệu</h1></div>
    <?php endif; ?>

    <div class="card filter-card mb-4"><div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form method="get" action="index.php" class="row g-2 align-items-center">
                    <input type="hidden" name="action" value="brand">
                    <input type="hidden" name="id" value="<?= (int)($_GET['id'] ?? 0) ?>">
                    <div class="col-auto"><label class="form-label mb-0 fw-semibold">Sắp xếp:</label></div>
                    <div class="col-auto">
                        <select class="form-select sort-select" name="sort" onchange="this.form.submit()">
                            <option value="newest" <?= ($paging['sort'] ?? '')==='newest'?'selected':'' ?>>Mới nhất</option>
                            <option value="price_asc" <?= ($paging['sort'] ?? '')==='price_asc'?'selected':'' ?>>Giá: Thấp đến Cao</option>
                            <option value="price_desc" <?= ($paging['sort'] ?? '')==='price_desc'?'selected':'' ?>>Giá: Cao đến Thấp</option>
                            <option value="popular" <?= ($paging['sort'] ?? '')==='popular'?'selected':'' ?>>Phổ biến nhất</option>
                            <option value="name_asc" <?= ($paging['sort'] ?? '')==='name_asc'?'selected':'' ?>>Tên: A-Z</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-md-end text-muted">
                Hiển thị <span class="fw-semibold"><?= min((int)($paging['page'] ?? 1) * (int)($paging['limit'] ?? 12), (int)($paging['total'] ?? 0)) ?></span> / <span class="fw-semibold"><?= number_format((int)($paging['total'] ?? 0),0,',','.') ?></span> sản phẩm
            </div>
        </div>
    </div></div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div>
        <p class="mt-2 text-muted">Đang tải sản phẩm...</p>
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
                                <span class="price-current me-2"><?= number_format($finalPrice,0,',','.') ?>₫</span>
                                <?php if ($hasDiscount): ?><span class="price-original"><?= number_format($originalPrice,0,',','.') ?>₫</span><?php endif; ?>
                            </div>
                            <div class="d-grid gap-2">
                                <a class="btn btn-primary btn-sm" href="index.php?action=product&id=<?= (int)$p['id'] ?>"><i class="bi bi-eye me-1"></i>Xem chi tiết</a>
                                <button class="btn btn-outline-primary btn-sm" onclick="addToCart(<?= (int)$p['id'] ?>,1)"><i class="bi bi-cart-plus me-1"></i>Thêm giỏ hàng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="card"><div class="empty-state"><i class="bi bi-box"></i><h4 class="text-muted mb-3">Chưa có sản phẩm</h4><p class="text-muted mb-4">Hiện chưa có sản phẩm nào trong thương hiệu này.</p><a href="index.php?action=products" class="btn btn-primary"><i class="bi bi-arrow-left me-2"></i>Quay lại danh sách sản phẩm</a></div></div>
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
