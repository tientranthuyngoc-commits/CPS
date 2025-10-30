<?php
namespace App\Models;

use App\Database;
use PDO;

class Post
{
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->query('SELECT * FROM posts ORDER BY id DESC');
        return $st? $st->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public static function latest(int $limit = 4): array
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('SELECT * FROM posts ORDER BY id DESC LIMIT :lim');
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $st->execute([':id'=>$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('INSERT INTO posts (title, slug, cover, content) VALUES (:t,:s,:c,:ct)');
        $st->execute([':t'=>$data['title'], ':s'=>$data['slug'] ?? null, ':c'=>$data['cover'] ?? null, ':ct'=>$data['content'] ?? '']);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('UPDATE posts SET title=:t, slug=:s, cover=:c, content=:ct WHERE id=:id');
        return $st->execute([':t'=>$data['title'], ':s'=>$data['slug'] ?? null, ':c'=>$data['cover'] ?? null, ':ct'=>$data['content'] ?? '', ':id'=>$id]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance()->pdo();
        $st = $pdo->prepare('DELETE FROM posts WHERE id=:id');
        return $st->execute([':id'=>$id]);
    }
}
