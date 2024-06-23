<?php

// 分析数据方法
function analysisData()
{
    // 清空数据
    execute('DELETE FROM TbHospital');


    analysis2023年EDR销量();
    analysis2023年SIG销量();


    analysis2024年1_3月EDR销量();
    analysis2024年1_3月SIG销量();


    analysis2024年EDR指标信息();
    analysis2024年SIG指标信息();


    analysis2024年两产品销量();

    analysis2024年5月销量();

}


function analysis2023年EDR销量()
{
    $path = 'data/2023年EDR销量.txt';
    $list = readData($path);

    orderByDate($list, '业务月');

    foreach ($list as $row) {

        $year = intval(mb_substr($row['业务月'], 0, 4));
        $month = intval(mb_substr($row['业务月'], 4, 2));

        $mgrName = (new Str($row['所属地区']))->between('(', ')');
        $saleName = (new Str($row['所属辖区']))->between('(', ')');
        $hospitalName = $row['医院名称'];
        $productType = 'EDR';
        $monthlySales = floatval($row['金额']);

        $hospital = getHospital($year, $month, $hospitalName, $productType)
            ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

        $hospital['MonthlySales'] += $monthlySales;

        updateName($hospital, $mgrName, $saleName);
        updateHospital($year, $month, $hospitalName, $productType,
            [
                'MonthlySales' => $hospital['MonthlySales']
            ]);
    }
}

function analysis2023年SIG销量()
{
    $path = 'data/2023年SIG销量.txt';
    $list = readData($path);
    orderByDate($list, '业务月');


    foreach ($list as $row) {

        $year = intval(mb_substr($row['业务月'], 0, 4));
        $month = intval(mb_substr($row['业务月'], 4, 2));
        $mgrName = (new Str($row['地区']))->between('(', ')');
        $saleName = (new Str($row['所属辖区']))->between('(', ')');
        $hospitalName = $row['医院名称'];
        $productType = 'SIG';
        $monthlySales = floatval($row['金额']);

        $hospital = getHospital($year, $month, $hospitalName, $productType)
            ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

        $hospital['MonthlySales'] += $monthlySales;

        updateName($hospital, $mgrName, $saleName);
        updateHospital($year, $month, $hospitalName, $productType,
            [
                'MonthlySales' => $hospital['MonthlySales']
            ]);


    }
}

function analysis2024年EDR指标信息()
{
    $path = 'data/2024年EDR指标信息.txt';
    $list = readData($path);


    foreach ($list as $row) {

        $year = 2024;
        $mgrName = (new Str($row['DSM']))->between('(', ')');
        $saleName = (new Str($row['所属辖区']))->between('(', ')');
        $hospitalName = $row['机构名称'];
        $productType = 'EDR';

        for ($month = 1; $month <= 12; $month++) {
            $monthlyVolumePlan = floatval($row[$month . '月指标数量']);
            $monthlySalesPlan = $monthlyVolumePlan * 40.81;

            $hospital = getHospital($year, $month, $hospitalName, $productType)
                ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);


            $hospital['MonthlyVolumePlan'] += $monthlyVolumePlan;
            $hospital['MonthlySalesPlan'] += $monthlySalesPlan;

            updateHospital($year, $month, $hospitalName, $productType,
                [
                    'MonthlyVolumePlan' => $hospital['MonthlyVolumePlan'],
                    'MonthlySalesPlan' => $hospital['MonthlySalesPlan']
                ]);
        }
    }
}

function analysis2024年SIG指标信息()
{
    $path = 'data/2024年SIG指标信息.txt';
    $list = readData($path);

    foreach ($list as $row) {

        $year = 2024;
        $area = new Str($row['辖区']);
        if ($area->contains('一组'))
            $mgrName = '朱戍馨';
        else if ($area->contains('二组'))
            $mgrName = '洪平良';
        else if ($area->contains('三组'))
            $mgrName = '张娜';
        else if ($area->contains('四组'))
            $mgrName = '狄志伟';
        $saleName = $area->between('(', ')');

        $hospitalName = $row['机构名称'];
        $productType = 'SIG';

        // 产品品规
        $productCode = $row['品规编码'];


        for ($month = 1; $month <= 12; $month++) {


            if ($productCode == "SIG0101") // 30片
            {
                $monthlyVolumePlan = floatval($row[$month . '月指标数量']);
                $monthlySalesPlan = $monthlyVolumePlan * 47.63;
            } else if ($productCode == "SIG0102") // 100片
            {
                $monthlyVolumePlan = floatval($row[$month . '月指标数量']);
                $monthlySalesPlan = $monthlyVolumePlan * 155.84;
            } else
                throw new Exception('未知的产品品规' . $productCode);


            $hospital = getHospital($year, $month, $hospitalName, $productType)
                ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

            $hospital['MonthlyVolumePlan'] += $monthlyVolumePlan;
            $hospital['MonthlySalesPlan'] += $monthlySalesPlan;

            updateHospital($year, $month, $hospitalName, $productType,
                [
                    'MonthlyVolumePlan' => $hospital['MonthlyVolumePlan'],
                    'MonthlySalesPlan' => $hospital['MonthlySalesPlan']
                ]);
        }
    }
}

