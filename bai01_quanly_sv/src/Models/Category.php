<?php
namespace App\Models;

use App\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function idByName(string $name): ?int
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = :n');
        $stmt->execute([':n'=>$name]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int)$id : null;
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(string $name): int
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:n)');
        $stmt->execute([':n'=>$name]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, string $name): bool
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('UPDATE categories SET name=:n WHERE id=:id');
        return $stmt->execute([':n'=>$name, ':id'=>$id]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance()->pdo();
        // remove links first
        $pdo->prepare('DELETE FROM product_categories WHERE category_id = :id')->execute([':id'=>$id]);
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }
}
