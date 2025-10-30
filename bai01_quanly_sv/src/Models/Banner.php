<?php
namespace App\Models;

use App\Database;
use PDO;

class Banner
{
    public static function allActive(): array {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->query('SELECT * FROM banners WHERE active = 1 ORDER BY sort, id');
        return $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public static function all(): array {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->query('SELECT * FROM banners ORDER BY sort, id');
        return $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public static function find(int $id): ?array {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM banners WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function create(array $data): int {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('INSERT INTO banners (image, title, link, sort, active) VALUES (:i,:t,:l,:s,:a)');
        $stmt->execute([':i'=>$data['image'], ':t'=>$data['title'] ?? null, ':l'=>$data['link'] ?? null, ':s'=>$data['sort'] ?? 0, ':a'=>$data['active'] ?? 1]);
        return (int)$pdo->lastInsertId();
    }
    public static function update(int $id, array $data): bool {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('UPDATE banners SET image=:i, title=:t, link=:l, sort=:s, active=:a WHERE id=:id');
        return $stmt->execute([':i'=>$data['image'], ':t'=>$data['title'] ?? null, ':l'=>$data['link'] ?? null, ':s'=>$data['sort'] ?? 0, ':a'=>$data['active'] ?? 1, ':id'=>$id]);
    }
    public static function delete(int $id): bool {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('DELETE FROM banners WHERE id=:id');
        return $stmt->execute([':id'=>$id]);
    }
}

