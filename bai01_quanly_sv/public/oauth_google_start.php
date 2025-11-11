<?php
// Khởi tạo luồng OAuth với Google
if (session_status()===PHP_SESSION_NONE) session_start();
// Load config safely (avoid fatal if missing)
$cfg = @require __DIR__ . '/../includes/auth_providers.php';
if (!is_array($cfg)) { $cfg = []; }
$g = $cfg['google'] ?? [];
if (empty($g['enabled']) || empty($g['client_id']) || empty($g['redirect_uri'])) {
  header('Location: index.php?action=login&err=google_disabled');
  exit;
}

$state = bin2hex(random_bytes(16)); $_SESSION['oauth_state'] = $state;
$params = [
  'response_type' => 'code',
  'client_id' => $g['client_id'],
  'redirect_uri' => $g['redirect_uri'],
  'scope' => 'openid email profile',
  'access_type' => 'offline',
  'prompt' => 'consent',
  'state' => $state,
];
$url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $url);
exit;
