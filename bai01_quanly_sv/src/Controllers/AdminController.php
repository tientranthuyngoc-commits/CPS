<?php
namespace App\Controllers;

class AdminController
{
    private function ensureSession(): void { if (session_status()===PHP_SESSION_NONE) session_start(); }
    private function requireAdmin(): void {
        $this->ensureSession();
        if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        if (($_SESSION['role'] ?? 'user') !== 'admin') { header('Location: index.php'); exit; }
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        $username = $_SESSION['username'] ?? 'user';

        // Simple stats
        $pdo = \App\Database::getInstance()->pdo();
        $stats = [
            'products' => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'orders'   => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'users'    => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'revenue'  => (int)$pdo->query('SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ("paid","completed","done","success")')->fetchColumn(),
        ];

        $recentOrders = $pdo->query('SELECT id, customer_name, total, status, created_at FROM orders ORDER BY id DESC LIMIT 5')->fetchAll(\PDO::FETCH_ASSOC);
        $recentProducts = $pdo->query('SELECT id, name, price, created_at FROM products ORDER BY id DESC LIMIT 8')->fetchAll(\PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/admin_dashboard.php';
    }

    public function products(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $list = $pdo->query('SELECT p.id, p.name, p.price, p.image, p.sku, p.stock, p.status, b.name AS brand, p.created_at FROM products p LEFT JOIN brands b ON p.brand_id=b.id ORDER BY p.id DESC')->fetchAll(\PDO::FETCH_ASSOC);
        require __DIR__ . '/../../views/admin/products_list.php';
    }

    public function productForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $product = $id ? \App\Models\Product::find($id) : null;
        $categories = \App\Models\Category::all();
        $selectedCategoryId = 0;
        $brands = \App\Models\Brand::all();
        // Tax categories for accountants
        $pdo = \App\Database::getInstance()->pdo();
        $taxCategories = $pdo->query('SELECT id, code, name FROM tax_categories ORDER BY code')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $selectedTaxCategoryId = (int)($product['tax_category_id'] ?? 0);
        if ($id && $product) {
            $stmt = $pdo->prepare('SELECT category_id FROM product_categories WHERE product_id = :pid');
            $stmt->execute([':pid'=>$id]);
            $selectedCategoryId = (int)($stmt->fetchColumn() ?: 0);
        }
        require __DIR__ . '/../../views/admin/product_form.php';
    }

