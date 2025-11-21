<?php 
$title = 'Bang dieu khien'; 
ob_start(); 
?>
<link rel="stylesheet" href="assets/css/admin_dashboard.css">

<div class="container-fluid py-4">
    <div class="admin-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-2 fw-bold">Xin chao, <?= htmlspecialchars($username) ?>!</h1>
                <p class="mb-0 opacity-75">Chao mung ban tro lai bang dieu khien quan tri</p>
            </div>
            <div class="col-auto">
                <div class="text-end">
                    <div class="text-white-50 small">Hom nay</div>
                    <div class="h5 mb-0"><?= date('d/m/Y') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Tong san pham</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['products'] ?? 0),0,',','.') ?></div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>12% so voi thang truoc</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-box-seam"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Tong don hang</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['orders'] ?? 0),0,',','.') ?></div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>8% so voi thang truoc</div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-cart-check"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Nguoi dung</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['users'] ?? 0),0,',','.') ?></div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>5% so voi thang truoc</div>
                    </div>
                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-people"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Doanh thu thang</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['revenue'] ?? 0),0,',','.') ?> VND</div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>15% so voi thang truoc</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-currency-dollar"></i></div>
                </div>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-4">
                <div class="col-12">
                    <div class="chart-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Doanh thu 7 ngay gan day</h5>
                            <select class="form-select form-select-sm w-auto">
                                <option>7 ngay</option>
                                <option>30 ngay</option>
                                <option>90 ngay</option>
                            </select>
                        </div>
                        <div style="height:200px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#6c757d">
                            <div class="text-center">
                                <i class="bi bi-bar-chart fs-1 mb-2"></i>
                                <div>Bieu do doanh thu</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Don hang gan day</h5>
                            <a href="index.php?action=admin_orders" class="btn btn-sm btn-outline-primary">Xem tat ca</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ma DH</th>
                                            <th>Khach hang</th>
                                            <th class="text-end">Tong tien</th>
                                            <th>Trang thai</th>
                                            <th>Ngay dat</th>
                                            <th class="text-end">Thao tac</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach (($recentOrders ?? []) as $o): $statusClass = ['pending'=>'status-pending','paid'=>'status-paid','completed'=>'status-completed','cancelled'=>'status-cancelled'][$o['status']] ?? 'status-pending'; ?>
                                        <tr>
                                            <td class="fw-semibold">#<?= (int)$o['id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2"><i class="bi bi-person text-primary"></i></div>
                                                    <?= htmlspecialchars($o['customer_name']) ?>
                                                </div>
                                            </td>
                                            <td class="text-end fw-semibold"><?= number_format((int)$o['total'],0,',','.') ?> VND</td>
                                            <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($o['status']) ?></span></td>
                                            <td><small class="text-muted"><?= htmlspecialchars($o['created_at']) ?></small></td>
                                            <td class="text-end"><a href="index.php?action=admin_order_detail&id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentOrders)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-cart-x fs-1 d-block mb-2"></i>Chua co don hang nao</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">San pham moi</h5>
                            <a href="index.php?action=admin_products" class="btn btn-sm btn-outline-primary">Xem tat ca</a>
                        </div>
                        <div class="card-body">
                            <?php foreach (($recentProducts ?? []) as $p): ?>
                                <div class="d-flex align-items-center border-bottom py-3">
                                    <div class="flex-shrink-0">
                                        <img src="<?= htmlspecialchars($p['image'] ?? 'https://via.placeholder.com/40') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded" width="40" height="40" style="object-fit:cover">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-semibold text-truncate" style="max-width:200px;"><?= htmlspecialchars($p['name']) ?></div>
                                        <div class="text-success fw-semibold"><?= number_format((int)$p['price'],0,',','.') ?> VND</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($recentProducts)): ?>
                                <div class="text-center text-muted py-4"><i class="bi bi-box fs-1 d-block mb-2"></i>Chua co san pham nao</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0"><h5 class="mb-0 fw-bold">Thao tac nhanh</h5></div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="index.php?action=admin_products&add=new" class="quick-action-card text-decoration-none">
                                        <div class="text-center"><i class="bi bi-plus-circle fs-2 d-block mb-2"></i><div>Them SP</div></div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="index.php?action=admin_orders&filter=pending" class="quick-action-card text-decoration-none" style="background:linear-gradient(135deg,#4facfe 0%, #00f2fe 100%)">
                                        <div class="text-center"><i class="bi bi-cart fs-2 d-block mb-2"></i><div>Don moi</div></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="fw-bold mb-3">Quan ly he thong</h5>
        <div class="nav-grid">
            <a href="index.php?action=admin_products" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-box-seam"></i></div>
                <div class="fw-semibold">San pham</div>
                <small class="text-muted">Quan ly kho hang</small>
            </a>
            <a href="index.php?action=admin_orders" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-cart-check"></i></div>
                <div class="fw-semibold">Don hang</div>
                <small class="text-muted">Xu ly don hang</small>
            </a>
            <a href="index.php?action=admin_categories" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-tags"></i></div>
                <div class="fw-semibold">Danh muc</div>
                <small class="text-muted">Phan loai san pham</small>
            </a>
            <a href="index.php?action=admin_customers" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-people"></i></div>
                <div class="fw-semibold">Khach hang</div>
                <small class="text-muted">Quan ly nguoi dung</small>
            </a>
            <a href="index.php?action=admin_promotions" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-percent"></i></div>
                <div class="fw-semibold">Khuyen mai</div>
                <small class="text-muted">Chuong trinh uu dai</small>
            </a>
            <a href="index.php?action=admin_brands" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-star"></i></div>
                <div class="fw-semibold">Thuong hieu</div>
                <small class="text-muted">Quan ly nhan hieu</small>
            </a>
            <a href="index.php?action=admin_posts" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-file-text"></i></div>
                <div class="fw-semibold">Bai viet</div>
                <small class="text-muted">Noi dung website</small>
            </a>
            <a href="index.php?action=admin_banners" class="nav-item-card">
                <div class="nav-icon"><i class="bi bi-image"></i></div>
                <div class="fw-semibold">Banners</div>
                <small class="text-muted">Quang cao & slider</small>
            </a>
        </div>
    </div>
</div>

<script src="assets/js/admin_dashboard.js"></script>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/layout.php'; 
?>
