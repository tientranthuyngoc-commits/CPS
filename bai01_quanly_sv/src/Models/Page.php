<?php
namespace App\Models;

use App\Database;
use PDO;

class Page
{
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->query('SELECT * FROM pages ORDER BY id DESC');
        return $st? $st->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('SELECT * FROM pages WHERE id=:id');
        $st->execute([':id'=>$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('SELECT * FROM pages WHERE slug=:s');
        $st->execute([':s'=>$slug]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('INSERT INTO pages (title, slug, content) VALUES (:t,:s,:c)');
        $st->execute([':t'=>$data['title'], ':s'=>$data['slug'] ?? null, ':c'=>$data['content'] ?? '']);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('UPDATE pages SET title=:t, slug=:s, content=:c WHERE id=:id');
        return $st->execute([':t'=>$data['title'], ':s'=>$data['slug'] ?? null, ':c'=>$data['content'] ?? '', ':id'=>$id]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('DELETE FROM pages WHERE id=:id');
        return $st->execute([':id'=>$id]);
    }
}

