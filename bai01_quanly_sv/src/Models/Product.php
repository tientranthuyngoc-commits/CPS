<?php
namespace App\Models;

use App\Database;
use PDO;

class Product
{
    public static function all(): array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
        return $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function byCategory(int $categoryId): array
    {
        $pdo = Database::getInstance()->pdo();
        $sql = 'SELECT p.* FROM products p 
                JOIN product_categories pc ON p.id = pc.product_id 
                WHERE pc.category_id = :cid 
                ORDER BY p.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cid' => $categoryId]);
        return $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public static function priceInfo(int $id): array
    {
        $pdo = Database::getInstance()->pdo();
        $base = self::find($id);
        if (!$base) return [];
        $today = date('Y-m-d');
        $sql = 'SELECT promo_price FROM promotions 
                WHERE product_id = :pid AND active = 1 
                  AND date(starts_at) <= :d 
                  AND (ends_at IS NULL OR date(ends_at) >= :d)
                ORDER BY id DESC LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':pid'=>$id, ':d'=>$today]);
        $promo = $stmt->fetchColumn();
        return [
            'price' => (int)$base['price'],
            'promo_price' => $promo !== false ? (int)$promo : null,
        ];
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image, sku, stock, status, brand_id) VALUES (:name,:desc,:price,:image,:sku,:stock,:status,:brand_id)');
        $stmt->execute([
            ':name' => trim($data['name'] ?? ''),
            ':desc' => $data['description'] ?? '',
            ':price' => (int)($data['price'] ?? 0),
            ':image' => $data['image'] ?? null,
            ':sku' => $data['sku'] ?? null,
            ':stock' => (int)($data['stock'] ?? 0),
            ':status' => $data['status'] ?? 'active',
            ':brand_id' => (int)($data['brand_id'] ?? 0)?:null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('UPDATE products SET name=:name, description=:desc, price=:price, image=:image, sku=:sku, stock=:stock, status=:status, brand_id=:brand_id WHERE id=:id');
        return $stmt->execute([
            ':name' => trim($data['name'] ?? ''),
            ':desc' => $data['description'] ?? '',
            ':price' => (int)($data['price'] ?? 0),
            ':image' => $data['image'] ?? null,
            ':sku' => $data['sku'] ?? null,
            ':stock' => (int)($data['stock'] ?? 0),
            ':status' => $data['status'] ?? 'active',
            ':brand_id' => (int)($data['brand_id'] ?? 0)?:null,
            ':id' => $id,
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }

    public static function featured(int $limit = 10): array
    {
        $pdo = Database::getInstance()->pdo();
        // Chiến lược lấy sản phẩm nổi bật: rating|newest (mặc định: rating)
        $strategy = getenv('FEATURED_STRATEGY') ?: '';
        $strategy = $strategy ? strtolower($strategy) : '';
        // Cho phép cấu hình qua file data/app_config.json nếu có
        if ($strategy === '') {
            $cfgPath = __DIR__ . '/../../data/app_config.json';
            if (is_readable($cfgPath)) {
                try {
                    $cfg = json_decode(file_get_contents($cfgPath), true);
                    if (isset($cfg['featured_strategy'])) $strategy = strtolower((string)$cfg['featured_strategy']);
                    $minReviews = isset($cfg['featured_min_reviews']) ? max(0,(int)$cfg['featured_min_reviews']) : 2;
                    $minRating  = isset($cfg['featured_min_rating'])  ? max(1,(int)$cfg['featured_min_rating'])  : 4;
                } catch (\Throwable $e) { /* ignore */ }
            }
        }
        if (empty($minReviews)) $minReviews = 2; if (empty($minRating)) $minRating = 4;

        if ($strategy === 'newest') {
            $stmt = $pdo->prepare('SELECT p.* FROM products p ORDER BY id DESC LIMIT :lim');
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        }

        // Mặc định: theo rating, fallback về newest nếu không đủ dữ liệu
        $sql = 'SELECT p.*, AVG(r.rating) AS avg_rating, COUNT(r.id) AS rating_count
                FROM products p
                LEFT JOIN ratings r ON r.product_id = p.id AND r.approved = 1
                GROUP BY p.id
                HAVING rating_count >= :minReviews AND avg_rating >= :minRating
                ORDER BY avg_rating DESC, rating_count DESC, p.id DESC
                LIMIT :lim';
        $st = $pdo->prepare($sql);
        $st->bindValue(':minReviews', $minReviews, \PDO::PARAM_INT);
        $st->bindValue(':minRating',  $minRating,  \PDO::PARAM_INT);
        $st->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        if (!empty($rows)) return $rows;

        $stmt = $pdo->prepare('SELECT p.* FROM products p ORDER BY id DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function getDeals(int $limit = 12): array
    {
        $pdo = Database::getInstance()->pdo();
        $today = date('Y-m-d');
        $sql = 'SELECT p.*, pr.promo_price 
                FROM promotions pr 
                JOIN products p ON p.id = pr.product_id 
                WHERE pr.active = 1 
                  AND date(pr.starts_at) <= :d 
                  AND (pr.ends_at IS NULL OR date(pr.ends_at) >= :d)
                ORDER BY pr.id DESC 
                LIMIT :lim';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':d', $today, \PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function filterByAttributes(array $attributeIds, ?int $categoryId = null): array
    {
        $pdo = Database::getInstance()->pdo();
        if (empty($attributeIds)) {
            if ($categoryId) return self::byCategory($categoryId);
            return self::all();
        }
        $placeholders = implode(',', array_fill(0, count($attributeIds), '?'));
        $sql = 'SELECT DISTINCT p.* FROM products p ' .
               'JOIN product_attributes pa ON pa.product_id = p.id ' .
               'WHERE pa.attribute_id IN (' . $placeholders . ')';
        $params = $attributeIds;
        if ($categoryId) {
            $sql .= ' AND EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = ?)';
            $params[] = $categoryId;
        }
        // ensure product has ALL attributes: count match = count attributeIds
        $sql .= ' GROUP BY p.id HAVING COUNT(DISTINCT pa.attribute_id) = ' . count($attributeIds) . ' ORDER BY p.id DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function allAttributeTypes(): array
    {
        $pdo = Database::getInstance()->pdo();
        $types = $pdo->query('SELECT * FROM attribute_types ORDER BY id')->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        foreach ($types as &$t) {
            $st = $pdo->prepare('SELECT * FROM attributes WHERE type_id = :id ORDER BY id');
            $st->execute([':id'=>$t['id']]);
            $t['attributes'] = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        }
        return $types;
    }

    public static function search(string $q, int $limit = 24): array
    {
        $pdo = Database::getInstance()->pdo();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE name LIKE :q ORDER BY id DESC LIMIT :lim');
        $stmt->bindValue(':q', '%'.$q.'%', \PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    // Generic fetch with filters + sorting + pagination
    public static function fetch(array $opts): array
    {
        $pdo = Database::getInstance()->pdo();
        $q = trim($opts['q'] ?? '');
        $attrIds = $opts['attrs'] ?? [];
        $categoryId = $opts['category_id'] ?? null;
        $brandId = $opts['brand_id'] ?? null;
        $order = strtolower($opts['order'] ?? 'newest');
        $limit = max(1, (int)($opts['limit'] ?? 12));
        $offset = max(0, (int)($opts['offset'] ?? 0));
        $minPrice = isset($opts['min_price']) ? max(0, (int)$opts['min_price']) : null;
        $maxPrice = isset($opts['max_price']) ? max(0, (int)$opts['max_price']) : null;
        $onlyPromo = !empty($opts['only_promo']);
        $ratingMin = isset($opts['rating_min']) ? (int)$opts['rating_min'] : null;

        $where = [];
        $params = [];
        $joins = [];

        if ($q !== '') {
            $where[] = '(p.name LIKE :q OR p.sku LIKE :q OR p.description LIKE :q)';
            $params[':q'] = "%$q%";
        }
        if (!empty($attrIds)) {
            $joins[] = 'JOIN product_attributes pa ON pa.product_id = p.id';
            $in = implode(',', array_fill(0, count($attrIds), '?'));
            $where[] = 'pa.attribute_id IN ('.$in.')';
            foreach ($attrIds as $aid) { $params[] = (int)$aid; }
        }
        if (!empty($categoryId)) {
            $joins[] = 'JOIN product_categories pc ON pc.product_id = p.id';
            $where[] = 'pc.category_id = ?';
            $params[] = (int)$categoryId;
        }
        if (!empty($brandId)) {
            $where[] = 'p.brand_id = ?';
            $params[] = (int)$brandId;
        }
        if ($minPrice !== null) { $where[] = 'p.price >= ?'; $params[] = $minPrice; }
        if ($maxPrice !== null && $maxPrice > 0) { $where[] = 'p.price <= ?'; $params[] = $maxPrice; }
        if ($onlyPromo) {
            $joins[] = 'JOIN promotions pr ON pr.product_id = p.id AND pr.active=1 AND date(pr.starts_at) <= date("now") AND (pr.ends_at IS NULL OR date(pr.ends_at) >= date("now"))';
        }
        $needAggregate = !empty($attrIds) || ($ratingMin !== null);
        if ($ratingMin !== null) {
            $joins[] = 'LEFT JOIN ratings r ON r.product_id = p.id AND r.approved = 1';
        }

        $whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';
        $joinSql  = $joins ? (' '.implode(' ', array_unique($joins)).' ') : ' ';

        switch ($order) {
            case 'price_asc':  $orderBy = 'ORDER BY p.price ASC, p.id DESC'; break;
            case 'price_desc': $orderBy = 'ORDER BY p.price DESC, p.id DESC'; break;
            default:           $orderBy = 'ORDER BY p.id DESC';
        }

        // count
        $countSql = 'SELECT COUNT(DISTINCT p.id) FROM products p'.$joinSql.$whereSql;
        $stc = $pdo->prepare($countSql);
        $stc->execute($params);
        $total = (int)($stc->fetchColumn() ?: 0);

        // list
        $select = $needAggregate ? 'p.*, AVG(r.rating) AS avg_rating' : 'p.*';
        $group  = $needAggregate ? ' GROUP BY p.id ' : ' ';
        $having = ($ratingMin !== null) ? ' HAVING AVG(r.rating) >= '.(int)$ratingMin.' ' : ' ';
        $listSql = 'SELECT DISTINCT '.$select.' FROM products p'.$joinSql.$whereSql.$group.$having.$orderBy.' LIMIT :limit OFFSET :offset';
        $st = $pdo->prepare($listSql);
        // bind numeric params again
        $i = 1; $named = [];
        foreach ($params as $k => $v) {
            if (is_string($k)) { $named[$k] = $v; } else { $st->bindValue($i++, $v, \PDO::PARAM_INT); }
        }
        foreach ($named as $k => $v) { $st->bindValue($k, $v, \PDO::PARAM_STR); }
        $st->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return ['total'=>$total, 'items'=>$rows];
    }
}
