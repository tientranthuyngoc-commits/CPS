<?php
namespace App\Controllers;

use App\Models\Product;

class ProductController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = $id ? Product::find($id) : null;
        require __DIR__ . '/../../views/product_detail.php';
    }

    public function rate(): void
    {
        if (session_status()===PHP_SESSION_NONE) session_start();
        $pid = (int)($_POST['product_id'] ?? 0);
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 0)));
        $comment = trim($_POST['comment'] ?? '');
        if ($pid <= 0 || $rating < 1) { header('Location: index.php?action=product&id='.$pid); return; }
        $pdo = \App\Database::getInstance()->pdo();
        $uid = (int)($_SESSION['user_id'] ?? 0) ?: null;
        $st = $pdo->prepare('INSERT INTO ratings (product_id, user_id, rating, comment, approved) VALUES (:p,:u,:r,:c,1)');
        $st->execute([':p'=>$pid, ':u'=>$uid, ':r'=>$rating, ':c'=>$comment]);
        header('Location: index.php?action=product&id='.$pid.'#ratings');
    }
}
