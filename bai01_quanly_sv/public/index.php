<?php
declare(strict_types=1);
session_start();

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

// Composer autoload cho thư viện ngoài (PhpSpreadsheet, ...).
// Nếu thiếu vendor, không dừng hẳn site mà chỉ log cảnh báo (hạn chế 500).
$__auto = __DIR__ . '/../vendor/autoload.php';
if (is_readable($__auto)) {
    require $__auto;
} else {
    error_log('Warning: vendor/autoload.php not found. Run "composer install".');
}

require __DIR__ . '/../src/Database.php';

spl_autoload_register(function($class){
    $prefix = 'App\\';
    $base = __DIR__ . '/../src/';
    if (str_starts_with($class, $prefix)) {
        $rel = substr($class, strlen($prefix));
        $file = $base . str_replace('\\','/',$rel) . '.php';
        if (is_readable($file)) require $file;
    }
});

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\AccountController;
use App\Controllers\BrandController;
use App\Controllers\PostController;
use App\Controllers\WishlistController;
use App\Controllers\ComplaintController;

$locked = false;
if (!empty($_SESSION['user_id'])) {
    try {
        $pdoStatus = \App\Database::getInstance()->pdo();
        $chk = $pdoStatus->prepare('SELECT is_active, block_reason FROM users WHERE id = :id');
        $chk->execute([':id'=>(int)$_SESSION['user_id']]);
        $status = $chk->fetch(\PDO::FETCH_ASSOC);
        if (!$status || (int)($status['is_active'] ?? 0) !== 1) {
            $reason = trim((string)($status['block_reason'] ?? 'Tài khoản đã bị khóa.'));
            $_SESSION = ['locked_message' => 'Tài khoản của bạn đã bị khóa. Lý do: ' . $reason];
            header('Location: index.php?action=login&blocked=1&reason=' . urlencode($reason));
            exit;
        }
    } catch (\Throwable $e) {
        // ignore
    }
}

$action = $_GET['action'] ?? 'home';

