<?php
namespace App\Controllers;

use App\Models\Order;
use App\Database;

class CheckoutController
{
    private function ensure(): void { if (session_status()===PHP_SESSION_NONE) session_start(); }

    public function form(): void
    {
        $this->ensure();
        $items = array_values($_SESSION['cart'] ?? []);
        $addresses = [];
        $zones = [];
        if (!empty($_SESSION['user_id'])) {
            $pdo = Database::getInstance()->pdo();
            $st = $pdo->prepare('SELECT * FROM addresses WHERE user_id = :u ORDER BY is_default DESC, id DESC');
            $st->execute([':u'=>(int)$_SESSION['user_id']]);
            $addresses = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            // load shipping zones
            $zones = $pdo->query('SELECT * FROM shipping_zones ORDER BY id')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        }
        require __DIR__ . '/../../views/checkout.php';
    }

    public function checkoutFromProduct(): void
    {
        $this->ensure();
        $productId = (int)($_GET['id'] ?? 0);
        $quantity = (int)($_GET['qty'] ?? 1);
        
        if ($productId <= 0) {
            header('Location: index.php?action=home&error=invalid_product');
            exit;
        }
        
        try {
            $pdo = Database::getInstance()->pdo();
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$product) {
                header('Location: index.php?action=home&error=product_not_found');
                exit;
            }
            
            // Clear cart and add only this product
            $_SESSION['cart'] = [];
            $_SESSION['cart'][] = [
                'id' => $productId,
                'name' => $product['name'],
                'price' => (int)$product['price'],
                'image' => $product['image'] ?? '',
                'quantity' => max(1, $quantity)
            ];
            
            // Redirect to checkout
            header('Location: index.php?action=checkout');
            exit;
        } catch (\Throwable $e) {
            error_log("Checkout from product error: " . $e->getMessage());
            header('Location: index.php?action=product&id=' . $productId . '&error=1');
            exit;
        }
    }

    public function place(): void
    {
        $this->ensure();
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $shipping = trim($_POST['shipping_method'] ?? 'standard');
        $payment = trim($_POST['payment_method'] ?? 'cod');
        $couponCode = trim($_POST['coupon'] ?? '');
        $items = array_values($_SESSION['cart'] ?? []);
        if ($name === '' || $phone === '' || $address === '' || empty($items)) {
            header('Location: index.php?action=checkout&error=1'); exit;
        }
        // calculate totals
        $subtotal = 0; foreach ($items as $it) { $subtotal += (int)$it['price']*(int)$it['quantity']; }
        $tax = (int)round($subtotal*0.08);
        $ship = ($shipping==='express'?50000:($shipping==='pickup'?0:30000));
        $discount = 0;
        if ($couponCode !== '') {
            $pdo = \App\Database::getInstance()->pdo();
            $st = $pdo->prepare('SELECT * FROM coupons WHERE code = :c AND active = 1 AND (valid_from IS NULL OR date(valid_from) <= date("now")) AND (valid_to IS NULL OR date(valid_to) >= date("now"))');
            $st->execute([':c'=>$couponCode]);
            $cp = $st->fetch(\PDO::FETCH_ASSOC);
            if ($cp && $subtotal >= (int)($cp['min_order'] ?? 0)) {
                if (($cp['type'] ?? 'percent') === 'percent') $discount = (int)round($subtotal * ((int)$cp['value'] / 100));
                else $discount = (int)$cp['value'];
                if ($discount > $subtotal) $discount = $subtotal;
            }
        }
        // Tính thuế chi tiết theo cấu hình thuế
        try {
            $calc = new \App\Services\TaxCalculator();
            $tax = 0;
            foreach ($items as $it) {
                $rateRow = $calc->resolveRate($it['tax_category_id'] ?? null);
                $rate = (float)($rateRow['rate'] ?? 0);
                $type = (string)($rateRow['type'] ?? 'exclusive');
                $res = $calc->computeLine((int)$it['price'], (int)$it['quantity'], $rate, $type);
                $tax += (int)$res['tax'];
            }
        } catch (\Throwable $e) { /* fallback giữ nguyên $tax */ }

        $total = $subtotal + $tax + $ship - $discount;
        if ($total < 0) $total = 0;
        // Append info to address for demo storage
        $fullAddress = $address . " | Ship: $shipping | Pay: $payment" . ($couponCode?" | Coupon: $couponCode (-$discount)":"");
        $orderId = Order::create($name, $phone, $fullAddress, $items, $total);
        // Save payment/shipping fields
        $pdo = Database::getInstance()->pdo();
        $ps = 'unpaid'; if ($payment==='bank' || $payment==='wallet') $ps = 'pending';
        $up = $pdo->prepare('UPDATE orders SET payment_method=:pm, shipping_method=:sm, payment_status=:ps, shipping_fee=:sf, tax=:tx, tax_total=:tt, discount_total=:dc WHERE id=:id');
        $up->execute([':pm'=>$payment, ':sm'=>$shipping, ':ps'=>$ps, ':sf'=>$ship, ':tx'=>$tax, ':tt'=>$tax, ':dc'=>$discount, ':id'=>$orderId]);

        // Ghi journal thuế cho từng dòng
        try {
            $stItems = $pdo->prepare('SELECT id, product_id, price, quantity FROM order_items WHERE order_id=:o');
            $stItems->execute([':o'=>$orderId]);
            $rows = $stItems->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            foreach ($rows as $row) {
                // lấy tax_category của sản phẩm
                $tcId = (int)$pdo->query('SELECT tax_category_id FROM products WHERE id='.(int)$row['product_id'])->fetchColumn();
                $rateRow = $calc->resolveRate($tcId ?: null);
                $rate = (float)($rateRow['rate'] ?? 0);
                $code = (string)($rateRow['code'] ?? '');
                $type = (string)($rateRow['type'] ?? 'exclusive');
                $res = $calc->computeLine((int)$row['price'], (int)$row['quantity'], $rate, $type);
                $pdo->prepare('INSERT INTO tax_journal(order_id,order_item_id,tax_code,base_amount,tax_amount,rate,inclusive) VALUES (:o,:oi,:c,:b,:t,:r,:inc)')
                    ->execute([':o'=>$orderId, ':oi'=>$row['id'], ':c'=>$code, ':b'=>$res['base'], ':t'=>$res['tax'], ':r'=>$rate, ':inc'=> (strtolower($type)==='inclusive'?1:0)]);
                $pdo->prepare('UPDATE order_items SET tax_amount=:t, tax_rate=:r, tax_code=:c, tax_inclusive=:inc WHERE id=:id')
                    ->execute([':t'=>$res['tax'], ':r'=>$rate, ':c'=>$code, ':inc'=>(strtolower($type)==='inclusive'?1:0), ':id'=>$row['id']]);
            }
        } catch (\Throwable $e) { /* ignore journal errors */ }
        // Log email/SMS demo
        @file_put_contents(__DIR__.'/../../data/order_emails.log', date('c')." | $name | $phone | order#$orderId\n", FILE_APPEND);
        // Redirect to payment gateway when selected
        if ($payment === 'payos') {
            header('Location: index.php?action=payos_create&id=' . $orderId);
            exit;
        }
        if ($payment === 'momo') {
            header('Location: index.php?action=momo_create&id=' . $orderId);
            exit;
        }
        if ($payment === 'momo_demo') {
            header('Location: index.php?action=momo_demo&id=' . $orderId);
            exit;
        }
        if ($payment === 'bank_qr') {
            header('Location: index.php?action=bank_qr&id=' . $orderId);
            exit;
        }
        if ($payment === 'demo') {
            header('Location: index.php?action=demo_pay_start&id=' . $orderId);
            exit;
        }

        $_SESSION['cart'] = [];
        header('Location: index.php?action=success&id=' . $orderId);
        exit;
    }
}
