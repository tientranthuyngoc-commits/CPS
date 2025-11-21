<?php
namespace App\Services;

/**
 * AI indexer for products. Provides a stub fallback and an HTTP-based example.
 */
class AiProductIndexer
{
    public static function indexProduct(string $name, string $description): array
    {
        // If you want to skip real AI, uncomment the stub line below.
        // return self::stub($name, $description);

        $result = self::viaHttp($name, $description);
        if (empty($result)) {
            return self::stub($name, $description);
        }
        return $result;
    }

    private static function stub(string $name, string $description): array
    {
        $summary = mb_substr($description !== '' ? $description : $name, 0, 200, 'UTF-8');
        if (mb_strlen($description, 'UTF-8') > 200) {
            $summary .= '...';
        }
        $words = preg_split('/\s+/u', mb_strtolower($name, 'UTF-8'));
        $words = array_unique(array_filter($words));
        $keywords = implode(', ', $words);

        return [
            'summary' => $summary,
            'keywords' => $keywords,
            'raw' => [
                'type' => 'stub',
                'source' => 'simple_php',
            ],
        ];
    }

    private static function viaHttp(string $name, string $description): array
    {
        $prompt = "Bạn là trợ lý thương mại điện tử. Hãy phân tích thông tin sản phẩm sau và trả về JSON.\n"
            ."Tên sản phẩm: {$name}\nMô tả: {$description}\n\n"
            ."Yêu cầu:\n- \"summary\": tóm tắt ngắn gọn (1-2 câu, tiếng Việt).\n"
            ."- \"keywords\": chuỗi keywords ngăn cách bởi dấu phẩy, phục vụ tìm kiếm.\n"
            ."Trả về đúng JSON không thêm chữ nào khác.";

        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Bạn là trợ lý giúp index sản phẩm.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
        ];

        // Configure your AI endpoint and key here
        $apiUrl = 'http://localhost:11434/v1/chat/completions'; // Example for Ollama/OpenAI-compatible API
        $apiKey = ''; // Set if required

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array_filter([
                'Content-Type: application/json',
                $apiKey ? 'Authorization: Bearer '.$apiKey : null,
            ]),
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            return [];
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';
        $json = json_decode($content, true);
        if (!is_array($json)) {
            return [];
        }

        return [
            'summary' => $json['summary'] ?? null,
            'keywords' => $json['keywords'] ?? null,
            'raw' => $json,
        ];
    }
}
