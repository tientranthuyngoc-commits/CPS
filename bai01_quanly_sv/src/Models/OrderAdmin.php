<?php
namespace App\Models;

use App\Database;
use PDO;

class OrderAdmin
{
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->query('SELECT * FROM orders ORDER BY id DESC');
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $pdo = Database::getInstance()->pdo();
        $allowed = ['pending','confirmed','shipping','completed','cancelled'];
        $normalize = static function (string $s): string {
            $s = strtolower($s);
            if ($s === 'paid') return 'confirmed';
            if ($s === 'success' || $s === 'done') return 'completed';
            return $s;
        };
        $status = $normalize($status);
        if (!in_array($status, $allowed, true)) { return false; }

        $current = $pdo->prepare('SELECT status FROM orders WHERE id = :id');
        $current->execute([':id'=>$id]);
        $currentStatus = (string)$current->fetchColumn();
        if ($currentStatus === '') { return false; }
        $currentStatus = $normalize($currentStatus);

        $flow = [
            'pending'   => ['confirmed','cancelled'],
            'confirmed' => ['shipping','cancelled'],
            'shipping'  => ['completed','cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
        if (!isset($flow[$currentStatus])) {
            $stmt = $pdo->prepare('UPDATE orders SET status = :s WHERE id = :id');
            return $stmt->execute([':s'=>$status, ':id'=>$id]);
        }
        if (!in_array($status, $flow[$currentStatus] ?? [], true) && $status !== $currentStatus) {
            return false;
        }
        $stmt = $pdo->prepare('UPDATE orders SET status = :s WHERE id = :id');
        return $stmt->execute([':s'=>$status, ':id'=>$id]);
    }

    public static function items(int $orderId): array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :id');
        $stmt->execute([':id'=>$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
