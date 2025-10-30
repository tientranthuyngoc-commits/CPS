<?php
namespace PayOS;

class PayOS
{
    private string $baseUrl;
    private string $clientId;
    private string $apiKey;
    private string $checksumKey;
    private bool $insecure = false; // cho phép tắt kiểm tra SSL trong môi trường dev (không khuyến nghị)

    public function __construct(array $cfg)
    {
        $this->baseUrl = rtrim($cfg['base_url'] ?? '', '/');
        $this->clientId = (string)($cfg['client_id'] ?? '');
        $this->apiKey = (string)($cfg['api_key'] ?? '');
        $this->checksumKey = (string)($cfg['checksum_key'] ?? '');
        $this->insecure = (bool)($cfg['insecure'] ?? (getenv('PAYOS_INSECURE') ? true : false));
    }

    // Tạo chữ ký HMAC SHA256 theo dạng query key=value&... (sắp xếp key)
    public function sign(array $payload): string
    {
        ksort($payload);
        $data = [];
        foreach ($payload as $k=>$v) { if ($v!=='' && $v!==null) $data[] = $k.'='.$v; }
        $qs = implode('&', $data);
        return hash_hmac('sha256', $qs, $this->checksumKey);
    }

    public function createPaymentLink(array $data): array
    {
        $endpoint = $this->baseUrl . '/payment-requests';
        $payload = [
            'orderCode'   => $data['orderCode'],
            'amount'      => (int)$data['amount'],
            'description' => $data['description'] ?? ('Thanh toan don hang #' . $data['orderCode']),
            'returnUrl'   => $data['returnUrl'] ?? '',
            'cancelUrl'   => $data['cancelUrl'] ?? '',
        ];
        $payload['signature'] = $this->sign($payload);
        $resp = $this->request('POST', $endpoint, $payload);
        return $resp;
    }

    public function verify(array $params, string $signature): bool
    {
        $data = $params; unset($data['signature']);
        return hash_equals($this->sign($data), $signature);
    }

    private function request(string $method, string $url, array $json=null): array
    {
        $headers = [
            'Content-Type: application/json',
            'x-client-id: ' . $this->clientId,
            'x-api-key: ' . $this->apiKey,
        ];

        // Ưu tiên cURL; nếu môi trường XAMPP chưa bật cURL, fallback sang stream context
        if (function_exists('curl_init')) {
            $ch = curl_init();
            $opts = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_TIMEOUT => 30,
            ];
            if ($json !== null) { $opts[CURLOPT_POSTFIELDS] = json_encode($json); }
            curl_setopt_array($ch, $opts);
            $raw = curl_exec($ch);
            $err = curl_error($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($raw === false) return ['error'=>true,'message'=>$err?:'cURL error'];
            $data = json_decode($raw, true);
            if ($code >= 400) return ['error'=>true,'message'=>$data['message'] ?? ('HTTP '.$code), 'raw'=>$data];
            return $data ?: [];
        }

        // Fallback: stream context
        $optHeaders = implode("\r\n", $headers);
        $contextOptions = [
            'http' => [
                'method' => $method,
                'header' => $optHeaders,
                'content' => $json !== null ? json_encode($json) : null,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ];
        // Nếu thiếu CA cert ở Windows/XAMPP, có thể bật insecure trong config để test nội bộ
        if ($this->insecure) {
            $contextOptions['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ];
        }
        $ctx = stream_context_create($contextOptions);
        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            $lastErr = error_get_last();
            $msg = $lastErr['message'] ?? 'HTTP(S) request failed (no cURL)';
            return ['error'=>true,'message'=>$msg];
        }
        // Try to read HTTP status from $http_response_header
        $code = 200;
        if (isset($http_response_header[0]) && preg_match('~\s(\d{3})\s~', $http_response_header[0], $m)) {
            $code = (int)$m[1];
        }
        $data = json_decode($raw, true);
        if ($code >= 400) return ['error'=>true,'message'=>$data['message'] ?? ('HTTP '.$code), 'raw'=>$data];
        return $data ?: [];
    }
}
