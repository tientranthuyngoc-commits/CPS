<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

$cfg = require __DIR__ . '/../payos/config.php';
if (!class_exists('PayOS\\PayOS')) require_once __DIR__ . '/../payos/PayOS.php';
if (!class_exists('App\\Database')) require_once __DIR__ . '/../src/Database.php';

use PayOS\PayOS;

function payos_redirect_with_error(string $code, array $extra = []): void {
    $qs = http_build_query(array_merge(['err' => $code], $extra));
    header('Location: index.php?action=checkout&' . $qs);
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
    payos_redirect_with_error('payos_missing_order');
}

try {
    $pdo = \App\Database::getInstance()->pdo();
    $st = $pdo->prepare('SELECT id, total, customer_name FROM orders WHERE id = :id LIMIT 1');
    $st->execute([':id' => $orderId]);
    $order = $st->fetch(\PDO::FETCH_ASSOC) ?: null;
    if (!$order) {
        payos_redirect_with_error('order_not_found');
    }
    $amount = (int)($order['total'] ?? 0);
    if ($amount <= 0) {
        payos_redirect_with_error('invalid_amount');
    }

    $client = new PayOS($cfg);
    $payload = [
        'orderCode'   => $orderId,
        'amount'      => $amount,
        'description' => 'Thanh toán đơn hàng #' . $orderId,
        'returnUrl'   => (string)($cfg['return_url'] ?? ''),
        'cancelUrl'   => (string)($cfg['cancel_url'] ?? ''),
    ];

    $res = $client->createPaymentLink($payload);

    // Thành công: cố gắng lấy URL thanh toán
    if (!($res['error'] ?? false)) {
        $payUrl = '';
        if (isset($res['checkoutUrl'])) $payUrl = (string)$res['checkoutUrl'];
        elseif (isset($res['data']['checkoutUrl'])) $payUrl = (string)$res['data']['checkoutUrl'];
        elseif (isset($res['data']['shortLink'])) $payUrl = (string)$res['data']['shortLink'];
        elseif (isset($res['link'])) $payUrl = (string)$res['link'];

        if ($payUrl !== '') {
            header('Location: ' . $payUrl);
            exit;
        }
        // Không tìm thấy link => coi như lỗi
        $res = ['error' => true, 'message' => 'Missing checkoutUrl in response', 'raw' => $res];
    }

    // Nếu lỗi: phân loại để hiển thị thông báo thân thiện
    $msg = (string)($res['message'] ?? '');
    $errCode = 'payos_create_failed';

    // Thiếu cURL trong môi trường
    if (!function_exists('curl_init')) {
        $errCode = 'payos_curl';
    }
    // DNS/Network lỗi
    if (stripos($msg, 'getaddrinfo') !== false || stripos($msg, 'No such host') !== false) {
        $errCode = 'payos_network';
    }
    // SSL cert lỗi
    elseif (stripos($msg, 'ssl') !== false || stripos($msg, 'certificate') !== false) {
        $errCode = 'payos_ssl';
    }
    // 401/403 hoặc lỗi xác thực header
    elseif (stripos($msg, 'unauthorized') !== false || stripos($msg, 'forbidden') !== false || stripos($msg, '401') !== false || stripos($msg, '403') !== false) {
        $errCode = 'payos_auth';
    }

    // Ghi log chi tiết để debug
    $logDir = __DIR__ . '/../data';
    if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
    @file_put_contents(
        $logDir . '/payos_errors.log',
        date('c') . ' | createPaymentLink failed | order#' . $orderId . ' | ' . json_encode($res, JSON_UNESCAPED_UNICODE) . PHP_EOL,
        FILE_APPEND
    );

    payos_redirect_with_error($errCode);
} catch (\Throwable $e) {
    // Bắt mọi exception không mong muốn
    $logDir = __DIR__ . '/../data';
    if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
    @file_put_contents(
        $logDir . '/payos_errors.log',
        date('c') . ' | exception | order#' . $orderId . ' | ' . $e->getMessage() . PHP_EOL,
        FILE_APPEND
    );
    payos_redirect_with_error('exception');
}

