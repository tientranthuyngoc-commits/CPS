<?php
namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function login(): void
    {
        $this->ensureSession();
        if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
        require __DIR__ . '/../../views/login_utf8.php';
    }

    public function loginSubmit(): void
    {
        $this->ensureSession();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=login'); exit; }
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $username !== '' ? User::findByUsername($username) : null;
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            header('Location: index.php');
            exit;
        }

        // LDAP fallback nếu được bật
        $cfg = @require __DIR__ . '/../../includes/auth_providers.php';
        $ldap = $cfg['ldap'] ?? [];
        if (!empty($ldap['enabled']) && function_exists('ldap_connect') && $username !== '' && $password !== '') {
            try {
                $host = $ldap['host'] ?? 'ldap://127.0.0.1';
                $port = (int)($ldap['port'] ?? 389);
                $conn = @ldap_connect($host, $port);
                if ($conn) {
                    @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $dnTpl = $ldap['bind_dn_template'] ?? 'uid={username}';
                    $bindDn = str_replace('{username}', $username, $dnTpl);
                    if (@ldap_bind($conn, $bindDn, $password)) {
                        $pdo = \App\Database::getInstance()->pdo();
                        $st = $pdo->prepare('SELECT * FROM users WHERE username = :u');
                        $st->execute([':u'=>$username]);
                        $u = $st->fetch(\PDO::FETCH_ASSOC);
                        if (!$u) {
                            $emailDomain = $ldap['default_email_domain'] ?? 'example.com';
                            $email = $username . '@' . $emailDomain;
                            $pdo->prepare('INSERT INTO users (username, email, password_hash, role, is_active, provider) VALUES (:u,:e,:h, "user", 1, :p)')
                                ->execute([':u'=>$username, ':e'=>$email, ':h'=>password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), ':p'=>'ldap']);
                            $u = $pdo->query('SELECT * FROM users WHERE id = '.$pdo->lastInsertId())->fetch(\PDO::FETCH_ASSOC);
                        }
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = (int)$u['id'];
                        $_SESSION['username'] = $u['username'];
                        $_SESSION['role'] = $u['role'] ?? 'user';
                        header('Location: index.php');
                        exit;
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        $error = 'Sai t�i kho?n ho?c m?t kh?u';
        require __DIR__ . '/../../views/login_utf8.php';
    }

    public function logout(): void
    {
        $this->ensureSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}

