<?php
namespace App\Services;

use PDO;

/**
 * LocalProductAI
 * --------------
 * "AI" nội bộ, không dùng API ngoài.
 *
 * - Tự huấn luyện = scan bảng products, build index TF-IDF, lưu ra JSON.
 * - Tự trả lời = đọc index, tìm sản phẩm liên quan câu hỏi, sinh reply tiếng Việt.
 *
 * Sử dụng thuần PHP, không phụ thuộc API hay thư viện AI bên ngoài.
 */
class LocalProductAI
{
    /**
     * Huấn luyện lại: đọc toàn bộ products, build index và lưu vào JSON.
     */
    public static function rebuildIndex(PDO $pdo, string $indexPath): void
    {
        $stmt = $pdo->query("
            SELECT p.id, p.name, p.description, p.price, p.stock, p.image, b.name AS brand
            FROM products p
            LEFT JOIN brands b ON b.id = p.brand_id
        ");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $docCount = 0;
        $df = [];     // document frequency: term => số doc chứa term
        $docs = [];   // docs[id] = ['freq' => [term => count]]

        foreach ($products as $p) {
            $docId = (string)$p['id'];
            $docCount++;

            $textParts = [];
            if (!empty($p['name']))        $textParts[] = $p['name'];
            if (!empty($p['brand']))       $textParts[] = $p['brand'];
            if (!empty($p['description'])) $textParts[] = $p['description'];

            $fullText = implode(' ', $textParts);
            $tokens = self::tokenize($fullText);
            if (empty($tokens)) {
                continue;
            }

            $freq = [];
            foreach ($tokens as $t) {
                $freq[$t] = ($freq[$t] ?? 0) + 1;
            }

            $docs[$docId] = ['freq' => $freq];

            $seen = [];
            foreach ($freq as $t => $_) {
                if (isset($seen[$t])) {
                    continue;
                }
                $seen[$t] = true;
                $df[$t] = ($df[$t] ?? 0) + 1;
            }
        }

        $index = [
            'docCount' => $docCount,
            'df'       => $df,
            'docs'     => $docs,
        ];

        $dir = dirname($indexPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        file_put_contents(
            $indexPath,
            json_encode($index, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    /**
     * Trả lời câu hỏi về sản phẩm.
     * @return array{text:string,products:array<int,array{id:int,name:string,price:int,stock:int,brand:string,image:string}>}
     */
    public static function answer(PDO $pdo, string $question, string $indexPath, int $limit = 5): array
    {
        if (!file_exists($indexPath)) {
            self::rebuildIndex($pdo, $indexPath);
        }

        $json = file_get_contents($indexPath);
        if (!$json) {
            return [
                'text' => 'Hiện tại tôi chưa có dữ liệu sản phẩm để tư vấn.',
                'products' => [],
            ];
        }

        $index = json_decode($json, true);
        if (!is_array($index) || empty($index['docCount']) || empty($index['docs'])) {
            return [
                'text' => 'Chỉ mục sản phẩm bị lỗi hoặc trống, hãy huấn luyện lại.',
                'products' => [],
            ];
        }

        $docCount = max(1, (int)($index['docCount'] ?? 1));
        $df   = $index['df']   ?? [];
        $docs = $index['docs'] ?? [];

        $qTokens = self::tokenize($question);
        if (empty($qTokens)) {
            return [
                'text' => 'Mình chưa hiểu câu hỏi của bạn, bạn có thể mô tả rõ sản phẩm cần tìm không?',
                'products' => [],
            ];
        }

        $qFreq = [];
        foreach ($qTokens as $t) {
            $qFreq[$t] = ($qFreq[$t] ?? 0) + 1;
        }

        $scores = [];
        foreach ($docs as $docId => $docData) {
            $freq = $docData['freq'] ?? [];
            $score = 0.0;
            foreach ($qFreq as $term => $qCount) {
                if (!isset($freq[$term])) {
                    continue;
                }
                $tf = $freq[$term];
                $docWithTerm = $df[$term] ?? 1;
                $idf = log($docCount / (1 + $docWithTerm));
                $score += (1 + log($tf)) * $idf;
            }
            if ($score > 0) {
                $scores[$docId] = $score;
            }
        }

        if (empty($scores)) {
            return [
                'text' => 'Mình không tìm thấy sản phẩm nào phù hợp với mô tả của bạn trong kho dữ liệu hiện tại.',
                'products' => [],
            ];
        }

        arsort($scores);
        $topIds = array_slice(array_keys($scores), 0, $limit);

        $placeholders = implode(',', array_fill(0, count($topIds), '?'));
        $stmt = $pdo->prepare("
            SELECT p.id, p.name, p.description, p.price, p.stock, p.image, b.name AS brand
            FROM products p
            LEFT JOIN brands b ON b.id = p.brand_id
            WHERE p.id IN ($placeholders)
        ");
        $stmt->execute($topIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $r) {
            $map[(string)$r['id']] = $r;
        }

        $productsOut = [];
        foreach ($topIds as $id) {
            if (!isset($map[(string)$id])) {
                continue;
            }
            $r = $map[(string)$id];
            $productsOut[] = [
                'id'    => (int)$r['id'],
                'name'  => (string)($r['name'] ?? ''),
                'price' => (int)($r['price'] ?? 0),
                'stock' => (int)($r['stock'] ?? 0),
                'brand' => (string)($r['brand'] ?? ''),
                'image' => (string)($r['image'] ?? ''),
            ];
        }

        $reply = self::buildAnswerText($question, $productsOut);

        return [
            'text' => $reply,
            'products' => $productsOut,
        ];
    }

    /**
     * Huấn luyện nhanh: gọi trực tiếp.
     */
    public static function trainNow(PDO $pdo, string $indexPath): void
    {
        self::rebuildIndex($pdo, $indexPath);
    }

    // ================== HÀM NỘI BỘ ==================

    /**
     * Tokenize tiếng Việt đơn giản (lowercase, bỏ ký hiệu).
     */
    protected static function tokenize(string $text): array
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9áàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệíìỉĩịóòỏõọôốồổỗộơớờởỡợúùủũụưứừửữựýỳỷỹỵđ\\s]+/u', ' ', $text);
        $text = preg_replace('/\\s+/u', ' ', $text);
        $text = trim($text);
        if ($text === '') {
            return [];
        }
        $tokens = preg_split('/\\s+/u', $text);
        return array_values(array_filter($tokens));
    }

    /**
     * Sinh câu trả lời gợi ý sản phẩm từ danh sách top.
     */
    protected static function buildAnswerText(string $question, array $products): string
    {
        if (empty($products)) {
            return 'Hiện mình không tìm thấy sản phẩm phù hợp với yêu cầu của bạn.';
        }

        $lines = [];
        $lines[] = 'Dựa trên câu hỏi của bạn, mình gợi ý một số sản phẩm như sau:';
        foreach ($products as $i => $p) {
            $stt = $i + 1;
            $name  = $p['name'];
            $price = number_format($p['price'], 0, ',', '.').' ₫';
            $stock = $p['stock'] > 0 ? "Còn khoảng {$p['stock']} trong kho" : "Có thể đang hết hàng";
            $brand = $p['brand'] ? "Thương hiệu: {$p['brand']}. " : '';
            $lines[] = "{$stt}. {$name} - {$price}. {$brand}{$stock}.";
        }
        $lines[] = 'Nếu bạn mô tả chi tiết hơn (ví dụ: ngân sách, mục đích sử dụng), mình có thể lọc sản phẩm chính xác hơn nữa.';

        return implode("\n", $lines);
    }
}
