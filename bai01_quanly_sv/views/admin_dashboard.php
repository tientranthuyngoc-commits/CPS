<?php 
$title = 'Bảng điều khiển'; 
ob_start(); 
?>
<style>
    :root { 
        --primary-color: #0d6efd; 
        --primary-dark: #0b5ed7;
        --success-color: #198754; 
        --success-dark: #157347;
        --warning-color: #ffc107; 
        --warning-dark: #ffb300;
        --danger-color: #dc3545; 
        --danger-dark: #c82333;
        --info-color: #0dcaf0; 
        --info-dark: #0baccc;
        --dark-color: #212529; 
        --light-bg: #f8f9fa; 
        --border-radius: 12px; 
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
        --shadow-lg: 0 8px 30px rgba(0,0,0,0.15);
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .admin-header {
        background: var(--gradient-primary);
        color: #fff;
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2.5rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .admin-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }

    .stat-card {
        border: none;
        border-radius: var(--border-radius);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        background: #fff;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card:hover::before {
        height: 6px;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-trend {
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .trend-up { color: var(--success-color); }
    .trend-down { color: var(--danger-color); }

    .quick-action-card {
        background: var(--gradient-warning);
        color: #fff;
        border-radius: var(--border-radius);
        padding: 1.75rem 1rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        text-decoration: none;
        display: block;
        border: none;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .quick-action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: var(--shadow-lg);
        text-decoration: none;
        color: #fff;
    }

    .quick-action-card:hover::before {
        left: 100%;
    }

    .table-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        background: #fff;
    }

    .table-card:hover {
        box-shadow: var(--shadow-md);
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.9rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: var(--shadow-sm);
        border: 1px solid transparent;
    }

    .status-pending { 
        background: linear-gradient(135deg, var(--warning-color), var(--warning-dark));
        color: #000;
        border-color: rgba(0,0,0,0.1);
    }
    .status-paid { 
        background: linear-gradient(135deg, var(--info-color), var(--info-dark));
        color: #000;
        border-color: rgba(0,0,0,0.1);
    }
    .status-completed { 
        background: linear-gradient(135deg, var(--success-color), var(--success-dark));
        color: #fff;
        border-color: rgba(255,255,255,0.2);
    }
    .status-cancelled { 
        background: linear-gradient(135deg, var(--danger-color), var(--danger-dark));
        color: #fff;
        border-color: rgba(255,255,255,0.2);
    }

    .chart-container {
        background: #fff;
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .chart-container:hover {
        box-shadow: var(--shadow-md);
    }

    .nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
    }

    .nav-item-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: var(--border-radius);
        padding: 2rem 1.5rem;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
        color: inherit;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .nav-item-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .nav-item-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-lg);
        color: inherit;
        text-decoration: none;
    }

    .nav-item-card:hover::before {
        transform: scaleX(1);
    }

    .nav-icon {
        width: 60px;
        height: 60px;
        background: var(--gradient-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        color: #fff;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .nav-item-card:hover .nav-icon {
        transform: scale(1.1) rotate(8deg);
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
    }

    .opacity-75 { opacity: 0.75; }

    /* Table improvements */
    .table {
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .table thead th {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 1rem 1.25rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 1.25rem;
        vertical-align: middle;
        border-color: #f1f3f4;
        transition: all 0.2s ease;
    }

    .table tbody tr:hover td {
        background: rgba(13, 110, 253, 0.03);
        transform: translateY(-1px);
    }

    /* Card header improvements */
    .card-header {
        background: linear-gradient(135deg, #fff, #f8fafc);
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 1.5rem 1rem;
    }

    /* Button improvements */
    .btn-outline-primary {
        border-width: 2px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    /* Animation for cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card, .table-card, .chart-container, .nav-item-card {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .admin-header {
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .nav-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }
        
        .nav-item-card {
            padding: 1.5rem 1rem;
        }
        
        .chart-container {
            padding: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .admin-header {
            text-align: center;
            padding: 1.25rem;
        }
        
        .nav-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-action-card {
            padding: 1.5rem 1rem;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
        }
    }

    /* Loading animation */
    .loading-pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 3px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }
</style>s

<div class="container-fluid py-4">
    <div class="admin-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-2 fw-bold">Xin chào, <?= htmlspecialchars($username) ?>! 👋</h1>
                <p class="mb-0 opacity-75">Chào mừng bạn trở lại bảng điều khiển quản trị</p>
            </div>
            <div class="col-auto">
                <div class="text-end">
                    <div class="text-white-50 small">Hôm nay</div>
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
                        <div class="text-muted small mb-1">Tổng sản phẩm</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['products'] ?? 0),0,',','.') ?></div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>12% so với tháng trước</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-box-seam"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Tổng đơn hàng</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['orders'] ?? 0),0,',','.') ?></div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>8% so với tháng trước</div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-cart-check"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Người dùng</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['users'] ?? 0),0,',','.') ?></div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>5% so với tháng trước</div>
                    </div>
                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-people"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Doanh thu tháng</div>
                        <div class="h3 mb-2"><?= number_format((int)($stats['revenue'] ?? 0),0,',','.') ?>₫</div>
                        <div class="stat-trend trend-up"><i class="bi bi-arrow-up-right me-1"></i>15% so với tháng trước</div>
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
                            <h5 class="mb-0 fw-bold">Doanh thu 7 ngày gần đây</h5>
                            <select class="form-select form-select-sm w-auto"><option>7 ngày</option><option>30 ngày</option><option>90 ngày</option></select>
                        </div>
                        <div style="height:200px;background:#f8f9fa;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#6c757d">
                            <div class="text-center"><i class="bi bi-bar-chart fs-1 mb-2"></i><div>Biểu đồ doanh thu</div></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Đơn hàng gần đây</h5>
                            <a href="index.php?action=admin_orders" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light"><tr><th>Mã ĐH</th><th>Khách hàng</th><th class="text-end">Tổng tiền</th><th>Trạng thái</th><th>Ngày đặt</th><th class="text-end">Thao tác</th></tr></thead>
                                    <tbody>
                                    <?php foreach (($recentOrders ?? []) as $o): $statusClass = ['pending'=>'status-pending','paid'=>'status-paid','completed'=>'status-completed','cancelled'=>'status-cancelled'][$o['status']] ?? 'status-pending'; ?>
                                        <tr>
                                            <td class="fw-semibold">#<?= (int)$o['id'] ?></td>
                                            <td><div class="d-flex align-items-center"><div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2"><i class="bi bi-person text-primary"></i></div><?= htmlspecialchars($o['customer_name']) ?></div></td>
                                            <td class="text-end fw-semibold"><?= number_format((int)$o['total'],0,',','.') ?>₫</td>
                                            <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($o['status']) ?></span></td>
                                            <td><small class="text-muted"><?= htmlspecialchars($o['created_at']) ?></small></td>
                                            <td class="text-end"><a href="index.php?action=admin_order_detail&id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentOrders)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-cart-x fs-1 d-block mb-2"></i>Chưa có đơn hàng nào</td></tr>
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
                            <h5 class="mb-0 fw-bold">Sản phẩm mới</h5>
                            <a href="index.php?action=admin_products" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="card-body">
                            <?php foreach (($recentProducts ?? []) as $p): ?>
                                <div class="d-flex align-items-center border-bottom py-3">
                                    <div class="flex-shrink-0"><img src="<?= htmlspecialchars($p['image'] ?? 'https://via.placeholder.com/40') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded" width="40" height="40" style="object-fit:cover"></div>
                                    <div class="flex-grow-1 ms-3"><div class="fw-semibold text-truncate" style="max-width:200px;">&nbsp;<?= htmlspecialchars($p['name']) ?></div><div class="text-success fw-semibold"><?= number_format((int)$p['price'],0,',','.') ?>₫</div></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($recentProducts)): ?>
                                <div class="text-center text-muted py-4"><i class="bi bi-box fs-1 d-block mb-2"></i>Chưa có sản phẩm nào</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-white border-0"><h5 class="mb-0 fw-bold">Thao tác nhanh</h5></div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6"><a href="index.php?action=admin_products&add=new" class="quick-action-card text-decoration-none"><div class="text-center"><i class="bi bi-plus-circle fs-2 d-block mb-2"></i><div>Thêm SP</div></div></a></div>
                                <div class="col-6"><a href="index.php?action=admin_orders&filter=pending" class="quick-action-card text-decoration-none" style="background:linear-gradient(135deg,#4facfe 0%, #00f2fe 100%)"><div class="text-center"><i class="bi bi-cart fs-2 d-block mb-2"></i><div>Đơn mới</div></div></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="fw-bold mb-3">Quản lý hệ thống</h5>
        <div class="nav-grid">
            <a href="index.php?action=admin_products" class="nav-item-card"><div class="nav-icon"><i class="bi bi-box-seam"></i></div><div class="fw-semibold">Sản phẩm</div><small class="text-muted">Quản lý kho hàng</small></a>
            <a href="index.php?action=admin_orders" class="nav-item-card"><div class="nav-icon"><i class="bi bi-cart-check"></i></div><div class="fw-semibold">Đơn hàng</div><small class="text-muted">Xử lý đơn hàng</small></a>
            <a href="index.php?action=admin_categories" class="nav-item-card"><div class="nav-icon"><i class="bi bi-tags"></i></div><div class="fw-semibold">Danh mục</div><small class="text-muted">Phân loại sản phẩm</small></a>
            <a href="index.php?action=admin_customers" class="nav-item-card"><div class="nav-icon"><i class="bi bi-people"></i></div><div class="fw-semibold">Khách hàng</div><small class="text-muted">Quản lý người dùng</small></a>
            <a href="index.php?action=admin_promotions" class="nav-item-card"><div class="nav-icon"><i class="bi bi-percent"></i></div><div class="fw-semibold">Khuyến mãi</div><small class="text-muted">Chương trình ưu đãi</small></a>
            <a href="index.php?action=admin_brands" class="nav-item-card"><div class="nav-icon"><i class="bi bi-star"></i></div><div class="fw-semibold">Thương hiệu</div><small class="text-muted">Quản lý nhãn hiệu</small></a>
            <a href="index.php?action=admin_posts" class="nav-item-card"><div class="nav-icon"><i class="bi bi-file-text"></i></div><div class="fw-semibold">Bài viết</div><small class="text-muted">Nội dung website</small></a>
            <a href="index.php?action=admin_banners" class="nav-item-card"><div class="nav-icon"><i class="bi bi-image"></i></div><div class="fw-semibold">Banners</div><small class="text-muted">Quảng cáo & slider</small></a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  setInterval(()=>{ console.log('Refreshing dashboard stats...'); }, 30000);
  document.querySelectorAll('.quick-action-card').forEach(card=>{
    card.addEventListener('click', function(){ const text=this.querySelector('div:last-child').textContent; console.log('Quick action:', text); });
  });
});
</script>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/layout.php'; 
?>
