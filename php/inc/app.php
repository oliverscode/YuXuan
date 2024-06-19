<?php
// 屏蔽警告
error_reporting(E_ERROR | E_PARSE);
require_once 'vendor/autoload.php';
require_once 'db.php';
require_once 'api.php';
require_once 'util.php';


// 分析数据方法
function analysisData()
{
    // 清空数据
    execute('DELETE FROM TbHospital');

    analysis2023年EDR销量();
    analysis2023年SIG销量();

    analysis2024年EDR指标信息();
    analysis2024年SIG指标信息();


    analysis2024年1_3月EDR销量();
    analysis2024年1_3月SIG销量();
    analysis2024年两产品销量();
    die('数据分析完成');


}


function analysis2023年EDR销量()
{
    $path = 'data/2023年EDR销量.txt';
    $list = readData($path);

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

        $hospital['monthlySales'] += $monthlySales;

        updateHospital($year, $month, $hospitalName, $productType,
            [
                'monthlySales' => $hospital['monthlySales']
            ]);
    }
}

function analysis2023年SIG销量()
{
    $path = 'data/2023年SIG销量.txt';
    $list = readData($path);
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

        $hospital['monthlySales'] += $monthlySales;

        updateHospital($year, $month, $hospitalName, $productType,
            [
                'monthlySales' => $hospital['monthlySales']
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

        $hospital['monthlySales'] += $monthlySales;

        updateHospital($year, $month, $hospitalName, $productType,
            [
                'monthlySales' => $hospital['monthlySales']
            ]);
    }

}

function analysis2024年1_3月SIG销量()
{
    $path = 'data/2024年SIG1-3月销量.txt';
    $list = readData($path);

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

        $hospital['monthlySales'] += $monthlySales;

        updateHospital($year, $month, $hospitalName, $productType,
            [
                'monthlySales' => $hospital['monthlySales']
            ]);
    }

}

function analysis2024年两产品销量()
{
    $path = 'data/2024年4月两产品销量.txt';
    $list = readData($path);


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

        $hospital['monthlySales'] += $monthlySales;

        updateHospital($year, $month, $hospitalName, $productType,
            [
                'monthlySales' => $hospital['monthlySales']
            ]);
    }
}