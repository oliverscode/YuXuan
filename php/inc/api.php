<?php
require_once 'db.php';

/** 添加一个医院
 * @param $year
 * @param $month
 * @param $hospitalName
 * @param $mgrName
 * @param $saleName
 * @param $productType
 * @return bool
 */
function addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType)
{
    $sql = 'INSERT INTO TbHospital(year, month, hospitalName, mgrName, saleName, productType) VALUES(?, ?, ?, ?, ?, ?)';
    if (!execute($sql, array($year, $month, $hospitalName, $mgrName, $saleName, $productType))) {
        throw new Exception('添加医院失败');
    }
    return getHospital($year, $month, $hospitalName, $productType);
}

/** 获取一个医院
 * @param $year
 * @param $month
 * @param $hospitalName
 * @param $productType
 * @return array|false
 */
function getHospital($year, $month, $hospitalName, $productType)
{
    $sql = 'SELECT * FROM TbHospital WHERE year = ? AND month = ? AND hospitalName = ? AND productType = ? LIMIT 1';
    $result = fetchAll($sql, array($year, $month, $hospitalName, $productType));
    return empty($result) ? false : $result[0];
}

function updateHospital($year, $month, $hospitalName, $productType, $data)
{

    $sql = 'UPDATE TbHospital SET ';
    $params = array();
    foreach ($data as $key => $value) {
        $sql .= $key . ' = ?, ';
        $params[] = $value;
    }
    $sql = substr($sql, 0, strlen($sql) - 2);
    $sql .= ' WHERE year = ? AND month = ? AND hospitalName = ? AND productType = ?';
    $params[] = $year;
    $params[] = $month;
    $params[] = $hospitalName;
    $params[] = $productType;

    if (!execute($sql, $params)) {
        throw new Exception('更新医院失败');
    }
}
