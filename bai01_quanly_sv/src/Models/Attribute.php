<?php
namespace App\Models;

use App\Database;
use PDO;

class Attribute
{
    public static function all(?int $typeId = null): array {
        $pdo = Database::getInstance()->pdo();
        if ($typeId) {
            $stmt = $pdo->prepare('SELECT * FROM attributes WHERE type_id = :t ORDER BY id');
            $stmt->execute([':t'=>$typeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        $stmt = $pdo->query('SELECT * FROM attributes ORDER BY id');
        return $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public static function find(int $id): ?array {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM attributes WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function create(int $typeId, string $name): int {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('INSERT INTO attributes (type_id, name) VALUES (:t,:n)');
        $stmt->execute([':t'=>$typeId, ':n'=>$name]);
        return (int)$pdo->lastInsertId();
    }
    public static function update(int $id, int $typeId, string $name): bool {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('UPDATE attributes SET type_id=:t, name=:n WHERE id=:id');
        return $stmt->execute([':t'=>$typeId, ':n'=>$name, ':id'=>$id]);
    }
    public static function delete(int $id): bool {
        $pdo = Database::getInstance()->pdo();
        $pdo->prepare('DELETE FROM product_attributes WHERE attribute_id=:a')->execute([':a'=>$id]);
        $stmt = $pdo->prepare('DELETE FROM attributes WHERE id=:id');
        return $stmt->execute([':id'=>$id]);
    }
}

