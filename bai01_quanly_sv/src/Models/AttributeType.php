<?php
namespace App\Models;

use App\Database;
use PDO;

class AttributeType
{
    public static function all(): array {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->query('SELECT * FROM attribute_types ORDER BY id');
        return $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public static function find(int $id): ?array {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM attribute_types WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function create(string $name): int {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('INSERT INTO attribute_types (name) VALUES (:n)');
        $stmt->execute([':n'=>$name]);
        return (int)$pdo->lastInsertId();
    }
    public static function update(int $id, string $name): bool {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('UPDATE attribute_types SET name=:n WHERE id=:id');
        return $stmt->execute([':n'=>$name, ':id'=>$id]);
    }
    public static function delete(int $id): bool {
        $pdo = Database::getInstance()->pdo();
        // cascade delete attributes
        $attrs = $pdo->prepare('SELECT id FROM attributes WHERE type_id=:t');
        $attrs->execute([':t'=>$id]);
        $ids = $attrs->fetchAll(PDO::FETCH_COLUMN) ?: [];
        if (!empty($ids)) {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare('DELETE FROM product_attributes WHERE attribute_id IN ('.$in.')')->execute($ids);
            $pdo->prepare('DELETE FROM attributes WHERE id IN ('.$in.')')->execute($ids);
        }
        $stmt = $pdo->prepare('DELETE FROM attribute_types WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }
}

