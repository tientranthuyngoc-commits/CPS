<?php
// OAuth and auth provider configuration
// Fill in your real credentials for Google (and optionally Facebook/LDAP)
// Redirect URI MUST exactly match what is configured in the provider console.
// For this app, use the front controller route so autoload/Database are available:
//   http://localhost/CPS/bai01_quanly_sv/public/index.php?action=oauth_google_callback

return [
    'google' => [
        'enabled' => true,
        // Google OAuth 2.0 Web client credentials (env var takes precedence)
        // NOTE: Do NOT commit real secrets. Set via environment or .env
        'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID',
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/CPS/bai01_quanly_sv/public/index.php?action=oauth_google_callback',
        // Optional: restrict logins to certain email domains, e.g. ['yourcompany.com']
        'allowed_domains' => [],
    ],

    // Optional placeholders to avoid undefined index usage elsewhere
    'facebook' => [
        'enabled' => false,
        'client_id' => '',
        'client_secret' => '',
        'redirect_uri' => '',
    ],

    'ldap' => [
        'enabled' => false,
        'host' => 'ldap://127.0.0.1',
        'port' => 389,
        'bind_dn_template' => 'uid={username}',
        'default_email_domain' => 'example.com',
    ],
];
