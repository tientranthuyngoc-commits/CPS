<?php
namespace App\Models;

use App\Database;
use PDO;

class Order
{
    public static function create(string $name, string $phone, string $address, array $items, ?int $overrideTotal = null): int
    {
        $pdo = Database::getInstance()->pdo();
        $pdo->beginTransaction();
        try {
            $total = 0;
            foreach ($items as $it) { $total += (int)$it['price'] * (int)$it['quantity']; }
            if ($overrideTotal !== null) { $total = (int)$overrideTotal; }

            $stmt = $pdo->prepare('INSERT INTO orders (customer_name, phone, address, total) VALUES (:n,:p,:a,:t)');
            $stmt->execute([':n'=>$name, ':p'=>$phone, ':a'=>$address, ':t'=>$total]);
            $orderId = (int)$pdo->lastInsertId();

            $ins = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:o,:pid,:q,:pr)');
            foreach ($items as $it) {
                $ins->execute([':o'=>$orderId, ':pid'=>(int)$it['id'], ':q'=>(int)$it['quantity'], ':pr'=>(int)$it['price']]);
            }
            $pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}

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
