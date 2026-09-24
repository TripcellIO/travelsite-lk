<?php
$counts = ['destinations'=>0,'providers'=>0,'trips'=>0,'clicks'=>0];
try {
    $counts['destinations']=(int)db()->query('SELECT COUNT(*) FROM destinations')->fetchColumn();
    $counts['providers']=(int)db()->query('SELECT COUNT(*) FROM providers')->fetchColumn();
    $counts['trips']=(int)db()->query('SELECT COUNT(*) FROM trips')->fetchColumn();
    $counts['clicks']=(int)db()->query('SELECT COUNT(*) FROM affiliate_clicks')->fetchColumn();
} catch (Throwable) {}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<title>Dashboard · TravelSite.lk</title><link rel="stylesheet" href="assets/admin.css"></head>
<body>
<aside>
<div class="logo">✈ <span>travelsite.lk</span><small>ADMIN</small></div>
<nav><a class="on" href="?page=dashboard">Dashboard</a>
<span>Destinations</span><span>Affiliate Providers</span><a href="?page=ai-search">AI Search</a><span>Trips</span>
<span>Clicks & Conversions</span><span>CMS & SEO</span><span>Site Settings</span><span>Admin Users</span><span>Audit Log</span></nav>
<a class="logout" href="?page=logout">Sign out</a>
</aside>
<section class="shell">
<header><div><b>Dashboard</b><small>TravelSite.lk control centre</small></div><div><?=e($_SESSION['admin_name'] ?? 'Admin')?> · <?=e($_SESSION['admin_role'] ?? '')?></div></header>
<main>
<div class="title"><div><h1>Welcome back, <?=e($_SESSION['admin_name'] ?? 'Admin')?></h1><p>Admin foundation is online. Operational modules will be added step by step.</p></div><span class="badge green">System online</span></div>
<div class="stats">
<article><span>Destinations</span><strong><?=$counts['destinations']?></strong><small>configured</small></article>
<article><span>Providers</span><strong><?=$counts['providers']?></strong><small>affiliate integrations</small></article>
<article><span>Trips</span><strong><?=$counts['trips']?></strong><small>traveller itineraries</small></article>
<article><span>Affiliate clicks</span><strong><?=$counts['clicks']?></strong><small>tracked referrals</small></article>
</div>
<div class="grid2">
<section class="panel"><h2>Foundation status</h2>
<div class="check">✓ PHP application shell <em>Ready</em></div>
<div class="check">✓ MySQL schema <em>Ready</em></div>
<div class="check">✓ Admin authentication <em>Ready</em></div>
<div class="check">✓ CSRF protection <em>Ready</em></div>
<div class="check">✓ Audit logging foundation <em>Ready</em></div>
</section>
<section class="panel"><h2>Next build</h2><p>Step 2 will turn Destinations into the first full CRUD module, including content, images, featured status and SEO metadata.</p></section>
</div>
</main></section></body></html>
