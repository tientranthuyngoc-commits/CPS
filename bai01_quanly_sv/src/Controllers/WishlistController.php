<?php
namespace App\Controllers;

use App\Database;
use App\Models\Product;
use PDO;

class WishlistController
{
    private function ensure(): void { if (session_status()===PHP_SESSION_NONE) session_start(); }
    private function pdo(): PDO { return Database::getInstance()->pdo(); }

    public function view(): void
    {
        $this->ensure();
        if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo();
        $st = $pdo->prepare('SELECT p.* FROM wishlists w JOIN products p ON p.id = w.product_id WHERE w.user_id = :u ORDER BY w.created_at DESC');
        $st->execute([':u'=>(int)$_SESSION['user_id']]);
        $items = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../../views/wishlist.php';
    }

    public function add(): void
    {
        $this->ensure();
        if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pid = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $redirect = $_GET['redirect'] ?? 'product';
        
        if ($pid > 0) {
            $pdo = $this->pdo();
            $st = $pdo->prepare('INSERT OR IGNORE INTO wishlists (user_id, product_id) VALUES (:u,:p)');
            $st->execute([':u'=>(int)$_SESSION['user_id'], ':p'=>$pid]);
        }
        
        if ($redirect === 'wishlist') {
            header('Location: index.php?action=wishlist');
        } else {
            header('Location: index.php?action=product&id=' . $pid);
        }
        exit;
    }

    public function remove(): void
    {
        $this->ensure();
        if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pid = (int)($_GET['id'] ?? 0);
        $redirect = $_GET['redirect'] ?? 'product';
        
        if ($pid > 0) {
            $pdo = $this->pdo();
            $st = $pdo->prepare('DELETE FROM wishlists WHERE user_id=:u AND product_id=:p');
            $st->execute([':u'=>(int)$_SESSION['user_id'], ':p'=>$pid]);
        }
        
        if ($redirect === 'wishlist') {
            header('Location: index.php?action=wishlist');
        } else {
            header('Location: index.php?action=product&id=' . $pid);
        }
        exit;
    }
}

