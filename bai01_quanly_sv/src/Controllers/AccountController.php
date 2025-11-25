<?php
namespace App\Controllers;

use App\Database;
use PDO;

class AccountController
{
    private function ensureSession(): void { if (session_status()===PHP_SESSION_NONE) session_start(); }
    private function pdo(): PDO { return Database::getInstance()->pdo(); }

    public function register(): void
    {
        $this->ensureSession();
        if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
        $error = '';
        require __DIR__.'/../../views/register.php';
    }

    public function registerSubmit(): void
    {
        $this->ensureSession();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=register'); exit; }
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        if ($username === '' || $email === '' || $password === '' || $password !== $confirm) {
            $error = 'Vui lòng nhập đủ thông tin và xác nhận mật khẩu.';
            require __DIR__.'/../../views/register.php'; return;
        }
        $pdo = $this->pdo();
        $st = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e');
        $st->execute([':u'=>$username, ':e'=>$email]);
        if ($st->fetch()) { $error='Tên đăng nhập hoặc email đã tồn tại.'; require __DIR__.'/../../views/register.php'; return; }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, role, is_active) VALUES (:u,:e,:h, "user", 1)');
        $ins->execute([':u'=>$username, ':e'=>$email, ':h'=>$hash]);
        $_SESSION['user_id'] = (int)$pdo->lastInsertId();
        $_SESSION['username'] = $username; $_SESSION['role'] = 'user';
        header('Location: index.php');
    }

    public function forgot(): void
    {
        $this->ensureSession(); $msg = '';$error='';
        require __DIR__.'/../../views/forgot.php';
    }

    public function forgotSubmit(): void
    {
        $this->ensureSession();
        $email = trim($_POST['email'] ?? ''); $msg=''; $error='';
        if ($email === '') { $error = 'Nhập email đã đăng ký.'; require __DIR__.'/../../views/forgot.php'; return; }
        $pdo = $this->pdo();
        $st = $pdo->prepare('SELECT id FROM users WHERE email = :e'); $st->execute([':e'=>$email]); $id = (int)($st->fetchColumn() ?: 0);
        if ($id) {
            $token = bin2hex(random_bytes(16)); $exp = date('Y-m-d H:i:s', time()+3600);
            $pdo->prepare('UPDATE users SET reset_token=:t, reset_expires=:x WHERE id=:id')->execute([':t'=>$token, ':x'=>$exp, ':id'=>$id]);
            // Log "email" to file for demo
            @file_put_contents(__DIR__.'/../../data/reset_links.log', date('c')." | $email | http://localhost/CPS/bai01_quanly_sv/public/index.php?action=reset&token=$token\n", FILE_APPEND);
            $msg = 'Đã tạo liên kết đặt lại mật khẩu. Kiểm tra file data/reset_links.log';
        } else {
            $error = 'Email không tồn tại.';
        }
        require __DIR__.'/../../views/forgot.php';
    }

    public function reset(): void
    {
        $this->ensureSession(); $token = $_GET['token'] ?? '';$error='';
        if ($token==='') { echo 'Liên kết không hợp lệ'; return; }
        require __DIR__.'/../../views/reset.php';
    }

    public function resetSubmit(): void
    {
        $this->ensureSession();
        $token = $_POST['token'] ?? ''; $pass = $_POST['password'] ?? ''; $confirm = $_POST['confirm'] ?? '';
        if ($token==='' || $pass==='' || $pass!==$confirm) { $error='Thông tin không hợp lệ.'; require __DIR__.'/../../views/reset.php'; return; }
        $pdo = $this->pdo(); $now = date('Y-m-d H:i:s');
        $st = $pdo->prepare('SELECT id FROM users WHERE reset_token=:t AND (reset_expires IS NULL OR reset_expires >= :n)');
        $st->execute([':t'=>$token, ':n'=>$now]); $id = (int)($st->fetchColumn() ?: 0);
        if (!$id) { $error='Token hết hạn hoặc không hợp lệ.'; require __DIR__.'/../../views/reset.php'; return; }
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash=:h, reset_token=NULL, reset_expires=NULL WHERE id=:id')->execute([':h'=>$hash, ':id'=>$id]);
        header('Location: index.php?action=login');
    }

    public function profile(): void
    {
        $this->ensureSession(); if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo(); $id = (int)$_SESSION['user_id']; $msg=''; $error='';
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? '');
            $pdo->prepare('UPDATE users SET email=:e, phone=:p WHERE id=:id')->execute([':e'=>$email, ':p'=>$phone, ':id'=>$id]);
            $msg = 'Đã cập nhật hồ sơ.';
        }
        $st = $pdo->prepare('SELECT username, email, phone FROM users WHERE id=:id'); $st->execute([':id'=>$id]);
        $user = $st->fetch(PDO::FETCH_ASSOC);
        require __DIR__.'/../../views/account_profile.php';
    }

    public function addresses(): void
    {
        $this->ensureSession(); if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo(); $id = (int)$_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $name=trim($_POST['name']??''); $phone=trim($_POST['phone']??''); $addr=trim($_POST['address_line']??''); $def=(int)($_POST['is_default']??0);
            if ($def===1) { $pdo->prepare('UPDATE addresses SET is_default=0 WHERE user_id=:u')->execute([':u'=>$id]); }
            if ($addr!=='') $pdo->prepare('INSERT INTO addresses (user_id,name,phone,address_line,is_default) VALUES (:u,:n,:p,:a,:d)')->execute([':u'=>$id,':n'=>$name,':p'=>$phone,':a'=>$addr,':d'=>$def]);
        }
        if (isset($_GET['delete'])) { $aid=(int)$_GET['delete']; $pdo->prepare('DELETE FROM addresses WHERE id=:id AND user_id=:u')->execute([':id'=>$aid, ':u'=>$id]); }
        $rows = $pdo->prepare('SELECT * FROM addresses WHERE user_id=:u ORDER BY is_default DESC, id DESC'); $rows->execute([':u'=>$id]);
        $addresses = $rows->fetchAll(PDO::FETCH_ASSOC) ?: [];
        require __DIR__.'/../../views/account_addresses.php';
    }

    public function orders(): void
    {
        $this->ensureSession(); if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo(); $id = (int)$_SESSION['user_id'];
        $userStmt = $pdo->prepare('SELECT username, phone FROM users WHERE id=:id');
        $userStmt->execute([':id'=>$id]);
        $userRow = $userStmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        $username = $userRow['username'] ?? '';
        $userPhone = $userRow['phone'] ?? '';

        $st = $pdo->prepare('SELECT * FROM orders WHERE customer_name = :u OR phone = :p OR phone IN (SELECT phone FROM addresses WHERE user_id=:id) ORDER BY id DESC');
        $st->execute([':u'=>$username, ':p'=>$userPhone, ':id'=>$id]);
        $orders = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        require __DIR__.'/../../views/account_orders.php';
    }

    public function orderDetail(): void
    {
        $this->ensureSession(); if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo(); $oid = (int)($_GET['id'] ?? 0);
        $st = $pdo->prepare('SELECT * FROM orders WHERE id=:id'); $st->execute([':id'=>$oid]); $order = $st->fetch(PDO::FETCH_ASSOC);
        $it = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=:id');
        $it->execute([':id'=>$oid]); $items = $it->fetchAll(PDO::FETCH_ASSOC) ?: [];
        // Lấy yêu cầu đổi trả (nếu có)
        $ret = $pdo->prepare('SELECT * FROM order_returns WHERE order_id=:id ORDER BY id DESC');
        $ret->execute([':id'=>$oid]);
        $returns = $ret->fetchAll(PDO::FETCH_ASSOC) ?: [];
        require __DIR__.'/../../views/account_order_detail.php';
    }

    public function orderPrint(): void
    {
        $this->ensureSession(); if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo(); $oid = (int)($_GET['id'] ?? 0);
        $st = $pdo->prepare('SELECT * FROM orders WHERE id=:id'); $st->execute([':id'=>$oid]); $order = $st->fetch(PDO::FETCH_ASSOC);
        $it = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=:id');
        $it->execute([':id'=>$oid]); $items = $it->fetchAll(PDO::FETCH_ASSOC) ?: [];
        require __DIR__.'/../../views/invoice.php';
    }

    public function orderReturn(): void
    {
        $this->ensureSession(); if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $pdo = $this->pdo(); $oid = (int)($_POST['id'] ?? 0); $reason = trim($_POST['reason'] ?? '');
        if ($oid>0 && $reason!=='') {
            $ins = $pdo->prepare('INSERT INTO order_returns (order_id, user_id, reason, status) VALUES (:o,:u,:r, "requested")');
            $ins->execute([':o'=>$oid, ':u'=>(int)$_SESSION['user_id'], ':r'=>$reason]);
        }
        header('Location: index.php?action=account_order_detail&id='.$oid);
    }

    public function changePassword(): void
    {
        $this->ensureSession();
        if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        $msg = '';
        $error = '';
        require __DIR__ . '/../../views/account_change_password.php';
    }

    public function changePasswordSubmit(): void
    {
        $this->ensureSession();
        if (empty($_SESSION['user_id'])) { header('Location: index.php?action=login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?action=account_change_password'); exit; }
        $msg = '';
        $error = '';
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if ($current === '' || $new === '' || $confirm === '') {
            $error = 'Vui lòng nhập đầy đủ thông tin.';
            require __DIR__ . '/../../views/account_change_password.php';
            return;
        }
        if ($new !== $confirm) {
            $error = 'Mật khẩu mới và xác nhận không khớp.';
            require __DIR__ . '/../../views/account_change_password.php';
            return;
        }
        $pdo = $this->pdo();
        $st = $pdo->prepare('SELECT password_hash FROM users WHERE id=:id');
        $st->execute([':id' => (int)$_SESSION['user_id']]);
        $hash = $st->fetchColumn();
        if (!$hash || !password_verify($current, (string)$hash)) {
            $error = 'Mật khẩu hiện tại không chính xác.';
            require __DIR__ . '/../../views/account_change_password.php';
            return;
        }
        if (password_verify($new, (string)$hash)) {
            $error = 'Mật khẩu mới phải khác mật khẩu hiện tại.';
            require __DIR__ . '/../../views/account_change_password.php';
            return;
        }
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash=:h WHERE id=:id')
            ->execute([':h' => $newHash, ':id' => (int)$_SESSION['user_id']]);
        $msg = 'Đã thay đổi mật khẩu thành công.';
        require __DIR__ . '/../../views/account_change_password.php';
    }
    public function uploadAvatar(): void
    {
        $this->ensureSession();
        header('Content-Type: application/json');
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['avatar'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $file = $_FILES['avatar'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed']);
            return;
        }
        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'File too large (max 2MB)']);
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid image type']);
            return;
        }

        $ext = $allowed[$mime];
        $filename = 'u' . (int)$_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $destDir = __DIR__ . '/../../public/uploads/avatars';
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0775, true);
        }
        $destPath = $destDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Cannot save file']);
            return;
        }

        $relative = 'uploads/avatars/' . $filename;
        $pdo = $this->pdo();
        $pdo->prepare('UPDATE users SET avatar = :a WHERE id = :id')
            ->execute([':a' => $relative, ':id' => (int)$_SESSION['user_id']]);
        $_SESSION['avatar'] = $relative;

        echo json_encode([
            'ok' => true,
            'url' => $relative . '?v=' . time(),
        ]);
    }
}
