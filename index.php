<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/config.php';

$page = $_GET['page'] ?? 'dashboard';

if ($page === 'login') {
    if (auth()) { header('Location: ?page=dashboard'); exit; }

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf();
        $stmt = db()->prepare('SELECT * FROM admin_users WHERE email=? AND status="active" LIMIT 1');
        $stmt->execute([trim($_POST['email'] ?? '')]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            db()->prepare('UPDATE admin_users SET last_login_at=NOW() WHERE id=?')->execute([$user['id']]);
            audit('login');
            header('Location: ?page=dashboard');
            exit;
        }
        $error = 'Invalid email or password.';
    }
    require __DIR__ . '/views/login.php';
    exit;
}

if ($page === 'logout') {
    if (auth()) audit('logout');
    $_SESSION = [];
    session_destroy();
    header('Location: ?page=login');
    exit;
}

require_auth();
$allowed = ['dashboard'];
if (!in_array($page, $allowed, true)) $page = 'dashboard';

require __DIR__ . '/views/layout.php';