function analysis2024年1_3月EDR销量()
{
    $path = 'data/2024年EDR1-3月销量.txt';
    $list = readData($path);
    orderByDate($list, '业务月');

    foreach ($list as $row) {

        $year = intval(mb_substr($row['业务月'], 0, 4));
        $month = intval(mb_substr($row['业务月'], 4, 2));
        $mgrName = (new Str($row['所属地区']))->between('(', ')');
        $saleName = (new Str($row['所属辖区']))->between('(', ')');
        $hospitalName = $row['医院名称'];
        $productType = 'EDR';
        $monthlySales = floatval($row['金额']);

        $hospital = getHospital($year, $month, $hospitalName, $productType)
            ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

        $hospital['MonthlySales'] += $monthlySales;
        updateName($hospital, $mgrName, $saleName);
        updateHospital($year, $month, $hospitalName, $productType,
            [
                'MonthlySales' => $hospital['MonthlySales']
            ]);
    }

}

function analysis2024年1_3月SIG销量()
{
    $path = 'data/2024年SIG1-3月销量.txt';
    $list = readData($path);
    orderByDate($list, '业务月');

    foreach ($list as $row) {

        $year = intval(mb_substr($row['业务月'], 0, 4));
        $month = intval(mb_substr($row['业务月'], 4, 2));
        $mgrName = (new Str($row['所属地区']))->between('(', ')');
        $saleName = (new Str($row['所属辖区']))->between('(', ')');
        $hospitalName = $row['医院名称'];
        $productType = 'SIG';
        $monthlySales = floatval($row['金额']);


        $hospital = getHospital($year, $month, $hospitalName, $productType)
            ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

        $hospital['MonthlySales'] += $monthlySales;
        updateName($hospital, $mgrName, $saleName);
        updateHospital($year, $month, $hospitalName, $productType,
            [
                'MonthlySales' => $hospital['MonthlySales']
            ]);
    }


}

function analysis2024年两产品销量()
{
    $path = 'data/2024年4月两产品销量.txt';
    $list = readData($path);
    orderByDate($list, '业务月');

    foreach ($list as $row) {

        $year = intval(mb_substr($row['业务月'], 0, 4));
        $month = intval(mb_substr($row['业务月'], 4, 2));

        $area = new Str($row['所属辖区']);
        if ($area->contains('一组'))
            $mgrName = '朱戍馨';
        else if ($area->contains('二组'))
            $mgrName = '洪平良';
        else if ($area->contains('三组'))
            $mgrName = '张娜';
        else if ($area->contains('四组'))
            $mgrName = '狄志伟';
        $saleName = $area->between('(', ')');

        $hospitalName = $row['医院名称'];

        $monthlyVolume = floatval($row['销量']);

        // 产品品规
        $productCode = new Str($row['品规']);
        if ($productCode == 'EDR') {
            $productType = 'EDR';
            $monthlySales = $monthlyVolume * 40.81;
        } else if ($productCode->contains('SIG') && $productCode->contains('30片')) {
            $productType = 'SIG';
            $monthlySales = $monthlyVolume * 47.63;
        } else if ($productCode->contains('SIG') && $productCode->contains('100片')) {
            $productType = 'SIG';
            $monthlySales = $monthlyVolume * 155.84;
        } else {
            throw new Exception("未知的产品品规[$productCode]");
        }


        $hospital = getHospital($year, $month, $hospitalName, $productType)
            ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

        $hospital['MonthlySales'] += $monthlySales;
        updateName($hospital, $mgrName, $saleName);
        updateHospital($year, $month, $hospitalName, $productType,
            [
                'MonthlySales' => $hospital['MonthlySales']
            ]);
    }
}

