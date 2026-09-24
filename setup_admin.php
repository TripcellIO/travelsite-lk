<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

$done = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals((string)envv('APP_KEY', ''), (string)($_POST['setup_key'] ?? ''))) {
        $error = 'Invalid setup key.';
    } elseif (strlen($_POST['password'] ?? '') < 12) {
        $error = 'Use a password of at least 12 characters.';
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO admin_users(name,email,password_hash,role) VALUES(?,?,?,"super_admin")');
            $stmt->execute([
                trim($_POST['name'] ?? ''),
                strtolower(trim($_POST['email'] ?? '')),
                password_hash($_POST['password'], PASSWORD_DEFAULT)
            ]);
            $done = true;
        } catch (Throwable) {
            $error = 'Could not create the administrator. The email may already exist.';
        }
    }
}
?><!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<title>TravelSite.lk Setup</title><link rel="stylesheet" href="assets/admin.css"></head>
<body class="login-bg"><main class="login-card">
<div class="brand">✈ <b>travelsite.lk</b></div>
<h1>Initial admin setup</h1>
<?php if ($done): ?>
<div class="alert">Administrator created. Delete <b>setup_admin.php</b> from production, then <a href="index.php?page=login">sign in</a>.</div>
<?php else: ?>
<p>Run this once after importing database.sql.</p>
<?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post">
<label>Name<input name="name" required></label>
<label>Email<input type="email" name="email" required></label>
<label>Password<input type="password" name="password" minlength="12" required></label>
<label>APP_KEY<input type="password" name="setup_key" required></label>
<button>Create super administrator</button>
</form>
<?php endif; ?>
</main></body></html>
