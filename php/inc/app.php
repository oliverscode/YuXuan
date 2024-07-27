<?php
// 仅仅屏蔽警告
error_reporting(E_ERROR);


require_once 'pingfan.kit.php';

require_once 'config.php';
require_once 'product.php';
require_once 'mgr.php';
require_once 'sale.php';
require_once 'cost.php';

require_once 'csvread.php';
require_once 'analysis.php';
require_once 'export.php';


//require_once 'api.php';
//require_once 'util.php';
//require_once 'analysis.php';
//require_once 'export.php';


$req = new Req();
$res = new Res();
$session = new Session();
$auth = new Auth();
$cache = new FileCache();
$log = new Log();

$url = Path::getUrl();
$ip = $req->ip();
$log->debug("访问页面: $url, Ip: $ip");


$dbPath = Path::combineFromServerRoot('inc', 'db.sqlite');
$db = new Orm("sqlite:$dbPath");
