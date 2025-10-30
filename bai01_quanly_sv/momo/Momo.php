<?php
namespace Momo;

class Client
{
    private string $endpoint;
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $returnUrl;
    private string $ipnUrl;

    public function __construct(array $cfg)
    {
        $this->endpoint    = (string)($cfg['endpoint'] ?? '');
        $this->partnerCode = (string)($cfg['partnerCode'] ?? '');
        $this->accessKey   = (string)($cfg['accessKey'] ?? '');
        $this->secretKey   = (string)($cfg['secretKey'] ?? '');
        $this->returnUrl   = (string)($cfg['return_url'] ?? '');
        $this->ipnUrl      = (string)($cfg['ipn_url'] ?? '');
    }

    public function createPayment(string $orderId, int $amount, string $orderInfo): array
    {
        $requestId = (string)round(microtime(true) * 1000);
        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'CPS Shop',
            'storeId'     => 'CPS-LOCAL',
            'requestId'   => $requestId,
            'amount'      => (string)$amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl'      => $this->ipnUrl,
            'lang'        => 'vi',
            'requestType' => 'captureWallet',
            'extraData'   => base64_encode(json_encode(['note'=>'CPS Shop'])),
        ];
        $rawHash = sprintf(
            'accessKey=%s&amount=%s&extraData=%s&ipnUrl=%s&orderId=%s&orderInfo=%s&partnerCode=%s&redirectUrl=%s&requestId=%s&requestType=%s',
            $this->accessKey,
            $data['amount'],
            $data['extraData'],
            $data['ipnUrl'],
            $data['orderId'],
            $data['orderInfo'],
            $data['partnerCode'],
            $data['redirectUrl'],
            $data['requestId'],
            $data['requestType']
        );
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);
        $data['signature'] = $signature;

        // Ghi log debug chữ ký để chẩn đoán sai khóa/endpoint
        @file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | create_raw | ".$rawHash." | sig=".$signature."\n", FILE_APPEND);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'timeout' => 30,
                'ignore_errors' => true,
            ]
        ];
        // Ưu tiên cURL nếu có (ổn định SSL trên Windows)
        $raw = null;
        if (function_exists('curl_init')) {
            $ch = curl_init($this->endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json','User-Agent: CPS-Local/1.0'],
                CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
                CURLOPT_TIMEOUT => 30,
            ]);
            $raw = curl_exec($ch);
            if ($raw === false) {
                @file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | curl_error | ".curl_error($ch)."\n", FILE_APPEND);
            }
            curl_close($ch);
        }
        if ($raw === null) {
            $ctx = stream_context_create($opts);
            $raw = @file_get_contents($this->endpoint, false, $ctx);
        }
        $res = json_decode($raw ?: '[]', true) ?: [];
        if (!$res) {
            @file_put_contents(__DIR__.'/../data/momo_errors.log', date('c')." | http_error | ".$raw."\n", FILE_APPEND);
        }
        return $res;
    }
}
