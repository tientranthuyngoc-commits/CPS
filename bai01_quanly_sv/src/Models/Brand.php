<?php
namespace App\Models;

use App\Database;
use PDO;

class Brand
{
    public static function all(): array {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->query('SELECT * FROM brands ORDER BY name');
        return $st? $st->fetchAll(PDO::FETCH_ASSOC):[];
    }
    public static function find(int $id): ?array {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('SELECT * FROM brands WHERE id=:id');
        $st->execute([':id'=>$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row?:null;
    }
    public static function create(string $name, string $slug=''): int {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('INSERT INTO brands (name, slug) VALUES (:n,:s)');
        $st->execute([':n'=>$name, ':s'=>$slug]); return (int)$pdo->lastInsertId();
    }
    public static function update(int $id, string $name, string $slug=''): bool {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('UPDATE brands SET name=:n, slug=:s WHERE id=:id');
        return $st->execute([':n'=>$name, ':s'=>$slug, ':id'=>$id]);
    }
    public static function delete(int $id): bool {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('DELETE FROM brands WHERE id=:id');
        return $st->execute([':id'=>$id]);
    }
}

