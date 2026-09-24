<?php $editing=in_array($action,['new','edit'],true); ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<title>Destinations · TravelSite.lk</title><link rel="stylesheet" href="assets/admin.css"></head><body>
<aside><div class="logo">✈ <span>travelsite.lk</span><small>ADMIN</small></div><nav>
<a href="?page=dashboard">Dashboard</a><a class="on" href="?page=destinations">Destinations</a>
<span>Affiliate Providers</span><span>AI Search</span><span>Trips</span><span>Clicks & Conversions</span><span>CMS & SEO</span><span>Site Settings</span><span>Admin Users</span><span>Audit Log</span>
</nav><a class="logout" href="?page=logout">Sign out</a></aside>
<section class="shell"><header><div><b>Destinations</b><small>TravelSite.lk content catalogue</small></div><div><?=e($_SESSION['admin_name'])?> · <?=e($_SESSION['admin_role'])?></div></header><main>
<?php if($notice):?><div class="alert"><?=e($notice)?></div><?php endif;?>
<?php if($editing):?>
<div class="title"><div><h1><?=$id?'Edit':'Add'?> destination</h1><p>Manage discovery content and search-engine metadata.</p></div><a class="btn secondary" href="?page=destinations">Back</a></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form class="panel formgrid" method="post"><input type="hidden" name="csrf" value="<?=csrf()?>">
<label>Destination name<input name="name" maxlength="150" value="<?=e($item['name'])?>" required></label>
<label>Country<input name="country" maxlength="100" value="<?=e($item['country'])?>" required></label>
<label class="wide">URL slug<input name="slug" maxlength="160" value="<?=e($item['slug'])?>" placeholder="Auto-generated if blank"></label>
<label class="wide">Short summary<textarea name="summary" rows="4"><?=e($item['summary'])?></textarea></label>
<label class="wide">Hero image URL / asset path<input name="hero_image" maxlength="255" value="<?=e($item['hero_image'])?>" placeholder="/uploads/destinations/singapore.jpg"></label>
<label>SEO title<input name="seo_title" maxlength="190" value="<?=e($item['seo_title'])?>"></label>
<label>SEO description<textarea name="seo_description" rows="3" maxlength="255"><?=e($item['seo_description'])?></textarea></label>
<label class="toggle"><input type="checkbox" name="featured" <?=$item['featured']?'checked':''?>> Featured</label>
<label class="toggle"><input type="checkbox" name="active" <?=$item['active']?'checked':''?>> Active / visible</label>
<div class="wide actions"><button>Save destination</button></div></form>
<?php else:?>
<div class="title"><div><h1>Destinations</h1><p>Manage destination content, visibility and SEO.</p></div><a class="btn" href="?page=destinations&action=new">+ Add destination</a></div>
<form class="searchbar"><input type="hidden" name="page" value="destinations"><input name="q" value="<?=e($q)?>" placeholder="Search destination or country"><button>Search</button></form>
<section class="panel tablewrap"><table><thead><tr><th>Destination</th><th>Country</th><th>Status</th><th>Featured</th><th>Updated</th><th></th></tr></thead><tbody>
<?php if(!$rows):?><tr><td colspan="6" class="empty">No destinations yet. Add the first one.</td></tr><?php endif;?>
<?php foreach($rows as $row):?><tr><td><b><?=e($row['name'])?></b><small>/<?=e($row['slug'])?></small></td><td><?=e($row['country'])?></td>
<td><span class="badge <?=$row['active']?'green':'muted'?>"><?=$row['active']?'Active':'Hidden'?></span></td><td><?=$row['featured']?'★ Yes':'—'?></td><td><?=e($row['updated_at'])?></td>
<td class="rowactions"><a href="?page=destinations&action=edit&id=<?=$row['id']?>">Edit</a>
<?php if(in_array($_SESSION['admin_role'],['super_admin','admin'],true)):?><form method="post" action="?page=destinations&action=delete" onsubmit="return confirm('Delete this destination?')"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$row['id']?>"><button class="link danger">Delete</button></form><?php endif;?></td></tr><?php endforeach;?>
</tbody></table></section><?php endif;?>
</main></section></body></html>
