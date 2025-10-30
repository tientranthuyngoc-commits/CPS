<?php
// Khởi tạo luồng OAuth với Facebook
if (session_status()===PHP_SESSION_NONE) session_start();
$cfg = require __DIR__ . '/../includes/auth_providers.php';
$fb = $cfg['facebook'] ?? [];
if (empty($fb['enabled'])) { header('Location: index.php?action=login'); exit; }
if (empty($fb['client_id']) || empty($fb['redirect_uri'])) { header('Location: index.php?action=login'); exit; }

$version = $fb['graph_version'] ?? 'v17.0';
$state = bin2hex(random_bytes(16)); $_SESSION['oauth_state_fb'] = $state;
$params = [
  'client_id' => $fb['client_id'],
  'redirect_uri' => $fb['redirect_uri'],
  'state' => $state,
  'response_type' => 'code',
  'scope' => 'email,public_profile',
];
$url = 'https://www.facebook.com/'.$version.'/dialog/oauth?' . http_build_query($params);
header('Location: ' . $url);
exit;

