<?php
// 连接pdo数据库, 数据库为sqlite, 路径为db.sqlite
$pdo = new PDO('sqlite:db.sqlite');
// 设置错误处理方式为异常
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 设置数据库的模式为持久化
$pdo->exec('PRAGMA journal_mode = WAL');
// 设置数据库的缓存大小为20M
$pdo->exec('PRAGMA cache_size = 20000');


function fetchAll($sql, $params = array())
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function execute($sql, $params = array()): int
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $affectedRows = $stmt->rowCount();
    return $affectedRows;
}

