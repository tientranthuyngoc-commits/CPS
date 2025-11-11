<?php
// Xử lý callback OAuth Google: đổi code -> token, lấy id_token
// Lưu ý: file này được include thông qua public/index.php (đã nạp autoload + Database)
if (session_status()===PHP_SESSION_NONE) session_start();
// Ensure autoload/Database if this file is accessed directly (not via index.php)
if (!class_exists('App\\Database') && is_readable(__DIR__ . '/../src/Database.php')) {
  require __DIR__ . '/../src/Database.php';
  spl_autoload_register(function($class){
    $prefix = 'App\\\\';
    $base = __DIR__ . '/../src/';
    if (str_starts_with($class, $prefix)) {
      $rel = substr($class, strlen($prefix));
      $file = $base . str_replace('\\\\','/',$rel) . '.php';
      if (is_readable($file)) require $file;
    }
  });
}
function oauth_log(string $msg): void { @file_put_contents(__DIR__.'/../data/oauth_google.log', date('c')." | ".$msg."\n", FILE_APPEND); }

// Load config safely and validate
$cfg = @require __DIR__ . '/../includes/auth_providers.php';
if (!is_array($cfg)) { $cfg = []; }
$g = $cfg['google'] ?? [];
if (empty($g['enabled']) || empty($g['client_id']) || empty($g['client_secret']) || empty($g['redirect_uri'])) {
  oauth_log('error: disabled');
  header('Location: index.php?action=login&err=google_disabled');
  exit;
}

function http_post_json(string $url, array $data): array {
  // Prefer cURL if available for better compatibility
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    return json_decode($raw ?: '[]', true) ?: [];
  }

  // Fallback to streams
  $opts = [
    'http' => [
      'method' => 'POST',
      'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
      'content' => http_build_query($data),
      'timeout' => 30,
      'ignore_errors' => true,
    ]
  ];
  $ctx = stream_context_create($opts);
  $raw = @file_get_contents($url,false,$ctx);
  return json_decode($raw ?: '[]', true) ?: [];
}

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
if (!$code || !$state || ($state !== ($_SESSION['oauth_state'] ?? ''))) {
  oauth_log('error: state_mismatch');
  header('Location: index.php?action=login&err=state'); exit;
}
unset($_SESSION['oauth_state']);

$resp = http_post_json('https://oauth2.googleapis.com/token', [
  'code' => $code,
  'client_id' => $g['client_id'],
  'client_secret' => $g['client_secret'],
  'redirect_uri' => $g['redirect_uri'],
  'grant_type' => 'authorization_code',
]);

if (empty($resp) || isset($resp['error'])) {
  oauth_log('token_error: '.json_encode($resp));
  header('Location: index.php?action=login&err=token'); exit;
}

$idToken = $resp['id_token'] ?? '';
if (!$idToken) { oauth_log('error: missing_id_token'); header('Location: index.php?action=login&err=idtoken'); exit; }

// Giải mã payload JWT (demo – nên xác minh chữ ký ở sản xuất)
function jwt_payload(string $jwt): array {
  $parts = explode('.', $jwt);
  if (count($parts) < 2) return [];
  $payload = $parts[1];
  $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);
  $json = base64_decode(strtr($payload, '-_', '+/'));
  return json_decode($json ?: '[]', true) ?: [];
}

$payload = jwt_payload($idToken);
$email = (string)($payload['email'] ?? '');
$name  = (string)($payload['name'] ?? '');
$sub   = (string)($payload['sub'] ?? '');
$aud   = (string)($payload['aud'] ?? '');

if (!$email || !$sub || ($aud !== $g['client_id'])) { oauth_log('error: invalid_payload '.json_encode($payload)); header('Location: index.php?action=login&err=idtoken'); exit; }
if (!empty($g['allowed_domains'])) {
  $domain = substr(strrchr($email, '@') ?: '', 1);
  if ($domain === '' || !in_array(strtolower($domain), array_map('strtolower',$g['allowed_domains']))) {
    oauth_log('error: domain_block '.$domain);
    header('Location: index.php?action=login&err=domain'); exit;
  }
}

// Tạo/đăng nhập user
$pdo = \App\Database::getInstance()->pdo();
$st = $pdo->prepare('SELECT * FROM users WHERE provider = :p AND provider_uid = :u');
$st->execute([':p'=>'google', ':u'=>$sub]);
$user = $st->fetch(\PDO::FETCH_ASSOC);
if (!$user) {
  // Nếu chưa có, thử khớp theo email
  $st2 = $pdo->prepare('SELECT * FROM users WHERE email = :e');
  $st2->execute([':e'=>$email]);
  $user = $st2->fetch(\PDO::FETCH_ASSOC);
  if ($user) {
    $pdo->prepare('UPDATE users SET provider=:p, provider_uid=:u WHERE id=:id')->execute([':p'=>'google',':u'=>$sub, ':id'=>$user['id']]);
  } else {
    $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, role, is_active, provider, provider_uid) VALUES (:u,:e,:h, "user", 1, :p, :uid)');
    $uname = $name ?: strstr($email,'@',true);
    $ins->execute([':u'=>$uname, ':e'=>$email, ':h'=>password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), ':p'=>'google', ':uid'=>$sub]);
    $user = $pdo->query('SELECT * FROM users WHERE id = '.$pdo->lastInsertId())->fetch(\PDO::FETCH_ASSOC);
  }
}

if ($user) {
  session_regenerate_id(true);
  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['username'] = $user['username'];
  $_SESSION['role'] = $user['role'] ?? 'user';
}

header('Location: index.php');
exit;
