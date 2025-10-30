<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Post;

class HomeController
{
    public function index(): void
    {
        $catId = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
        // Accept both attrs[]=.. and attr[]=.. or CSV string "attrs=1,2"
        $attrInput = $_GET['attrs'] ?? ($_GET['attr'] ?? null);
        $attrIds = [];
        if (is_array($attrInput)) {
            $attrIds = array_values(array_filter(array_map('intval', $attrInput)));
        } elseif (is_string($attrInput) && trim($attrInput) !== '') {
            $attrIds = array_values(array_filter(array_map('intval', explode(',', $attrInput))));
        }
        $q = trim($_GET['q'] ?? '');
        $minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;
        $onlyPromo = !empty($_GET['promo']);
        $ratingMin = isset($_GET['rating_min']) ? (int)$_GET['rating_min'] : null;
        // Sorting + pagination
        $sort = $_GET['sort'] ?? 'newest';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = max(6, (int)($_GET['limit'] ?? 12));
        $offset = ($page-1)*$limit;

        // Unified fetch
        $res = Product::fetch([
            'q' => $q,
            'attrs' => $attrIds,
            'category_id' => $catId ?: null,
            'order' => $sort,
            'limit' => $limit,
            'offset' => $offset,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'only_promo' => $onlyPromo,
            'rating_min' => $ratingMin,
        ]);
        $products = $res['items'];
        $total = (int)$res['total'];
        $pages = (int)ceil($total / $limit);
        $featured = Product::featured(10);
        $attributeTypes = Product::allAttributeTypes();
        $searchQuery = $q;
        $paging = ['total'=>$total,'pages'=>$pages,'page'=>$page,'limit'=>$limit,'sort'=>$sort,'min_price'=>$minPrice,'max_price'=>$maxPrice,'promo'=>$onlyPromo,'rating_min'=>$ratingMin];
        // Tin tức & danh mục nổi bật
        $latestPosts = Post::latest(4);
        $categoryList = Category::all();
        require __DIR__ . '/../../views/home.php';
    }

    // API gợi ý tìm kiếm kiểu TGDD (JSON)
// API gợi ý tìm kiếm kiểu TGDD (JSON)
public function suggest(): void
{
    header('Content-Type: application/json; charset=utf-8');
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode([]);
        return;
    }

    $pdo = \App\Database::getInstance()->pdo();

    // ✅ Chỉ tìm tên sản phẩm bắt đầu bằng ký tự đầu tiên người dùng nhập
    $stmt = $pdo->prepare('
        SELECT id, name, price 
        FROM products 
        WHERE name LIKE CONCAT(:q, "%") 
           OR sku LIKE CONCAT(:q, "%")
        ORDER BY id DESC 
        LIMIT 8
    ');
    $stmt->execute([':q' => $q]);
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

    // ✅ Fallback tìm không phân biệt dấu (accent-insensitive)
    if (empty($rows)) {
        $sample = $pdo->query('
            SELECT id, name, price 
            FROM products 
            ORDER BY id DESC 
            LIMIT 100
        ')->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $norm = function (string $s): string {
            $s = mb_strtolower($s, 'UTF-8');
            $repl = [
                'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
                'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
                'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
                'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
                'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
                'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y','đ'=>'d'
            ];
            return strtr($s, $repl);
        };

        $nq = $norm($q);
        $tmp = [];
        foreach ($sample as $row) {
            $name = $norm($row['name'] ?? '');
            // ✅ Chỉ khớp khi ký tự đầu tiên trùng (bắt đầu bằng q)
            if (mb_substr($name, 0, mb_strlen($nq)) === $nq) {
                $tmp[] = $row;
                if (count($tmp) >= 8) break;
            }
        }

        $rows = !empty($tmp) ? $tmp : (
            $pdo->query('SELECT id, name, price FROM products ORDER BY id DESC LIMIT 8')->fetchAll(\PDO::FETCH_ASSOC) ?: []
        );
    }

    echo json_encode($rows);
}

} 
