<?php
namespace App\Controllers;

use App\Models\Product;

class CartController
{
    private function ensure(): void { if (session_status()===PHP_SESSION_NONE) session_start(); if (!isset($_SESSION['cart'])) $_SESSION['cart']=[]; }

    public function add(): void
    {
        $this->ensure();
        if (!empty($_SESSION['user_id'])) {
            try {
                $pdo = \App\Database::getInstance()->pdo();
                $st = $pdo->prepare('SELECT is_active, block_reason FROM users WHERE id = :id');
                $st->execute([':id'=>(int)$_SESSION['user_id']]);
                $user = $st->fetch(\PDO::FETCH_ASSOC);
                if (!$user || (int)($user['is_active'] ?? 0) !== 1) {
                    $reason = trim((string)($user['block_reason'] ?? 'Tài khoản đã bị khóa.'));
                    $_SESSION = ['locked_message' => 'Tài khoản của bạn đã bị khóa. Lý do: ' . $reason];
                    header('Location: index.php?action=login&blocked=1&reason=' . urlencode($reason));
                    exit;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }
        $id = (int)($_POST['id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $p = $id ? Product::find($id) : null;
        if ($p) {
            $key = (string)$id;
            if (!isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key] = [
                    'id' => $id,
                    'name' => $p['name'],
                    'price' => (int)$p['price'],
                    'quantity' => 0,
                    'stock' => (int)($p['stock'] ?? 0),
                    'image' => $p['image'] ?? null,
                ];
            } else {
                $_SESSION['cart'][$key]['name'] = $p['name'];
                $_SESSION['cart'][$key]['price'] = (int)$p['price'];
            }
            $_SESSION['cart'][$key]['stock'] = (int)($p['stock'] ?? 0);
            if (!empty($p['image'])) {
                $_SESSION['cart'][$key]['image'] = $p['image'];
            }
            $_SESSION['cart'][$key]['quantity'] += $qty;
        }
        header('Location: index.php?action=cart');
        exit;
    }

    public function view(): void
    {
        $this->ensure();
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => &$entry) {
                $pid = (int)($entry['id'] ?? 0);
                if ($pid <= 0) {
                    $entry['stock'] = 0;
                    continue;
                }
                $product = Product::find($pid);
                if ($product) {
                    $entry['stock'] = (int)($product['stock'] ?? 0);
                    $entry['price'] = (int)($product['price'] ?? $entry['price'] ?? 0);
                    if (!empty($product['image'])) {
                        $entry['image'] = $product['image'];
                    }
                } else {
                    $entry['stock'] = 0;
                }
            }
            unset($entry);
        }
        $items = array_values($_SESSION['cart']);
        require __DIR__ . '/../../views/cart.php';
    }

    public function remove(): void
    {
        $this->ensure();
        $id = (int)($_GET['id'] ?? 0);
        unset($_SESSION['cart'][(string)$id]);
        header('Location: index.php?action=cart');
        exit;
    }

    public function update(): void
    {
        $this->ensure();
        $qtys = $_POST['qty'] ?? [];
        if (is_array($qtys)) {
            foreach ($qtys as $id => $q) {
                $id = (int)$id; $q = max(0, (int)$q);
                $key = (string)$id;
                if ($q === 0) { unset($_SESSION['cart'][$key]); }
                elseif (isset($_SESSION['cart'][$key])) { $_SESSION['cart'][$key]['quantity'] = $q; }
            }
        }
        header('Location: index.php?action=cart');
        exit;
    }
}
