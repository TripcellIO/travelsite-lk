<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<title>TravelSite.lk Admin</title><link rel="stylesheet" href="assets/admin.css"></head>
<body class="login-bg"><main class="login-card">
<div class="brand"><span>✈</span> <b>travelsite.lk</b></div>
<h1>Admin Portal</h1>
<p>Manage TravelSite.lk operations from one place.</p>
<?php if (!empty($error)): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf()?>">
<label>Email<input type="email" name="email" required autofocus></label>
<label>Password<input type="password" name="password" required></label>
<button>Sign in</button>
</form>
</main></body></html>
