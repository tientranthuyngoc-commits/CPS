<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use Throwable;

class ComplaintController
{
    public function showForm(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $productId = (int)($_GET['id'] ?? 0);
        if ($productId <= 0) {
            http_response_code(400);
            echo '<div class="alert alert-danger m-3">Sản phẩm không hợp lệ.</div>';
            return;
        }
        require __DIR__ . '/../../views/report_product.php';
    }

    public function submit(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($productId <= 0 || $title === '' || $content === '') {
            header('Location: index.php?action=report_product&id=' . $productId . '&error=missing_fields');
            exit;
        }
        try {
            $pdo = Database::getInstance()->pdo();
            $stmt = $pdo->prepare('INSERT INTO complaints (product_id, user_id, title, content, status, created_at) VALUES (:p,:u,:t,:c,"pending", datetime("now"))');
            $stmt->execute([
                ':p' => $productId,
                ':u' => (int)$_SESSION['user_id'],
                ':t' => $title,
                ':c' => $content,
            ]);
            header('Location: index.php?action=product&id=' . $productId . '&reported=1');
            exit;
        } catch (Throwable $e) {
            error_log('Complaint insert error: ' . $e->getMessage());
            header('Location: index.php?action=report_product&id=' . $productId . '&error=save_failed');
            exit;
        }
    }
}
