<?php
// Simple RBAC helper with static role → permission mapping
// Centralize roles so the UI can list them all consistently.

if (session_status() === PHP_SESSION_NONE) session_start();

// 1) Define permissions per role (source of truth for which roles exist)
// Avoid wildcards to keep roles clearly separated.
const ROLE_PERMISSIONS = [
    // Chủ shop: toàn quyền (liệt kê rõ để tránh chồng chéo với wildcard)
    'owner' => [
        'admin.panel',
        'product.view','product.create','product.update','product.delete',
        'order.view','order.update',
        'brand.manage','category.manage',
        'tax.view','tax.manage',
        'report.view',
        'user.view','user.manage',
        'customer.view',
        // Quản trị nội dung (banners, posts, pages, promotions, coupons...)
        'content.manage'
    ],

    // Quản trị viên: quyền rộng để vận hành hệ thống
    'admin' => [
        'admin.panel',
        'product.view','product.create','product.update','product.delete',
        'order.view','order.update',
        'brand.manage','category.manage',
        'tax.view','tax.manage',
        'report.view',
        'user.view','user.manage',
        'customer.view',
        'content.manage'
    ],

    // Quản lý: sản phẩm + đơn (không xem báo cáo doanh thu, không xóa sản phẩm)
    'manager' => [
        'admin.panel',
        'product.view','product.create','product.update',
        'order.view','order.update',
        'brand.manage','category.manage'
    ],

    // Bán hàng: thao tác trên đơn, xem khách
    'sales' => [
        'admin.panel',
        'order.view','order.update',
        'customer.view'
    ],

    // Kho: chỉ xem đơn (để chuẩn bị hàng)
    'warehouse' => [
        'admin.panel',
        'order.view'
    ],

    // Kế toán: thuế + xem đơn + xem báo cáo
    'accountant' => [
        'admin.panel',
        'tax.view','tax.manage',
        'order.view',
        'report.view'
    ],

    // CSKH: xem đơn/khách
    'support' => [
        'admin.panel',
        'order.view',
        'customer.view'
    ],

    // Khách (mặc định)
    'user' => [],
];

function current_user_role(): string {
    return $_SESSION['role'] ?? 'user';
}

function role_has_permission(string $role, string $perm): bool {
    $perms = ROLE_PERMISSIONS[$role] ?? [];
    if (in_array($perm, $perms, true)) return true;
    // support wildcard matching like product.* (we avoid wildcards in mapping, but keep backwards compatibility)
    $parts = explode('.', $perm);
    while (count($parts) > 1) {
        array_pop($parts);
        if (in_array(implode('.', $parts) . '.*', $perms, true)) return true;
    }
    return false;
}

function can(string $perm): bool { return role_has_permission(current_user_role(), $perm); }

function require_permission(string $perm): void {
    if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
    if (!can($perm)) {
        http_response_code(403);
        require __DIR__ . '/../views/error_404.php';
        exit;
    }
}

// 2) Vietnamese labels for roles. Only specify special labels; others fall back to a nice title.
function role_labels(): array {
    return [
        'owner'      => 'Chủ shop',
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'sales'      => 'Nhân viên bán hàng',
        'warehouse'  => 'Kho',
        'accountant' => 'Kế toán',
        'support'    => 'CSKH',
        'user'       => 'Khách',
    ];
}

// Translate role key to Vietnamese label
function role_vi(string $role): string {
    $labels = role_labels();
    if (isset($labels[$role])) return $labels[$role];
    // Fallback: Title Case from key
    return ucwords(str_replace('_', ' ', $role));
}

// Central role list for dropdowns and validation (role => Vietnamese label)
// Builds from ROLE_PERMISSIONS keys so newly added roles automatically appear
function role_options(): array {
    $labels = role_labels();
    $out = [];
    foreach (array_keys(ROLE_PERMISSIONS) as $role) {
        $out[$role] = $labels[$role] ?? ucwords(str_replace('_', ' ', $role));
    }
    return $out;
}