    public function productSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (int)($_POST['price'] ?? 0),
            'image' => trim($_POST['image'] ?? ''),
            'sku'   => trim($_POST['sku'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'status'=> trim($_POST['status'] ?? 'active'),
        ];
        // Handle main image upload
        if (!empty($_FILES['image_upload']['name'])) {
            $dir = __DIR__ . '/../../public/uploads/products';
            $webBase = 'uploads/products'; if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $fn = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/','', $_FILES['image_upload']['name']);
            $dest = $dir . '/' . $fn;
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $dest)) { $data['image'] = $webBase . '/' . $fn; }
        }

        if ($id) { \App\Models\Product::update($id, $data); } else { $id = \App\Models\Product::create($data); }

        // link category
        $catId = (int)($_POST['category_id'] ?? 0);
        $pdo = \App\Database::getInstance()->pdo();
        $pdo->prepare('DELETE FROM product_categories WHERE product_id = :pid')->execute([':pid'=>$id]);
        if ($catId > 0) {
            $pdo->prepare('INSERT OR IGNORE INTO product_categories (product_id, category_id) VALUES (:pid, :cid)')
                ->execute([':pid'=>$id, ':cid'=>$catId]);
        }
        // link brand
        $brandId = (int)($_POST['brand_id'] ?? 0);
        if ($brandId>0) { $pdo->prepare('UPDATE products SET brand_id=:b WHERE id=:id')->execute([':b'=>$brandId, ':id'=>$id]); }
        
        // save tax category mapping on product record (simple field)
        $taxCatId = (int)($_POST['tax_category_id'] ?? 0);
        if ($id) { $pdo->prepare('UPDATE products SET tax_category_id=:t WHERE id=:id')->execute([':t'=>$taxCatId, ':id'=>$id]); }

        // handle gallery uploads (up to 3)
        for ($i=1; $i<=3; $i++) {
            $key = 'gallery'.$i;
            if (!empty($_FILES[$key]['name'])) {
                $dir = __DIR__ . '/../../public/uploads/products';
                $webBase = 'uploads/products'; if (!is_dir($dir)) @mkdir($dir, 0775, true);
                $fn = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9_.-]/','', $_FILES[$key]['name']);
                $dest = $dir . '/' . $fn;
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $dest)) {
                    $pdo->prepare('INSERT INTO product_images (product_id, image, sort) VALUES (:p,:img,:s)')
                        ->execute([':p'=>$id, ':img'=>$webBase . '/' . $fn, ':s'=>$i]);
                }
            }
        }
        header('Location: index.php?action=admin_products');
    }

    // Brands CRUD
    public function brands(): void
    {
        $this->requireAdmin();
        $list = \App\Models\Brand::all();
        require __DIR__ . '/../../views/admin/brands_list.php';
    }
    public function brandForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $item = $id ? \App\Models\Brand::find($id) : null;
        require __DIR__ . '/../../views/admin/brand_form.php';
    }
    public function brandSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0); $name = trim($_POST['name'] ?? ''); $slug = trim($_POST['slug'] ?? '');
        if ($name!=='') { if ($id) \App\Models\Brand::update($id,$name,$slug); else \App\Models\Brand::create($name,$slug); }
        header('Location: index.php?action=admin_brands');
    }
    public function brandDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); if ($id) \App\Models\Brand::delete($id);
        header('Location: index.php?action=admin_brands');
    }

    public function productDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id) { \App\Models\Product::delete($id); }
        header('Location: index.php?action=admin_products');
    }

    public function orders(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $status = trim($_GET['status'] ?? '');
        $q = trim($_GET['q'] ?? '');
        $pay = trim($_GET['payment_status'] ?? '');
        $where = [];$params=[];
        if ($status !== '') { $where[] = 'status = :s'; $params[':s']=$status; }
        if ($pay !== '') { $where[] = 'payment_status = :ps'; $params[':ps']=$pay; }
        if ($q !== '') { $where[] = '(customer_name LIKE :q OR phone LIKE :q OR address LIKE :q)'; $params[':q'] = "%$q%"; }
        $sql = 'SELECT * FROM orders';
        if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
        $sql .= ' ORDER BY id DESC';
        $st = $pdo->prepare($sql); $st->execute($params);
        $list = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/admin/orders_list.php';
    }

    public function orderPaymentStatus(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $ps = trim($_POST['payment_status'] ?? 'unpaid');
        $pdo = \App\Database::getInstance()->pdo();
        if ($id) { $pdo->prepare('UPDATE orders SET payment_status=:ps WHERE id=:id')->execute([':ps'=>$ps, ':id'=>$id]); }
        header('Location: index.php?action=admin_orders');
        exit;
    }

    public function orderStatus(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'pending');
        if ($id) { \App\Models\OrderAdmin::updateStatus($id, $status); }
        header('Location: index.php?action=admin_orders');
        exit;
    }

    public function orderDetail(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            echo "<div class='alert alert-warning'><h4>Không tìm thấy đơn hàng</h4><p>ID đơn hàng không hợp lệ.</p><p><a href='index.php?action=admin_orders'>Quay lại</a></p></div>";
            return;
        }
        
        try {
            $pdo = \App\Database::getInstance()->pdo();
            $order = null; 
            $items = [];
            
            $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
            $stmt->execute([':id'=>$id]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($order) {
                $stmtItems = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :id');
                $stmtItems->execute([':id'=>$id]);
                $items = $stmtItems->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            }
            
            // Ensure variables exist for view
            if (!isset($order)) $order = null;
            if (!isset($items)) $items = [];
            
            require __DIR__ . '/../../views/admin/order_detail.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "<div style='padding:20px; font-family:Arial;'><h4>Có lỗi xảy ra</h4><p>".htmlspecialchars($e->getMessage())."</p><p><a href='index.php?action=admin_orders'>Quay lại danh sách đơn hàng</a></p></div>";
            error_log("Order detail error: ".$e->getMessage());
        }
    }

    public function orderPrint(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $pdo = \App\Database::getInstance()->pdo();
        $st = $pdo->prepare('SELECT * FROM orders WHERE id=:id'); $st->execute([':id'=>$id]);
        $order = $st->fetch(\PDO::FETCH_ASSOC);
        $it = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=:id');
        $it->execute([':id'=>$id]); $items = $it->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/invoice.php';
    }

    // ====== TAX ADMIN ======
    public function taxRates(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $rows = $pdo->query('SELECT * FROM tax_rates ORDER BY id DESC')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/admin/tax_rates.php';
    }

    public function taxRateForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $pdo = \App\Database::getInstance()->pdo();
        $rate = null;
        if ($id) { $st=$pdo->prepare('SELECT * FROM tax_rates WHERE id=:id'); $st->execute([':id'=>$id]); $rate=$st->fetch(\PDO::FETCH_ASSOC); }
        require __DIR__ . '/../../views/admin/tax_rate_form.php';
    }

    public function taxRateSave(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_POST['id'] ?? 0);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $rate = (float)($_POST['rate'] ?? 0);
        $type = in_array(strtolower($_POST['type'] ?? 'exclusive'), ['inclusive','exclusive']) ? strtolower($_POST['type']) : 'exclusive';
        $compound = (int)($_POST['compound'] ?? 0) ? 1:0;
        $active = (int)($_POST['active'] ?? 1) ? 1:0;
        if ($code !== '' && $name !== '') {
            if ($id) {
                $st = $pdo->prepare('UPDATE tax_rates SET code=:c,name=:n,rate=:r,type=:t,compound=:cp,active=:a WHERE id=:id');
                $st->execute([':c'=>$code,':n'=>$name,':r'=>$rate,':t'=>$type,':cp'=>$compound,':a'=>$active,':id'=>$id]);
            } else {
                $st = $pdo->prepare('INSERT INTO tax_rates(code,name,rate,type,compound,active) VALUES(:c,:n,:r,:t,:cp,:a)');
                $st->execute([':c'=>$code,':n'=>$name,':r'=>$rate,':t'=>$type,':cp'=>$compound,':a'=>$active]);
            }
        }
        header('Location: index.php?action=admin_tax_rates');
    }

    public function taxCategories(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $rows = $pdo->query('SELECT * FROM tax_categories ORDER BY id DESC')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/admin/tax_categories.php';
    }

    public function taxCategoryForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $pdo = \App\Database::getInstance()->pdo();
        $row = null;
        if ($id) { $st=$pdo->prepare('SELECT * FROM tax_categories WHERE id=:id'); $st->execute([':id'=>$id]); $row=$st->fetch(\PDO::FETCH_ASSOC); }
        require __DIR__ . '/../../views/admin/tax_category_form.php';
    }

    public function taxCategorySave(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_POST['id'] ?? 0);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        if ($code !== '' && $name !== '') {
            if ($id) { $st=$pdo->prepare('UPDATE tax_categories SET code=:c,name=:n WHERE id=:id'); $st->execute([':c'=>$code,':n'=>$name,':id'=>$id]); }
            else { $st=$pdo->prepare('INSERT INTO tax_categories(code,name) VALUES(:c,:n)'); $st->execute([':c'=>$code,':n'=>$name]); }
        }
        header('Location: index.php?action=admin_tax_categories');
    }

    public function taxMappings(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $cats = $pdo->query('SELECT * FROM tax_categories ORDER BY id DESC')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $rates = $pdo->query('SELECT * FROM tax_rates WHERE active=1 ORDER BY code')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        // current mappings
        $map = $pdo->query('SELECT tax_category_id, tax_rate_id FROM tax_rate_categories')->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        require __DIR__ . '/../../views/admin/tax_mappings.php';
    }

    public function taxMappingSave(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $cat = (int)($_POST['tax_category_id'] ?? 0);
        $rate = (int)($_POST['tax_rate_id'] ?? 0);
        if ($cat>0 && $rate>0) {
            $pdo->prepare('DELETE FROM tax_rate_categories WHERE tax_category_id=:c')->execute([':c'=>$cat]);
            $pdo->prepare('INSERT INTO tax_rate_categories(tax_rate_id,tax_category_id) VALUES(:r,:c)')->execute([':r'=>$rate,':c'=>$cat]);
        }
        header('Location: index.php?action=admin_tax_mappings');
    }

    public function reportTaxCsv(): void
    {
        $this->requireAdmin();
        $from = trim($_GET['from'] ?? '');
        $to   = trim($_GET['to'] ?? '');
        $pdo = \App\Database::getInstance()->pdo();
        $sql = 'SELECT * FROM tax_journal'; $params=[]; $where=[];
        if ($from !== '') { $where[] = 'date(created_at) >= date(:f)'; $params[':f']=$from; }
        if ($to !== '') { $where[] = 'date(created_at) <= date(:t)'; $params[':t']=$to; }
        if ($where) $sql .= ' WHERE '.implode(' AND ',$where);
        $sql .= ' ORDER BY id DESC';
        $st=$pdo->prepare($sql); $st->execute($params); $rows=$st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=tax_journal.csv');
        $out = fopen('php://output','w');
        fputcsv($out, ['id','order_id','order_item_id','tax_code','base_amount','tax_amount','rate','inclusive','created_at']);
        foreach ($rows as $r) fputcsv($out, $r);
        fclose($out); exit;
    }
    public function ordersExport(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $status = trim($_GET['status'] ?? '');
        $q = trim($_GET['q'] ?? '');
        $where = [];$params=[];
        if ($status !== '') { $where[] = 'status = :s'; $params[':s']=$status; }
        if ($q !== '') { $where[] = '(customer_name LIKE :q OR phone LIKE :q OR address LIKE :q)'; $params[':q'] = "%$q%"; }
        $sql = 'SELECT id, customer_name, phone, address, total, status, created_at FROM orders';
        if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
        $sql .= ' ORDER BY id DESC';
        $st = $pdo->prepare($sql); $st->execute($params);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Khách hàng','Điện thoại','Địa chỉ','Tổng','Trạng thái','Ngày']);
        foreach ($rows as $r) fputcsv($out, $r);
        fclose($out); exit;
    }

    public function categories(): void
    {
        $this->requireAdmin();
        $list = \App\Models\Category::all();
        require __DIR__ . '/../../views/admin/categories_list.php';
    }

    public function categoryForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $category = $id ? \App\Models\Category::find($id) : null;
        require __DIR__ . '/../../views/admin/category_form.php';
    }

    public function categorySave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            if ($id) { \App\Models\Category::update($id, $name); }
            else { \App\Models\Category::create($name); }
        }
        header('Location: index.php?action=admin_categories');
    }

    public function categoryDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id) { \App\Models\Category::delete($id); }
        header('Location: index.php?action=admin_categories');
    }

    // Promotions CRUD
    public function promotions(): void
    {
        $this->requireAdmin();
        $list = \App\Models\Promotion::all();
        require __DIR__ . '/../../views/admin/promotions_list.php';
    }

    public function promotionForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $promotion = $id ? \App\Models\Promotion::find($id) : null;
        // simple product list for selection
        $pdo = \App\Database::getInstance()->pdo();
        $products = $pdo->query('SELECT id, name, price FROM products ORDER BY name')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/admin/promotion_form.php';
    }

    public function promotionSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'product_id' => (int)($_POST['product_id'] ?? 0),
            'promo_price' => (int)($_POST['promo_price'] ?? 0),
            'starts_at' => trim($_POST['starts_at'] ?? ''),
            'ends_at' => trim($_POST['ends_at'] ?? ''),
            'active' => (int)($_POST['active'] ?? 1),
        ];
        if ($id) { \App\Models\Promotion::update($id, $data); } else { \App\Models\Promotion::create($data); }
        header('Location: index.php?action=admin_promotions');
    }

    public function promotionDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id) { \App\Models\Promotion::delete($id); }
        header('Location: index.php?action=admin_promotions');
    }

    // Coupons CRUD (mã giảm giá)
    public function coupons(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $list = $pdo->query('SELECT * FROM coupons ORDER BY id DESC')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/admin/coupons_list.php';
    }
    public function couponForm(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_GET['id'] ?? 0);
        $item = null; if ($id) { $st=$pdo->prepare('SELECT * FROM coupons WHERE id=:id'); $st->execute([':id'=>$id]); $item=$st->fetch(\PDO::FETCH_ASSOC); }
        require __DIR__ . '/../../views/admin/coupon_form.php';
    }
    public function couponSave(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_POST['id'] ?? 0);
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $type = ($_POST['type'] ?? 'percent') === 'fixed' ? 'fixed' : 'percent';
        $value = (int)($_POST['value'] ?? 0);
        $min = (int)($_POST['min_order'] ?? 0);
        $from = trim($_POST['valid_from'] ?? '');
        $to = trim($_POST['valid_to'] ?? '');
        $active = (int)($_POST['active'] ?? 1);
        if ($code !== '' && $value > 0) {
            if ($id) {
                $st = $pdo->prepare('UPDATE coupons SET code=:c, type=:t, value=:v, min_order=:m, valid_from=:f, valid_to=:o, active=:a WHERE id=:id');
                $st->execute([':c'=>$code, ':t'=>$type, ':v'=>$value, ':m'=>$min, ':f'=>$from?:null, ':o'=>$to?:null, ':a'=>$active, ':id'=>$id]);
            } else {
                $st = $pdo->prepare('INSERT INTO coupons (code, type, value, min_order, valid_from, valid_to, active) VALUES (:c,:t,:v,:m,:f,:o,:a)');
                $st->execute([':c'=>$code, ':t'=>$type, ':v'=>$value, ':m'=>$min, ':f'=>$from?:null, ':o'=>$to?:null, ':a'=>$active]);
            }
        }
        header('Location: index.php?action=admin_coupons');
    }
    public function couponDelete(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_GET['id'] ?? 0);
        if ($id) { $st=$pdo->prepare('DELETE FROM coupons WHERE id=:id'); $st->execute([':id'=>$id]); }
        header('Location: index.php?action=admin_coupons');
    }

    // Attribute types CRUD
    public function attrTypes(): void
    {
        $this->requireAdmin();
        $list = \App\Models\AttributeType::all();
        require __DIR__ . '/../../views/admin/attr_types_list.php';
    }
    public function attrTypeForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $item = $id ? \App\Models\AttributeType::find($id) : null;
        require __DIR__ . '/../../views/admin/attr_type_form.php';
    }
    public function attrTypeSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0); $name = trim($_POST['name'] ?? '');
        if ($name !== '') { if ($id) \App\Models\AttributeType::update($id, $name); else \App\Models\AttributeType::create($name); }
        header('Location: index.php?action=admin_attr_types');
    }
    public function attrTypeDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); if ($id) \App\Models\AttributeType::delete($id);
        header('Location: index.php?action=admin_attr_types');
    }

    // Attributes CRUD
    public function attrs(): void
    {
        $this->requireAdmin();
        $typeId = (int)($_GET['type_id'] ?? 0);
        $types = \App\Models\AttributeType::all();
        $list = \App\Models\Attribute::all($typeId ?: null);
        require __DIR__ . '/../../views/admin/attrs_list.php';
    }
    public function attrForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); $item = $id ? \App\Models\Attribute::find($id) : null; $types = \App\Models\AttributeType::all();
        require __DIR__ . '/../../views/admin/attr_form.php';
    }
    public function attrSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0); $typeId = (int)($_POST['type_id'] ?? 0); $name = trim($_POST['name'] ?? '');
        if ($name !== '' && $typeId>0) { if ($id) \App\Models\Attribute::update($id, $typeId, $name); else \App\Models\Attribute::create($typeId, $name); }
        header('Location: index.php?action=admin_attrs');
    }
    public function attrDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); if ($id) \App\Models\Attribute::delete($id);
        header('Location: index.php?action=admin_attrs');
    }

    // Assign attributes to product
    public function productAttrs(): void
    {
        $this->requireAdmin();
        $pid = (int)($_GET['id'] ?? 0);
        $pdo = \App\Database::getInstance()->pdo();
        $product = $pid ? \App\Models\Product::find($pid) : null;
        $types = \App\Models\Product::allAttributeTypes();
        $cur = [];
        if ($pid) {
            $st = $pdo->prepare('SELECT attribute_id FROM product_attributes WHERE product_id=:p');
            $st->execute([':p'=>$pid]);
            $cur = array_flip($st->fetchAll(\PDO::FETCH_COLUMN) ?: []);
        }
        require __DIR__ . '/../../views/admin/product_attributes_form.php';
    }
    public function productAttrsSave(): void
    {
        $this->requireAdmin();
        $pid = (int)($_POST['id'] ?? 0); $attrs = $_POST['attrs'] ?? [];
        $attrs = array_values(array_filter(array_map('intval', $attrs)));
        if ($pid) {
            $pdo = \App\Database::getInstance()->pdo();
            $pdo->prepare('DELETE FROM product_attributes WHERE product_id=:p')->execute([':p'=>$pid]);
            $ins = $pdo->prepare('INSERT OR IGNORE INTO product_attributes (product_id, attribute_id, value) VALUES (:p,:a,"")');
            foreach ($attrs as $a) { $ins->execute([':p'=>$pid, ':a'=>$a]); }
        }
        header('Location: index.php?action=admin_products');
    }

    // Banners CRUD
    public function banners(): void
    {
        $this->requireAdmin();
        $list = \App\Models\Banner::all();
        require __DIR__ . '/../../views/admin/banners_list.php';
    }
    public function bannerForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); $item = $id ? \App\Models\Banner::find($id) : null;
        require __DIR__ . '/../../views/admin/banner_form.php';
    }
    public function bannerSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? ''); $link = trim($_POST['link'] ?? '');
        $sort = (int)($_POST['sort'] ?? 0); $active = (int)($_POST['active'] ?? 1);
        $image = trim($_POST['image'] ?? '');
        if (!empty($_FILES['upload']['name'])) {
            $dir = __DIR__ . '/../../public/uploads/banners';
            $webBase = 'uploads/banners'; if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $fn = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/','', $_FILES['upload']['name']);
            $dest = $dir . '/' . $fn;
            if (move_uploaded_file($_FILES['upload']['tmp_name'], $dest)) { $image = $webBase . '/' . $fn; }
        }
        $data = ['image'=>$image, 'title'=>$title, 'link'=>$link, 'sort'=>$sort, 'active'=>$active];
        if ($id) { \App\Models\Banner::update($id, $data); } else { \App\Models\Banner::create($data); }
        header('Location: index.php?action=admin_banners');
    }
    public function bannerDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); if ($id) \App\Models\Banner::delete($id);
        header('Location: index.php?action=admin_banners');
    }

    // Posts (News/Promotions) CRUD
    public function posts(): void
    {
        $this->requireAdmin();
        $list = \App\Models\Post::all();
        require __DIR__ . '/../../views/admin/posts_list.php';
    }
    public function postForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $item = $id ? \App\Models\Post::find($id) : null;
        require __DIR__ . '/../../views/admin/post_form.php';
    }
    public function postSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? ''); $slug = trim($_POST['slug'] ?? ''); $cover = trim($_POST['cover'] ?? ''); $content = trim($_POST['content'] ?? '');
        if (!empty($_FILES['upload']['name'])) {
            $dir = __DIR__ . '/../../public/uploads/posts'; $webBase='uploads/posts'; if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $fn = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/','', $_FILES['upload']['name']); $dest=$dir.'/'.$fn;
            if (move_uploaded_file($_FILES['upload']['tmp_name'], $dest)) { $cover = $webBase.'/'.$fn; }
        }
        $data=['title'=>$title,'slug'=>$slug,'cover'=>$cover,'content'=>$content];
        if ($title!=='') { if ($id) \App\Models\Post::update($id,$data); else \App\Models\Post::create($data); }
        header('Location: index.php?action=admin_posts');
    }
    public function postDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); if ($id) \App\Models\Post::delete($id);
        header('Location: index.php?action=admin_posts');
    }

    // Pages (About/Policy) CRUD
    public function pages(): void
    {
        $this->requireAdmin();
        $list = \App\Models\Page::all();
        require __DIR__ . '/../../views/admin/pages_list.php';
    }
    public function pageForm(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $item = $id ? \App\Models\Page::find($id) : null;
        require __DIR__ . '/../../views/admin/page_form.php';
    }
    public function pageSave(): void
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? ''); $slug = trim($_POST['slug'] ?? ''); $content = trim($_POST['content'] ?? '');
        if ($title !== '') {
            $data = ['title'=>$title,'slug'=>$slug,'content'=>$content];
            if ($id) { \App\Models\Page::update($id, $data); } else { \App\Models\Page::create($data); }
        }
        header('Location: index.php?action=admin_pages');
    }
    public function pageDelete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0); if ($id) \App\Models\Page::delete($id);
        header('Location: index.php?action=admin_pages');
    }

    // Users management (accounts)
    public function users(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $list = $pdo->query('SELECT id, username, email, phone, role, is_active, created_at FROM users ORDER BY id DESC')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/admin/users_list.php';
    }

    public function userForm(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_GET['id'] ?? 0);
        $item = null;
        if ($id) {
            $st = $pdo->prepare('SELECT id, username, email, phone, role, is_active FROM users WHERE id=:id');
            $st->execute([':id'=>$id]);
            $item = $st->fetch(\PDO::FETCH_ASSOC);
        }
        require __DIR__ . '/../../views/admin/user_form.php';
    }

    public function userSave(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = in_array($_POST['role'] ?? 'user', ['admin','user']) ? $_POST['role'] : 'user';
        $isActive = !empty($_POST['is_active']) ? 1 : 0;
        $password = $_POST['password'] ?? '';
        if ($id) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $st = $pdo->prepare('UPDATE users SET username=:u, email=:e, phone=:p, role=:r, is_active=:a, password_hash=:h WHERE id=:id');
                $st->execute([':u'=>$username, ':e'=>$email, ':p'=>$phone, ':r'=>$role, ':a'=>$isActive, ':h'=>$hash, ':id'=>$id]);
            } else {
                $st = $pdo->prepare('UPDATE users SET username=:u, email=:e, phone=:p, role=:r, is_active=:a WHERE id=:id');
                $st->execute([':u'=>$username, ':e'=>$email, ':p'=>$phone, ':r'=>$role, ':a'=>$isActive, ':id'=>$id]);
            }
        } else {
            if ($username !== '' && $password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $st = $pdo->prepare('INSERT INTO users (username, email, phone, role, is_active, password_hash) VALUES (:u,:e,:p,:r,:a,:h)');
                $st->execute([':u'=>$username, ':e'=>$email, ':p'=>$phone, ':r'=>$role, ':a'=>$isActive, ':h'=>$hash]);
            }
        }
        header('Location: index.php?action=admin_users');
    }

    // Reports & Analytics
    public function reports(): void
    {
        $this->requireAdmin();
        try {
        $pdo = \App\Database::getInstance()->pdo();

        // Filters
        $from = trim($_GET['from'] ?? date('Y-m-01'));
        $to   = trim($_GET['to']   ?? date('Y-m-d'));
        // include whole day for 'to'
        $toEnd = $to . ' 23:59:59';
        $group = $_GET['group'] ?? 'day'; // day|month|year
        $fmt = ['day'=>'%Y-%m-%d', 'month'=>'%Y-%m', 'year'=>'%Y'][$group] ?? '%Y-%m-%d';

        // Revenue grouped
        $sqlRev = "SELECT strftime('".$fmt."', created_at) AS period,
                          SUM(CASE WHEN status IN ('paid','completed','done','success') THEN total ELSE 0 END) AS revenue,
                          COUNT(*) AS orders
                   FROM orders
                   WHERE created_at BETWEEN :from AND :to
                   GROUP BY period
                   ORDER BY period";
        $st = $pdo->prepare($sqlRev);
        $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $revenue = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Orders by status
        $sqlStatus = "SELECT status, COUNT(*) AS cnt
                      FROM orders WHERE created_at BETWEEN :from AND :to
                      GROUP BY status ORDER BY cnt DESC";
        $st = $pdo->prepare($sqlStatus); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $ordersByStatus = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Top selling products
        $sqlTop = "SELECT p.id, p.name, SUM(oi.quantity) AS qty, SUM(oi.quantity*oi.price) AS amount
                   FROM order_items oi
                   JOIN orders o ON o.id = oi.order_id
                   JOIN products p ON p.id = oi.product_id
                   WHERE o.created_at BETWEEN :from AND :to
                     AND o.status IN ('paid','completed','done','success')
                   GROUP BY p.id, p.name
                   ORDER BY qty DESC
                   LIMIT 10";
        $st = $pdo->prepare($sqlTop); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $topProducts = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Stock snapshot (low stock first)
        $stock = $pdo->query("SELECT id, name, sku, stock FROM products ORDER BY stock ASC, id DESC LIMIT 20")
                    ->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Top customers by phone/name (orders table has customer_name, phone)
        $sqlCust = "SELECT COALESCE(phone,'') AS phone, COALESCE(customer_name,'Khách lẻ') AS name,
                           COUNT(*) AS orders, SUM(total) AS spent
                    FROM orders
                    WHERE created_at BETWEEN :from AND :to
                      AND status IN ('paid','completed','done','success')
                    GROUP BY phone, name
                    ORDER BY spent DESC
                    LIMIT 10";
        $st = $pdo->prepare($sqlCust); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $topCustomers = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Doanh thu theo danh mục
        $sqlCat = "SELECT COALESCE(c.name,'(Không phân loại)') AS category_name,
                           SUM(oi.quantity) AS qty,
                           SUM(oi.quantity*oi.price) AS amount
                    FROM order_items oi
                    JOIN orders o ON o.id = oi.order_id
                    JOIN products p ON p.id = oi.product_id
                    LEFT JOIN product_categories pc ON pc.product_id = p.id
                    LEFT JOIN categories c ON c.id = pc.category_id
                    WHERE o.created_at BETWEEN :from AND :to
                      AND o.status IN ('paid','completed','done','success')
                    GROUP BY category_name
                    ORDER BY amount DESC
                    LIMIT 10";
        $st = $pdo->prepare($sqlCat); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $byCategory = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Doanh thu theo thương hiệu
        $sqlBrand = "SELECT COALESCE(b.name,'(Không có)') AS brand_name,
                            SUM(oi.quantity) AS qty,
                            SUM(oi.quantity*oi.price) AS amount
                     FROM order_items oi
                     JOIN orders o ON o.id = oi.order_id
                     JOIN products p ON p.id = oi.product_id
                     LEFT JOIN brands b ON b.id = p.brand_id
                     WHERE o.created_at BETWEEN :from AND :to
                       AND o.status IN ('paid','completed','done','success')
                     GROUP BY brand_name
                     ORDER BY amount DESC
                     LIMIT 10";
        $st = $pdo->prepare($sqlBrand); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $byBrand = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Phương thức thanh toán
        $sqlPay = "SELECT COALESCE(payment_method,'(khác)') AS method,
                          COUNT(*) AS cnt,
                          SUM(total) AS revenue
                   FROM orders
                   WHERE created_at BETWEEN :from AND :to
                   GROUP BY method
                   ORDER BY revenue DESC";
        $st = $pdo->prepare($sqlPay); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $byPayment = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Thuế (tổng hợp theo mã thuế)
        try {
            $sqlTax = "SELECT tax_code, SUM(base_amount) AS base, SUM(tax_amount) AS tax
                       FROM tax_journal
                       WHERE created_at BETWEEN :from AND :to
                       GROUP BY tax_code
                       ORDER BY tax DESC";
            $st = $pdo->prepare($sqlTax); $st->execute([':from'=>$from, ':to'=>$toEnd]);
            $taxSummary = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) { $taxSummary = []; }

        // Chiết khấu (giảm giá) theo kỳ
        $sqlDisc = "SELECT strftime('".$fmt."', created_at) AS period, SUM(COALESCE(discount_total,0)) AS discount
                    FROM orders
                    WHERE created_at BETWEEN :from AND :to
                    GROUP BY period
                    ORDER BY period";
        $st = $pdo->prepare($sqlDisc); $st->execute([':from'=>$from, ':to'=>$toEnd]);
        $discountSeries = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        require __DIR__ . '/../../views/admin/reports.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "<div class='alert alert-danger'><h4>Có lỗi xảy ra</h4><p>".htmlspecialchars($e->getMessage())."</p><p><a href='index.php?action=admin'>Về trang chủ</a></p></div>";
            error_log("Reports error: ".$e->getMessage());
        }
    }

    public function userDelete(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_GET['id'] ?? 0);
        if ($id>0) {
            $st = $pdo->prepare('DELETE FROM users WHERE id=:id');
            $st->execute([':id'=>$id]);
        }
        header('Location: index.php?action=admin_users');
    }

    public function userToggleActive(): void
    {
        $this->requireAdmin();
        $pdo = \App\Database::getInstance()->pdo();
        $id = (int)($_GET['id'] ?? 0); $active = (int)($_GET['a'] ?? 0);
        if ($id>0) {
            $st = $pdo->prepare('UPDATE users SET is_active=:a WHERE id=:id');
            $st->execute([':a'=>$active?1:0, ':id'=>$id]);
        }
        header('Location: index.php?action=admin_users');
    }

    public function customers(): void
    {
        $this->requireAdmin();
        $q = trim($_GET['q'] ?? '');
        $all = \App\Models\Customer::all();
        if ($q !== '') {
            $qLower = mb_strtolower($q, 'UTF-8');
            $list = array_values(array_filter($all, function($c) use ($qLower){
                $src = mb_strtolower(($c['customer_name'] ?? '').' '.($c['phone'] ?? '').' '.($c['address'] ?? ''), 'UTF-8');
                return strpos($src, $qLower) !== false;
            }));
        } else {
            $list = $all;
        }
        // Stats: total, new in 30 days, frequent (>=3 orders)
        $total = count($all);
        $now = time(); $days30 = 30*24*3600;
        $newCount = 0; $frequent = 0;
        foreach ($all as $c) {
            if (!empty($c['first_order']) && ($now - strtotime($c['first_order'])) <= $days30) $newCount++;
            if ((int)($c['orders_count'] ?? 0) >= 3) $frequent++;
        }
        $stats = ['total'=>$total,'new'=>$newCount,'frequent'=>$frequent];
        require __DIR__ . '/../../views/admin/customers_list.php';
    }

    public function customerDetail(): void
    {
        $this->requireAdmin();
        $phone = trim($_GET['phone'] ?? '');
        $orders = $phone !== '' ? \App\Models\Customer::ordersByPhone($phone) : [];
        $customer = null;
        if (!empty($orders)) {
            $last = $orders[0];
            $customer = [
                'customer_name' => $last['customer_name'] ?? '',
                'phone' => $last['phone'] ?? '',
                'address' => $last['address'] ?? ''
            ];
        }
        require __DIR__ . '/../../views/admin/customer_detail.php';
    }
}