try {
    switch ($action) {
        case 'home': (new HomeController())->index(); break;
        case 'search_suggest': (new HomeController())->suggest(); break;
        case 'product': (new ProductController())->show(); break;
        case 'product_rate': (new ProductController())->rate(); break;
        case 'cart': (new CartController())->view(); break;
        case 'add_to_cart': (new CartController())->add(); break;
        case 'update_cart': (new CartController())->update(); break;
        case 'remove_from_cart': (new CartController())->remove(); break;
        case 'checkout': (new CheckoutController())->form(); break;
        case 'checkout_from': (new CheckoutController())->checkoutFromProduct(); break;
        case 'place_order': (new CheckoutController())->place(); break;
        case 'payos_create': require __DIR__.'/payos_create.php'; break;
        case 'payos_return': require __DIR__.'/payos_return.php'; break;
        case 'payos_webhook': require __DIR__.'/payos_webhook.php'; break;
        case 'bank_qr': require __DIR__.'/bank_qr.php'; break;
        case 'momo_create': require __DIR__.'/momo_create.php'; break;
        case 'momo_return': require __DIR__.'/momo_return.php'; break;
        case 'momo_ipn': require __DIR__.'/momo_ipn.php'; break;
        case 'momo_demo': require __DIR__.'/momo_demo.php'; break;
        case 'demo_pay_start': require __DIR__.'/demo_pay_start.php'; break;
        case 'demo_pay_return': require __DIR__.'/demo_pay_return.php'; break;
        case 'success': $id = (int)($_GET['id'] ?? 0); require __DIR__.'/../views/success.php'; break;
        case 'login': (new AuthController())->login(); break;
        case 'login_submit': (new AuthController())->loginSubmit(); break;
        case 'logout': (new AuthController())->logout(); break;
        case 'oauth_google_start': require __DIR__ . '/oauth_google_start.php'; break;
        case 'oauth_google_callback': require __DIR__ . '/oauth_google_callback.php'; break;
        case 'oauth_facebook_start': require __DIR__ . '/oauth_facebook_start.php'; break;
        case 'oauth_facebook_callback': require __DIR__ . '/oauth_facebook_callback.php'; break;
        case 'register': (new AccountController())->register(); break;
        case 'register_submit': (new AccountController())->registerSubmit(); break;
        case 'forgot': (new AccountController())->forgot(); break;
        case 'forgot_submit': (new AccountController())->forgotSubmit(); break;
        case 'reset': (new AccountController())->reset(); break;
        case 'reset_submit': (new AccountController())->resetSubmit(); break;
        case 'account': (new AccountController())->profile(); break;
        case 'account_addresses': (new AccountController())->addresses(); break;
        case 'account_orders': (new AccountController())->orders(); break;
        case 'account_order_detail': (new AccountController())->orderDetail(); break;
        case 'account_order_print': (new AccountController())->orderPrint(); break;
        case 'account_order_return': (new AccountController())->orderReturn(); break;
        case 'account_change_password': (new AccountController())->changePassword(); break;
        case 'account_change_password_submit': (new AccountController())->changePasswordSubmit(); break;
        case 'account_avatar_upload': (new AccountController())->uploadAvatar(); break;
        case 'admin': (new AdminController())->dashboard(); break;
        case 'admin_products': (new AdminController())->products(); break;
        case 'admin_product_form': (new AdminController())->productForm(); break;
        case 'admin_product_save': (new AdminController())->productSave(); break;
        case 'admin_product_delete': (new AdminController())->productDelete(); break;
        case 'admin_product_excel': (new AdminController())->productExcelImport(); break;
        case 'admin_product_ai_demo': (new AdminController())->productAiDemo(); break;
        case 'admin_orders': (new AdminController())->orders(); break;
        case 'admin_order_detail': (new AdminController())->orderDetail(); break;
        case 'admin_order_print': (new AdminController())->orderPrint(); break;
        case 'admin_orders_export': (new AdminController())->ordersExport(); break;
        case 'admin_tax_rates': (new AdminController())->taxRates(); break;
        case 'admin_tax_rate_form': (new AdminController())->taxRateForm(); break;
        case 'admin_tax_rate_save': (new AdminController())->taxRateSave(); break;
        case 'admin_tax_categories': (new AdminController())->taxCategories(); break;
        case 'admin_tax_category_form': (new AdminController())->taxCategoryForm(); break;
        case 'admin_tax_category_save': (new AdminController())->taxCategorySave(); break;
        case 'admin_tax_mappings': (new AdminController())->taxMappings(); break;
        case 'admin_tax_mapping_save': (new AdminController())->taxMappingSave(); break;
        case 'admin_report_tax': (new AdminController())->reportTaxCsv(); break;
        case 'admin_order_payment': (new AdminController())->orderPaymentStatus(); break;
        case 'admin_order_status': (new AdminController())->orderStatus(); break;
        case 'admin_categories': (new AdminController())->categories(); break;
        case 'admin_category_form': (new AdminController())->categoryForm(); break;
        case 'admin_category_save': (new AdminController())->categorySave(); break;
        case 'admin_category_delete': (new AdminController())->categoryDelete(); break;
        case 'admin_promotions': (new AdminController())->promotions(); break;
        case 'admin_promotion_form': (new AdminController())->promotionForm(); break;
        case 'admin_promotion_save': (new AdminController())->promotionSave(); break;
        case 'admin_promotion_delete': (new AdminController())->promotionDelete(); break;
        case 'admin_coupons': (new AdminController())->coupons(); break;
        case 'admin_coupon_form': (new AdminController())->couponForm(); break;
        case 'admin_coupon_save': (new AdminController())->couponSave(); break;
        case 'admin_coupon_delete': (new AdminController())->couponDelete(); break;
        case 'admin_brands': (new AdminController())->brands(); break;
        case 'admin_brand_form': (new AdminController())->brandForm(); break;
        case 'admin_brand_save': (new AdminController())->brandSave(); break;
        case 'admin_brand_delete': (new AdminController())->brandDelete(); break;
        case 'admin_attr_types': (new AdminController())->attrTypes(); break;
        case 'admin_attr_type_form': (new AdminController())->attrTypeForm(); break;
        case 'admin_attr_type_save': (new AdminController())->attrTypeSave(); break;
        case 'admin_attr_type_delete': (new AdminController())->attrTypeDelete(); break;
        case 'admin_attrs': (new AdminController())->attrs(); break;
        case 'admin_attr_form': (new AdminController())->attrForm(); break;
        case 'admin_attr_save': (new AdminController())->attrSave(); break;
        case 'admin_attr_delete': (new AdminController())->attrDelete(); break;
        case 'admin_product_attrs': (new AdminController())->productAttrs(); break;
        case 'admin_product_attrs_save': (new AdminController())->productAttrsSave(); break;
        case 'admin_banners': (new AdminController())->banners(); break;
        case 'admin_banner_form': (new AdminController())->bannerForm(); break;
        case 'admin_banner_save': (new AdminController())->bannerSave(); break;
        case 'admin_banner_delete': (new AdminController())->bannerDelete(); break;
        case 'admin_posts': (new AdminController())->posts(); break;
        case 'admin_post_form': (new AdminController())->postForm(); break;
        case 'admin_post_save': (new AdminController())->postSave(); break;
        case 'admin_post_delete': (new AdminController())->postDelete(); break;
        case 'admin_pages': (new AdminController())->pages(); break;
        case 'admin_page_form': (new AdminController())->pageForm(); break;
        case 'admin_page_save': (new AdminController())->pageSave(); break;
        case 'admin_page_delete': (new AdminController())->pageDelete(); break;
        case 'admin_returns': (new AdminController())->returnsList(); break;
        case 'admin_returns_update': (new AdminController())->returnsUpdate(); break;
        case 'admin_customers': (new AdminController())->customers(); break;
        case 'admin_customer_detail': (new AdminController())->customerDetail(); break;
        case 'brand': (new BrandController())->index(); break;
        case 'post': (new PostController())->show(); break;
        case 'page': $slug = trim($_GET['slug'] ?? ''); $page = $slug? \App\Models\Page::findBySlug($slug) : null; require __DIR__ . '/../views/page.php'; break;
        case 'wishlist': (new WishlistController())->view(); break;
        case 'wishlist_add': (new WishlistController())->add(); break;
        case 'wishlist_remove': (new WishlistController())->remove(); break;
        case 'report_product': (new ComplaintController())->showForm(); break;
        case 'submit_report': (new ComplaintController())->submit(); break;
        case 'admin_users': (new AdminController())->users(); break;
        case 'admin_user_form': (new AdminController())->userForm(); break;
        case 'admin_user_save': (new AdminController())->userSave(); break;
        case 'admin_user_delete': (new AdminController())->userDelete(); break;
        case 'admin_user_toggle': (new AdminController())->userToggleActive(); break;
        case 'admin_user_lock_form': (new AdminController())->userLockForm(); break;
        case 'admin_user_lock_save': (new AdminController())->userLockSave(); break;
        case 'admin_reports': (new AdminController())->reports(); break;
        default: http_response_code(404); require __DIR__.'/../views/error_404.php';
    }
} catch (Throwable $e) {
    // Ghi log chi tiết để dễ debug thay vì lỗi 500 mù mịt
    error_log('APP ERROR: '.$e->getMessage().' @ '.$e->getFile().':'.$e->getLine());
    http_response_code(500);
    require __DIR__.'/../views/error_500.php';
}
?>
