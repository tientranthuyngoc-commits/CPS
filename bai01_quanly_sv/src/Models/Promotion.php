<?php
namespace App\Models;

use App\Database;
use PDO;

class Promotion
{
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $sql = 'SELECT pr.*, p.name AS product_name, p.price AS base_price 
                FROM promotions pr JOIN products p ON pr.product_id = p.id 
                ORDER BY pr.id DESC';
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM promotions WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('INSERT INTO promotions (product_id, promo_price, starts_at, ends_at, active) 
                               VALUES (:pid, :pp, :s, :e, :a)');
        $stmt->execute([
            ':pid'=>$data['product_id'],
            ':pp'=>$data['promo_price'],
            ':s'=>$data['starts_at'],
            ':e'=>$data['ends_at'] ?: null,
            ':a'=>$data['active'] ?? 1,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('UPDATE promotions SET product_id=:pid, promo_price=:pp, starts_at=:s, ends_at=:e, active=:a WHERE id=:id');
        return $stmt->execute([
            ':pid'=>$data['product_id'], ':pp'=>$data['promo_price'], ':s'=>$data['starts_at'], ':e'=>$data['ends_at'] ?: null, ':a'=>$data['active'] ?? 1, ':id'=>$id
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('DELETE FROM promotions WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }
}

