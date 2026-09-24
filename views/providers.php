<?php $editing=in_array($action,['new','edit'],true); ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Affiliate Providers · TravelSite.lk</title><link rel="stylesheet" href="assets/admin.css"></head><body>
<aside><div class="logo">✈ <span>travelsite.lk</span><small>ADMIN</small></div><nav>
<a href="?page=dashboard">Dashboard</a><a href="?page=destinations">Destinations</a><a class="on" href="?page=providers">Affiliate Providers</a>
<a href="?page=ai-search">AI Search</a><span>Trips</span><span>Clicks & Conversions</span><span>CMS & SEO</span><span>Site Settings</span><span>Admin Users</span><span>Audit Log</span>
</nav><a class="logout" href="?page=logout">Sign out</a></aside>
<section class="shell"><header><div><b>Affiliate Providers</b><small>Supplier and referral configuration</small></div><div><?=e($_SESSION['admin_name'])?> · <?=e($_SESSION['admin_role'])?></div></header><main>
<?php if($notice):?><div class="alert"><?=e($notice)?></div><?php endif;?>
<?php if($editing):?>
<div class="title"><div><h1><?=$id?'Edit':'Add'?> provider</h1><p>Configure non-secret provider and affiliate settings. API secrets stay in the server environment.</p></div><a class="btn secondary" href="?page=providers">Back</a></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form class="panel formgrid" method="post"><input type="hidden" name="csrf" value="<?=csrf()?>">
<label>Provider name<input name="name" maxlength="150" value="<?=e($item['name'])?>" placeholder="Booking.com" required></label>
<label>Internal code<input name="code" maxlength="80" value="<?=e($item['code'])?>" placeholder="booking-com" required></label>
<label>Product type<select name="type"><?php foreach($types as $t):?><option value="<?=$t?>" <?=$item['type']===$t?'selected':''?>><?=ucfirst($t)?></option><?php endforeach;?></select></label>
<label>Affiliate / partner ID<input name="affiliate_id" maxlength="190" value="<?=e($item['affiliate_id'])?>"></label>
<label class="wide">Base / deep-link URL<input name="base_url" maxlength="500" value="<?=e($item['base_url'])?>" placeholder="https://partner.example/..."></label>
<label class="wide">Non-secret provider configuration (JSON)<textarea class="code" name="config_json" rows="9" placeholder='{"market":"LK","currency":"LKR"}'><?=e($item['config_json'])?></textarea><small>Never store API keys, passwords or client secrets here. Those belong in .env on SiteGround.</small></label>
<label class="toggle"><input type="checkbox" name="active" <?=$item['active']?'checked':''?>> Provider enabled</label>
<div class="wide actions"><button>Save provider</button></div></form>
<?php else:?>
<div class="title"><div><h1>Affiliate Providers</h1><p>Manage flight, hotel, activity and package partners.</p></div><a class="btn" href="?page=providers&action=new">+ Add provider</a></div>
<form class="searchbar"><input type="hidden" name="page" value="providers"><input name="q" value="<?=e($q)?>" placeholder="Search provider"><select name="type"><option value="">All types</option><?php foreach($types as $t):?><option value="<?=$t?>" <?=$type===$t?'selected':''?>><?=ucfirst($t)?></option><?php endforeach;?></select><button>Filter</button></form>
<section class="panel tablewrap"><table><thead><tr><th>Provider</th><th>Type</th><th>Affiliate ID</th><th>Status</th><th></th></tr></thead><tbody>
<?php if(!$rows):?><tr><td colspan="5" class="empty">No providers configured yet.</td></tr><?php endif;?>
<?php foreach($rows as $row):?><tr><td><b><?=e($row['name'])?></b><small><?=e($row['code'])?></small></td><td><?=e(ucfirst($row['type']))?></td><td><?=e($row['affiliate_id'] ?: '—')?></td><td><span class="badge <?=$row['active']?'green':'muted'?>"><?=$row['active']?'Enabled':'Disabled'?></span></td>
<td class="rowactions"><a href="?page=providers&action=edit&id=<?=$row['id']?>">Edit</a><?php if(in_array($_SESSION['admin_role'],['super_admin','admin'],true)):?><form method="post" action="?page=providers&action=delete" onsubmit="return confirm('Delete this provider?')"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$row['id']?>"><button class="link danger">Delete</button></form><?php endif;?></td></tr><?php endforeach;?>
</tbody></table></section><?php endif;?>
</main></section></body></html>
