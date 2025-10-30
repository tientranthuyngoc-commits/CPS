<?php
declare(strict_types=1);

require __DIR__ . '/../src/Database.php';

use App\Database;

$pdo = Database::getInstance()->pdo();
$pdo->beginTransaction();

// Create types
$types = ['Màu sắc','RAM','ROM','Màn hình'];
$typeIds = [];
foreach ($types as $name) {
    $stmt = $pdo->prepare('INSERT INTO attribute_types (name) VALUES (:n)');
    try { $stmt->execute([':n'=>$name]); $typeIds[$name] = (int)$pdo->lastInsertId(); }
    catch (Throwable $e) {
        $q = $pdo->prepare('SELECT id FROM attribute_types WHERE name=:n');
        $q->execute([':n'=>$name]);
        $typeIds[$name] = (int)$q->fetchColumn();
    }
}

// Options
$options = [
    'Màu sắc' => ['Đen','Trắng','Xanh','Bạc','Vàng'],
    'RAM' => ['4GB','6GB','8GB','12GB'],
    'ROM' => ['64GB','128GB','256GB'],
    'Màn hình' => ['6.1"','6.5"','6.7"','6.8"']
];

foreach ($options as $typeName => $vals) {
    $tid = $typeIds[$typeName] ?? 0; if (!$tid) continue;
    foreach ($vals as $v) {
        try {
            $pdo->prepare('INSERT INTO attributes (type_id, name) VALUES (:t,:n)')->execute([':t'=>$tid, ':n'=>$v]);
        } catch (Throwable $e) { /* ignore dup */ }
    }
}

// Assign random attributes to products
$products = $pdo->query('SELECT id FROM products')->fetchAll(PDO::FETCH_COLUMN) ?: [];
foreach ($products as $pid) {
    // clear old
    $pdo->prepare('DELETE FROM product_attributes WHERE product_id=:p')->execute([':p'=>$pid]);
    // pick random values
    foreach ($options as $typeName => $vals) {
        $tid = $typeIds[$typeName] ?? 0; if (!$tid) continue;
        $attr = $pdo->prepare('SELECT id FROM attributes WHERE type_id=:t ORDER BY RANDOM() LIMIT 1');
        $attr->execute([':t'=>$tid]);
        $aid = (int)$attr->fetchColumn();
        if ($aid) {
            $pdo->prepare('INSERT OR IGNORE INTO product_attributes (product_id, attribute_id, value) VALUES (:p,:a,"")')
                ->execute([':p'=>$pid, ':a'=>$aid]);
        }
    }
}

// Seed gallery images (use product main image replicated if available)
$rows = $pdo->query('SELECT id, image FROM products')->fetchAll(PDO::FETCH_ASSOC) ?: [];
foreach ($rows as $r) {
    $pid = (int)$r['id']; $img = $r['image'] ?: 'assets/images/placeholder.svg';
    // clear old
    $pdo->prepare('DELETE FROM product_images WHERE product_id=:p')->execute([':p'=>$pid]);
    for ($i=0;$i<3;$i++) {
        $pdo->prepare('INSERT INTO product_images (product_id, image, sort) VALUES (:p,:img,:s)')
            ->execute([':p'=>$pid, ':img'=>$img, ':s'=>$i]);
    }
}

$pdo->commit();
echo "Seeded attribute types, attributes, assignments, and product galleries.\n";

