<?php

require_once 'app.php';


//$analysis = new analysis();
//$analysis->clearDb();
//
//$analysis->plan();
//$analysis->sale();
//$analysis->pay();

global $config;
$config['CACHE'] = false;
$export = new Export();
$data = $export->payMgr(2024);
die($data);