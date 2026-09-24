<?php
declare(strict_types=1);

$action=$_GET['action'] ?? 'list';
$id=(int)($_GET['id'] ?? 0);
$error='';
$notice=$_SESSION['notice'] ?? '';
unset($_SESSION['notice']);
$types=['flight','hotel','tour','package'];

$item=['id'=>0,'name'=>'','code'=>'','type'=>'hotel','affiliate_id'=>'','base_url'=>'','active'=>1,'config_json'=>''];

if ($action==='edit' && $id) {
    $s=db()->prepare('SELECT * FROM providers WHERE id=?'); $s->execute([$id]);
    $found=$s->fetch(); if(!$found){http_response_code(404);exit('Provider not found');}
    $item=array_merge($item,$found);
    if (is_array($item['config_json'] ?? null)) $item['config_json']=json_encode($item['config_json'],JSON_PRETTY_PRINT);
}

if ($action==='delete' && $_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    if (in_array($_SESSION['admin_role'] ?? '',['super_admin','admin'],true)) {
        $deleteId=(int)($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM providers WHERE id=?')->execute([$deleteId]);
        audit('delete','provider',$deleteId);
        $_SESSION['notice']='Provider deleted.';
    }
    header('Location: ?page=providers'); exit;
}

if (in_array($action,['new','edit'],true) && $_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
    $item=array_merge($item,[
        'name'=>trim($_POST['name'] ?? ''),
        'code'=>strtolower(trim(preg_replace('/[^a-zA-Z0-9_-]+/','-',$_POST['code'] ?? ''),'-')),
        'type'=>$_POST['type'] ?? '',
        'affiliate_id'=>trim($_POST['affiliate_id'] ?? ''),
        'base_url'=>trim($_POST['base_url'] ?? ''),
        'active'=>isset($_POST['active'])?1:0,
        'config_json'=>trim($_POST['config_json'] ?? '')
    ]);
    if($item['name']==='' || $item['code']==='' || !in_array($item['type'],$types,true)) $error='Name, code and a valid provider type are required.';
    if(!$error && $item['base_url']!=='' && !filter_var($item['base_url'],FILTER_VALIDATE_URL)) $error='Base URL must be a valid URL.';
    $json=null;
    if(!$error && $item['config_json']!==''){
        json_decode($item['config_json'],true);
        if(json_last_error()!==JSON_ERROR_NONE) $error='Provider configuration must be valid JSON.';
        else $json=$item['config_json'];
    }
    if(!$error){
        try{
            if($id){
                $s=db()->prepare('UPDATE providers SET name=?,code=?,type=?,affiliate_id=?,base_url=?,active=?,config_json=? WHERE id=?');
                $s->execute([$item['name'],$item['code'],$item['type'],$item['affiliate_id'],$item['base_url'],$item['active'],$json,$id]);
                audit('update','provider',$id); $_SESSION['notice']='Provider updated.';
            } else {
                $s=db()->prepare('INSERT INTO providers(name,code,type,affiliate_id,base_url,active,config_json) VALUES(?,?,?,?,?,?,?)');
                $s->execute([$item['name'],$item['code'],$item['type'],$item['affiliate_id'],$item['base_url'],$item['active'],$json]);
                $id=(int)db()->lastInsertId(); audit('create','provider',$id); $_SESSION['notice']='Provider created.';
            }
            header('Location: ?page=providers'); exit;
        }catch(PDOException $e){$error='Provider code must be unique.';}
    }
}

$q=trim($_GET['q'] ?? '');
$type=trim($_GET['type'] ?? '');
$sql='SELECT * FROM providers WHERE 1=1'; $args=[];
if($q!==''){$sql.=' AND (name LIKE ? OR code LIKE ?)';$args[]='%'.$q.'%';$args[]='%'.$q.'%';}
if(in_array($type,$types,true)){$sql.=' AND type=?';$args[]=$type;}
$sql.=' ORDER BY type,name';
$s=db()->prepare($sql);$s->execute($args);$rows=$s->fetchAll();

require __DIR__.'/../views/providers.php';