function analysis2024年5月销量()
{
    $path = 'data/2024年5月销量.txt';
    $list = readData($path);
    orderByDate($list, '业务月');

    foreach ($list as $row) {

        $year = intval(mb_substr($row['业务月'], 0, 4));
        $month = intval(mb_substr($row['业务月'], 4, 2));

        $area = new Str($row['所属辖区']);
        if ($area->contains('一组'))
            $mgrName = '朱戍馨';
        else if ($area->contains('二组'))
            $mgrName = '洪平良';
        else if ($area->contains('三组'))
            $mgrName = '张娜';
        else if ($area->contains('四组'))
            $mgrName = '狄志伟';
        $saleName = $area->between('(', ')');

        $hospitalName = $row['医院名称'];

        $monthlyVolume = floatval($row['销量']);

        // 产品品规
        $productCode = new Str($row['品规']);
        if ($productCode->contains("艾地罗")) { // 艾地罗是edr   喜格迈是sig
            $productType = 'EDR';
            $monthlySales = $monthlyVolume * 40.81;
        } else if ($productCode->contains('喜格迈') && $productCode->contains('30T')) {
            $productType = 'SIG';
            $monthlySales = $monthlyVolume * 47.63;
        } else if ($productCode->contains('喜格迈') && $productCode->contains('100T')) {
            $productType = 'SIG';
            $monthlySales = $monthlyVolume * 155.84;
        } else if ($productCode->contains('格拉诺赛特')) {
            continue;
        } else {
//            throw new Exception("未知的产品品规[$productCode]");
            echo "未知的产品品规[$productCode]<br>";
        }


        $hospital = getHospital($year, $month, $hospitalName, $productType)
            ?: addHospital($year, $month, $hospitalName, $mgrName, $saleName, $productType);

        $hospital['MonthlySales'] += $monthlySales;
        updateName($hospital, $mgrName, $saleName);
        updateHospital($year, $month, $hospitalName, $productType,
            [
                'MonthlySales' => $hospital['MonthlySales']
            ]);
    }
}

// 如果有名字变更，就更新名字
function updateName($hospital, $mgrName, $saleName)
{
    $data = [];
    if ($hospital['MgrName'] != $mgrName) {
        $data['MgrName'] = $mgrName;
    }
    if ($hospital['SaleName'] != $saleName) {
        $data['SaleName'] = $saleName;
    }

    if (!empty($data)) {
        updateHospital($hospital['Year'], $hospital['Month'], $hospital['HospitalName'], $hospital['ProductType'], $data);
    }
}

function orderByDate(&$list, $key)
{
    usort($list, function ($a, $b) use ($key) {
        return $a[$key] <=> $b[$key];
    });
}


function readData($path)
{

    $content = file_get_contents($path);


    /*
    这个$content的格式如下:
    DSM	所属辖区	机构名称	1月指标数量	2月指标数量	3月指标数量	4月指标数量	5月指标数量	6月指标数量	7月指标数量	8月指标数量	9月指标数量	10月指标数量	11月指标数量	12月指标数量
    广州一组DSM(朱戍馨)	广州一组MR(李保国)	东莞东华医院	30	30	30	100	100	122	300	300	300	300	300	300
    广州一组DSM(朱戍馨)	广州一组MR(李保国)	东莞市中医院	0	0	0	0	0	0	0	0	0	50	50	50
    广州一组DSM(朱戍馨)	广州一组MR(李保国)	东莞市万江医院	0	0	0	0	0	0	0	0	0	50	50	50

    需要按行读取，然后按列读取，然后返回一个二维数组, 第一行为标题，后面的行为数据, 我希望关联数组的key是标题，value是数据
    */

    $lines = explode("\n", $content);
    $titles = explode("\t", $lines[0]);
    $data = array();
    for ($i = 1; $i < count($lines); $i++) {
        if (empty($lines[$i]))
            continue;

        $values = explode("\t", $lines[$i]);
        $row = array();
        for ($j = 0; $j < count($titles); $j++) {

            // key去掉空字符, 例如空格, 换行符, 制表符等
            $key = $titles[$j];
            $key = preg_replace('/\s/', '', $key);
            $key = preg_replace('/\n/', '', $key);
            $key = preg_replace('/\t/', '', $key);
            $key = preg_replace('/\r/', '', $key);
            $key = preg_replace('/\v/', '', $key);
            $key = preg_replace('/\f/', '', $key);

            $value = $values[$j];

            // 如果value中ascii字符占到一半以上, 就需要转成半角, 同时去掉,
            if (getAsciiCount($value) > 0.5) {
                $value = fullWidthToHalfWidth($value);
                $value = preg_replace('/,/', '', $value);

//                die("[$value] 已转半角!");
            } else {
//                 die("[$value] 不用转半角!");
            }

            $row[$key] = $value;
        }
        $data[] = $row;
    }
    return $data;
}

function fullWidthToHalfWidth($str)
{
    // 结果字符串
    $res = '';

    for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
        // 获取字符
        $char = mb_substr($str, $i, 1, 'UTF-8');
        // 转换为Unicode编码
        $code = mb_ord($char, 'UTF-8');

        // 全角空格特殊处理
        if ($code == 0x3000) {
            $res .= chr(0x20);
        } // 全角字符（除空格）转换为半角
        elseif ($code >= 0xFF01 && $code <= 0xFF5E) {
            $res .= chr($code - 0xFEE0);
        } // 其他字符保持不变
        else {
            $res .= $char;
        }
    }

    return $res;
}

// 计算一个字符串中的ascii字符的比例
function getAsciiCount($str)
{
    // 正则判断是否是ascii字符
    $pattern = '/[[:ascii:]]/';
    // 计算字符串长度
    $len = mb_strlen($str);

    // 计算ascii字符的数量
    $ascii = preg_match_all($pattern, $str);
    // 计算ascii字符的比例
    return $ascii / $len;

}