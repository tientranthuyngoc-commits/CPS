<?php
// Cấu hình MoMo. Điền thông tin app của bạn tại đây hoặc dùng biến môi trường.
return [
  // Endpoint test của MoMo. Khi lên production, đổi sang endpoint production.
  'endpoint'     => getenv('MOMO_ENDPOINT') ?: 'https://test-payment.momo.vn/v2/gateway/api/create',
  'partnerCode'  => getenv('MOMO_PARTNER_CODE') ?: 'YOUR_PARTNER_CODE',
  'accessKey'    => getenv('MOMO_ACCESS_KEY')   ?: 'YOUR_ACCESS_KEY',
  'secretKey'    => getenv('MOMO_SECRET_KEY')   ?: 'YOUR_SECRET_KEY',
  // URL trả về trình duyệt
  'return_url'   => (function(){
      $host = (isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : 'localhost');
      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off')?'https':'http';
      return $scheme . '://' . $host . dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php') . '/index.php?action=momo_return';
  })(),
  // IPN (server to server)
  'ipn_url'      => (function(){
      $host = (isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : 'localhost');
      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off')?'https':'http';
      return $scheme . '://' . $host . dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php') . '/index.php?action=momo_ipn';
  })(),
];

