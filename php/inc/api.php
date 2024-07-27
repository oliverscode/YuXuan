<?php


/** 添加一个医院
 * @param $year
 * @param $month
 * @param $hospitalName
 * @param $mgrName
 * @param $saleName
 * @param $productName
 * @return bool
 */
function addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productName)
{
    global $db;
    $sql = 'INSERT INTO TbHospital(year, month, hospitalName, mgrName, saleName, productName) VALUES(?, ?, ?, ?, ?, ?)';
    $db->execute($sql, array($year, $month, $hospitalName, $mgrName, $saleName, $productName), 1);
    return getHospital($year, $month, $hospitalName, $productName);
}

/** 获取一个医院
 * @param $year
 * @param $month
 * @param $hospitalName
 * @param $productName
 * @return array|false
 */
function getHospital($year, $month, $hospitalName, $productName)
{
    global $db;
    $sql = 'SELECT * FROM TbHospital WHERE year = ? AND month = ? AND hospitalName = ? AND productName = ? LIMIT 1';
    $result = $db->query($sql, array($year, $month, $hospitalName, $productName));
    return empty($result) ? false : $result[0];
}

function updateHospital($year, $month, $hospitalName, $productName, $data)
{

    global $db;
    $sql = 'UPDATE TbHospital SET ';
    $params = array();
    foreach ($data as $key => $value) {
        $sql .= $key . ' = ?, ';
        $params[] = $value;
    }
    $sql = substr($sql, 0, strlen($sql) - 2);
    $sql .= ' WHERE year = ? AND month = ? AND hospitalName = ? AND productName = ?';
    $params[] = $year;
    $params[] = $month;
    $params[] = $hospitalName;
    $params[] = $productName;

    $db->execute($sql, $params, 1);
}
