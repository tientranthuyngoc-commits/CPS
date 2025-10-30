<?php
// PayOS configuration. Điền thông tin tài khoản của bạn tại đây.
return [
  'base_url'     => getenv('PAYOS_BASE_URL') ?: 'https://api.payos.vn/v2',
  // NOTE: Có thể đặt các khóa này qua biến môi trường PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY
  // Nếu không có biến môi trường, hệ thống sẽ dùng giá trị mặc định bên dưới.
  'client_id'    => getenv('PAYOS_CLIENT_ID') ?: '5fccb760-d41f-4e86-8508-c6b18e317525',
  'api_key'      => getenv('PAYOS_API_KEY')   ?: 'a9e678bb-7fbf-45b6-b9d1-de1989547bdc',
  'checksum_key' => getenv('PAYOS_CHECKSUM_KEY') ?: 'be6adcd4d502db581ef166c1a4c334f1a037d5e6fffc155ed9a93915d91872c5',
  // Đường dẫn trả về (trên site của bạn)
  'return_url'   => (function(){
      $host = (isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : 'localhost');
      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off')?'https':'http';
      return $scheme . '://' . $host . dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php') . '/index.php?action=payos_return';
  })(),
  'cancel_url'   => (function(){
      $host = (isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST'] : 'localhost');
      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off')?'https':'http';
      return $scheme . '://' . $host . dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php') . '/index.php?action=checkout';
  })(),
  // Webhook: cấu hình trong PayOS portal trỏ về action=payos_webhook
];
