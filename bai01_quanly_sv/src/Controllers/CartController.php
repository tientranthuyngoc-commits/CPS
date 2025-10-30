<?php
namespace App\Controllers;

use App\Models\Product;

class CartController
{
    private function ensure(): void { if (session_status()===PHP_SESSION_NONE) session_start(); if (!isset($_SESSION['cart'])) $_SESSION['cart']=[]; }

    public function add(): void
    {
        $this->ensure();
        $id = (int)($_POST['id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $p = $id ? Product::find($id) : null;
        if ($p) {
            $key = (string)$id;
            if (!isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key] = ['id'=>$id,'name'=>$p['name'],'price'=>(int)$p['price'],'quantity'=>0];
            }
            $_SESSION['cart'][$key]['quantity'] += $qty;
        }
        header('Location: index.php?action=cart');
        exit;
    }

    public function view(): void
    {
        $this->ensure();
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
