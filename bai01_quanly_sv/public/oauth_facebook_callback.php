<?php
// Xử lý callback OAuth Facebook: đổi code -> access_token, lấy thông tin người dùng
if (session_status()===PHP_SESSION_NONE) session_start();
function fb_log(string $msg): void { @file_put_contents(__DIR__.'/../data/oauth_facebook.log', date('c')." | ".$msg."\n", FILE_APPEND); }

$cfg = require __DIR__ . '/../includes/auth_providers.php';
$fb = $cfg['facebook'] ?? [];
if (empty($fb['enabled'])) { fb_log('error: disabled'); header('Location: index.php?action=login&err=fb_disabled'); exit; }

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
if (!$code || !$state || ($state !== ($_SESSION['oauth_state_fb'] ?? ''))) {
  fb_log('error: state_mismatch');
  header('Location: index.php?action=login&err=state'); exit;
}
unset($_SESSION['oauth_state_fb']);

$version = $fb['graph_version'] ?? 'v17.0';

// Đổi code lấy access token
$tokenUrl = 'https://graph.facebook.com/'.$version.'/oauth/access_token?'.http_build_query([
  'client_id' => $fb['client_id'],
  'redirect_uri' => $fb['redirect_uri'],
  'client_secret' => $fb['client_secret'],
  'code' => $code,
]);
$raw = @file_get_contents($tokenUrl);
$tok = json_decode($raw ?: '[]', true) ?: [];
if (empty($tok['access_token'])) { fb_log('token_error: '.($raw?:'')); header('Location: index.php?action=login&err=fb_token'); exit; }
$access = $tok['access_token'];

// Lấy thông tin người dùng
$meUrl = 'https://graph.facebook.com/me?'.http_build_query([
  'fields' => 'id,name,email',
  'access_token' => $access,
]);
$rawMe = @file_get_contents($meUrl);
$me = json_decode($rawMe ?: '[]', true) ?: [];
$email = (string)($me['email'] ?? '');
$name  = (string)($me['name'] ?? '');
$id    = (string)($me['id'] ?? '');
if (!$id) { fb_log('error: missing_id '.($rawMe?:'')); header('Location: index.php?action=login&err=fb_me'); exit; }

// Tạo/đăng nhập user
$pdo = \App\Database::getInstance()->pdo();
$st = $pdo->prepare('SELECT * FROM users WHERE provider = :p AND provider_uid = :u');
$st->execute([':p'=>'facebook', ':u'=>$id]);
$user = $st->fetch(\PDO::FETCH_ASSOC);
if (!$user) {
  if ($email) {
    $st2 = $pdo->prepare('SELECT * FROM users WHERE email = :e');
    $st2->execute([':e'=>$email]);
    $user = $st2->fetch(\PDO::FETCH_ASSOC);
  }
  if ($user) {
    $pdo->prepare('UPDATE users SET provider=:p, provider_uid=:u WHERE id=:id')->execute([':p'=>'facebook',':u'=>$id, ':id'=>$user['id']]);
  } else {
    $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, role, is_active, provider, provider_uid) VALUES (:u,:e,:h, "user", 1, :p, :uid)');
    $uname = $name ?: ($email ? strstr($email,'@',true) : ('fb_'.$id));
    $ins->execute([':u'=>$uname, ':e'=>$email ?: ('fb_'.$id.'@example.local'), ':h'=>password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), ':p'=>'facebook', ':uid'=>$id]);
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

