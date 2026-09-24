<?php
declare(strict_types=1);

function destination_slug(string $value): string {
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
    return substr(strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $value), '-')), 0, 160);
}
function unique_destination_slug(string $slug, int $ignoreId = 0): string {
    $base = $slug ?: 'destination'; $candidate = $base; $i = 2;
    while (true) {
        $sql = 'SELECT id FROM destinations WHERE slug=?' . ($ignoreId ? ' AND id<>?' : '') . ' LIMIT 1';
        $stmt = db()->prepare($sql); $stmt->execute($ignoreId ? [$candidate,$ignoreId] : [$candidate]);
        if (!$stmt->fetch()) return $candidate;
        $candidate = substr($base,0,150) . '-' . $i++;
    }
}

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$error = '';
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']);

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (in_array($_SESSION['admin_role'] ?? '', ['super_admin','admin'], true)) {
        $id = (int)($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM destinations WHERE id=?')->execute([$id]);
        audit('delete','destination',$id);
        $_SESSION['notice'] = 'Destination deleted.';
    }
    header('Location: ?page=destinations'); exit;
}

$item = ['id'=>0,'name'=>'','country'=>'','slug'=>'','summary'=>'','hero_image'=>'','featured'=>0,'active'=>1,'seo_title'=>'','seo_description'=>''];

if ($action === 'edit' && $id) {
    $stmt=db()->prepare('SELECT * FROM destinations WHERE id=?'); $stmt->execute([$id]);
    $found=$stmt->fetch();
    if (!$found) { http_response_code(404); exit('Destination not found'); }
    $item=array_merge($item,$found);
}

if (in_array($action,['new','edit'],true) && $_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    $item=array_merge($item,[
        'name'=>trim($_POST['name'] ?? ''), 'country'=>trim($_POST['country'] ?? ''),
        'slug'=>trim($_POST['slug'] ?? ''), 'summary'=>trim($_POST['summary'] ?? ''),
        'hero_image'=>trim($_POST['hero_image'] ?? ''), 'featured'=>isset($_POST['featured'])?1:0,
        'active'=>isset($_POST['active'])?1:0, 'seo_title'=>trim($_POST['seo_title'] ?? ''),
        'seo_description'=>trim($_POST['seo_description'] ?? '')
    ]);
    if ($item['name']==='' || $item['country']==='') $error='Destination name and country are required.';
    if (!$error) {
        $item['slug']=unique_destination_slug(destination_slug($item['slug'] ?: $item['name']),$id);
        if ($id) {
            $stmt=db()->prepare('UPDATE destinations SET name=?,country=?,slug=?,summary=?,hero_image=?,featured=?,active=?,seo_title=?,seo_description=? WHERE id=?');
            $stmt->execute([$item['name'],$item['country'],$item['slug'],$item['summary'],$item['hero_image'],$item['featured'],$item['active'],$item['seo_title'],$item['seo_description'],$id]);
            audit('update','destination',$id); $_SESSION['notice']='Destination updated.';
        } else {
            $stmt=db()->prepare('INSERT INTO destinations(name,country,slug,summary,hero_image,featured,active,seo_title,seo_description) VALUES(?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$item['name'],$item['country'],$item['slug'],$item['summary'],$item['hero_image'],$item['featured'],$item['active'],$item['seo_title'],$item['seo_description']]);
            $id=(int)db()->lastInsertId(); audit('create','destination',$id); $_SESSION['notice']='Destination created.';
        }
        header('Location: ?page=destinations'); exit;
    }
}

$q=trim($_GET['q'] ?? '');
if ($q!=='') {
    $stmt=db()->prepare('SELECT * FROM destinations WHERE name LIKE ? OR country LIKE ? ORDER BY name');
    $stmt->execute(['%'.$q.'%','%'.$q.'%']); $rows=$stmt->fetchAll();
} else {
    $rows=db()->query('SELECT * FROM destinations ORDER BY name')->fetchAll();
}
require __DIR__ . '/../views/destinations.php';
