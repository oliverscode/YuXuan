<?php

function exportHospital($year, $month, $productType)
{
    $list = fetchAll('SELECT * FROM TbHospital');

    $hospitals = [];
    foreach ($list as $row) {
        $hospitals[$row['HospitalName']][] = $row;
    }
    $lines = "医院名称,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月销售金额,当月达成百分比,全年占比贡献,当月占比贡献,同期增长,销售名,经理名\n";

    foreach ($hospitals as $hospital) {
        $hospitalName = '';
        $totalSales = 0;
        $totalTargetAmount = 0;
        $totalTargetQuantity = 0;
        $ytdAchievement = 0;
        $monthlyTargetAmount = 0;
        $monthlySales = 0;
        $monthlyAchievement = 0;
        $totalContribution = 0;
        $monthlyContribution = 0;
        $growth = 0;
        $mgrName = '';
        $saleName = '';

        // 所有医院, 当前产品全年销售额
        $allSales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $allSales += $row['MonthlySales'];
            }
        }
        // 所有医院当前月份销售额
        $allMonthlySales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $allMonthlySales += $row['MonthlySales'];
            }
        }


        // 医院名称
        $hospitalName = $hospital[0]['HospitalName'];

        // 全年销售额, 只计算当年且当前产品类型的数据
        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalSales += $row['MonthlySales'];
            }
        }

        // 全年指标金额, 只计算当年且当前产品类型的数据
        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalTargetAmount += $row['MonthlySalesPlan'];
            }
        }
        // 全年指标数量, 只计算当年且当前产品类型的数据
        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalTargetQuantity += $row['MonthlyVolumePlan'];
            }
        }

        // YTD达成百分比, 只计算当年且当前产品类型的数据, 保留两位小数
        if (!isZero($totalTargetAmount)) {
            $ytdAchievement = $totalSales / $totalTargetAmount * 100;
        }

        // 当月指标金额
        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $monthlyTargetAmount += $row['MonthlySalesPlan'];
            }
        }

        // 当月销售金额
        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $monthlySales += $row['MonthlySales'];
            }
        }

        // 当月达成百分比, 保留两位小数
        if (!isZero($monthlyTargetAmount)) {
            $monthlyAchievement = $monthlySales / $monthlyTargetAmount * 100;
        }

        // 全年占比贡献, 本家医院除以本年所有医院的销售额
        if (!isZero($totalSales)) {
            $totalContribution = 100.0 * $totalSales / $allSales;
        }

        // 当月占比贡献, 本家医院除以本月所有医院的销售额
        if (!isZero($monthlySales)) {
            $monthlyContribution = 100.0 * $monthlySales / $allMonthlySales;
        }

        // 同期增长, 去年的这个时候, 保留两位小数
        $lastYear = $year - 1;
        $lastMonth = $month;
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastYear -= 1;
        }
        $lastYearSales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $lastYear && $row['Month'] == $lastMonth && $row['ProductType'] == $productType && $row['HospitalName'] == $hospitalName) {
                $lastYearSales += $row['MonthlySales'];
            }
        }
        if (!isZero($lastYearSales)) {
            $growth = 100.0 * ($monthlySales / $lastYearSales - 1);
        }

        // 销售名, 先按照年份倒序,再按照月份倒序,再取非空第一个
        $saleName = getLastName($hospital, $year, $month, $productType, 'SaleName');


        // 经理名, 先按照年份倒序,再按照月份倒序,再取非空第一个
        $mgrName = getLastName($hospital, $year, $month, $productType, 'MgrName');


        // 全部保留2位小数
        $totalSales = round($totalSales, 2);
        $totalTargetAmount = round($totalTargetAmount, 2);
        $totalTargetQuantity = round($totalTargetQuantity, 2);
        $ytdAchievement = round($ytdAchievement, 2);
        $monthlyTargetAmount = round($monthlyTargetAmount, 2);
        $monthlySales = round($monthlySales, 2);
        $monthlyAchievement = round($monthlyAchievement, 2);
        $totalContribution = round($totalContribution, 2);
        $monthlyContribution = round($monthlyContribution, 2);
        $growth = round($growth, 2);


        $lines .= "$hospitalName,$totalSales,$totalTargetAmount,$totalTargetQuantity,$ytdAchievement,$monthlyTargetAmount,$monthlySales,$monthlyAchievement,$totalContribution,$monthlyContribution,$growth,$mgrName,$saleName\n";
    }

    return $lines;

}

function exportPeople($year, $month, $productType, $peopleType)
{
    if ($peopleType == 'mgr') {
        return exportMgr($year, $month, $productType);
    } else if ($peopleType == 'sale') {
        return exportSale($year, $month, $productType);
    } else {
        throw new Exception('未知的人员类型');
    }
}

function exportMgr($year, $month, $productType)
{
    $list = fetchAll('SELECT * FROM TbHospital');

    $mgrs = [];
    foreach ($list as $row) {
        $mgrName = $row['MgrName'];
        if (empty($mgrName))
            continue;
        $mgrs[$mgrName][] = $row;
    }
    $lines = "经理名,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月销售金额,当月达成百分比,${productType}全年占比贡献,${productType}当月占比贡献,同期增长,医院列表,组员列表\n";

    foreach ($mgrs as $mgr) {
        $mgrName = $mgr[0]['MgrName'];
        $totalSales = 0;
        $totalTargetAmount = 0;
        $totalTargetQuantity = 0;
        $ytdAchievement = 0;
        $monthlyTargetAmount = 0;
        $monthlySales = 0;
        $monthlyAchievement = 0;
        $totalContribution = 0;
        $monthlyContribution = 0;
        $growth = 0;
        $hospitals = [];
        $sales = [];


        // 所有经理, 当前产品全年销售额
        $allSales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $allSales += $row['MonthlySales'];
            }
        }

        // 所有经理当前月份销售额
        $allMonthlySales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $allMonthlySales += $row['MonthlySales'];
            }
        }

        // 全年销售额, 只计算当年且当前产品类型的数据
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalSales += $row['MonthlySales'];
            }
        }

        // 全年指标金额, 只计算当年且当前产品类型的数据
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalTargetAmount += $row['MonthlySalesPlan'];
            }
        }
        // 全年指标数量, 只计算当年且当前产品类型的数据
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalTargetQuantity += $row['MonthlyVolumePlan'];
            }
        }

        // YTD达成百分比, 只计算当年且当前产品类型的数据, 保留两位小数
        if (!isZero($totalTargetAmount)) {
            $ytdAchievement = $totalSales / $totalTargetAmount * 100;
        }

        // 当月指标金额
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $monthlyTargetAmount += $row['MonthlySalesPlan'];
            }
        }


        // 当月销售金额
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $monthlySales += $row['MonthlySales'];
            }
        }

        // 当月达成百分比, 保留两位小数
        if (!isZero($monthlyTargetAmount)) {
            $monthlyAchievement = $monthlySales / $monthlyTargetAmount * 100;
        }

        // 全年占比贡献, 本经理除以本年所有经理的销售额
        if (!isZero($totalSales)) {
            $totalContribution = 100.0 * $totalSales / $allSales;
        }

        // 当月占比贡献, 本家医院除以本月所有医院的销售额
        if (!isZero($monthlySales)) {
            $monthlyContribution = 100.0 * $monthlySales / $allMonthlySales;
        }

        // 同期增长, 去年的这个时候, 保留两位小数
        $lastYear = $year - 1;
        $lastMonth = $month;
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastYear -= 1;
        }
        $lastYearSales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $lastYear && $row['Month'] == $lastMonth && $row['ProductType'] == $productType && $row['MgrName'] == $mgrName) {
                $lastYearSales += $row['MonthlySales'];
            }
        }
        if (!isZero($lastYearSales)) {
            $growth = 100.0 * ($monthlySales / $lastYearSales - 1);
        }

        // 医院列表
        foreach ($mgr as $row) {
            // 判断是否重复
            if (in_array($row['HospitalName'], $hospitals) == false)
                $hospitals[] = $row['HospitalName'];
        }

        // 组员列表
        foreach ($mgr as $row) {
            // 判断是否重复
            if (in_array($row['SaleName'], $sales) == false)
                $sales[] = $row['SaleName'];
        }


        // 全部保留2位小数
        $totalSales = round($totalSales, 2);
        $totalTargetAmount = round($totalTargetAmount, 2);
        $totalTargetQuantity = round($totalTargetQuantity, 2);
        $ytdAchievement = round($ytdAchievement, 2);
        $monthlyTargetAmount = round($monthlyTargetAmount, 2);
        $monthlySales = round($monthlySales, 2);
        $monthlyAchievement = round($monthlyAchievement, 2);
        $totalContribution = round($totalContribution, 2);
        $monthlyContribution = round($monthlyContribution, 2);
        $growth = round($growth, 2);

        $hospitals = implode('&', $hospitals);
        $sales = implode('&', $sales);

        $lines .= "$mgrName,$totalSales,$totalTargetAmount,$totalTargetQuantity,$ytdAchievement,$monthlyTargetAmount,$monthlySales,$monthlyAchievement,$totalContribution,$monthlyContribution,$growth,$hospitals,$sales\n";
    }

    return $lines;
}

function exportSale($year, $month, $productType)
{
    $list = fetchAll('SELECT * FROM TbHospital');

    $sales = [];
    foreach ($list as $row) {
        $saleName = $row['SaleName'];
        if (empty($saleName))
            continue;
        $sales[$saleName][] = $row;
    }
    $lines = "组员名,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月销售金额,当月达成百分比,${productType}全年占比贡献,${productType}当月占比贡献,同期增长,经理名,医院列表\n";

    foreach ($sales as $mgr) {
        $saleName = $mgr[0]['SaleName'];
        $mgrName = $mgr[0]['MgrName'];
        $totalSales = 0;
        $totalTargetAmount = 0;
        $totalTargetQuantity = 0;
        $ytdAchievement = 0;
        $monthlyTargetAmount = 0;
        $monthlySales = 0;
        $monthlyAchievement = 0;
        $totalContribution = 0;
        $monthlyContribution = 0;
        $growth = 0;
        $hospitals = [];
        $sales = [];


        // 所有组员, 当前产品全年销售额
        $allSales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $allSales += $row['MonthlySales'];
            }
        }

        // 所有组员当前月份销售额
        $allMonthlySales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $allMonthlySales += $row['MonthlySales'];
            }
        }

        // 全年销售额, 只计算当年且当前产品类型的数据
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalSales += $row['MonthlySales'];
            }
        }

        // 全年指标金额, 只计算当年且当前产品类型的数据
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalTargetAmount += $row['MonthlySalesPlan'];
            }
        }
        // 全年指标数量, 只计算当年且当前产品类型的数据
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['ProductType'] == $productType) {
                $totalTargetQuantity += $row['MonthlyVolumePlan'];
            }
        }

        // YTD达成百分比, 只计算当年且当前产品类型的数据, 保留两位小数
        if (!isZero($totalTargetAmount)) {
            $ytdAchievement = $totalSales / $totalTargetAmount * 100;
        }

        // 当月指标金额
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $monthlyTargetAmount += $row['MonthlySalesPlan'];
            }
        }


        // 当月销售金额
        foreach ($mgr as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $monthlySales += $row['MonthlySales'];
            }
        }

        // 当月达成百分比, 保留两位小数
        if (!isZero($monthlyTargetAmount)) {
            $monthlyAchievement = $monthlySales / $monthlyTargetAmount * 100;
        }

        // 全年占比贡献, 本经理除以本年所有经理的销售额
        if (!isZero($totalSales)) {
            $totalContribution = 100.0 * $totalSales / $allSales;
        }

        // 当月占比贡献, 本家医院除以本月所有医院的销售额
        if (!isZero($monthlySales)) {
            $monthlyContribution = 100.0 * $monthlySales / $allMonthlySales;
        }

        // 同期增长, 去年的这个时候, 保留两位小数
        $lastYear = $year - 1;
        $lastMonth = $month;
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastYear -= 1;
        }
        $lastYearSales = 0;
        foreach ($list as $row) {
            if ($row['Year'] == $lastYear && $row['Month'] == $lastMonth && $row['ProductType'] == $productType && $row['SaleName'] == $saleName) {
                $lastYearSales += $row['MonthlySales'];
            }
        }
        if (!isZero($lastYearSales)) {
            $growth = 100.0 * ($monthlySales / $lastYearSales - 1);
        }

        // 医院列表
        foreach ($mgr as $row) {
            // 判断是否重复
            if (in_array($row['HospitalName'], $hospitals) == false)
                $hospitals[] = $row['HospitalName'];
        }


        // 全部保留2位小数
        $totalSales = round($totalSales, 2);
        $totalTargetAmount = round($totalTargetAmount, 2);
        $totalTargetQuantity = round($totalTargetQuantity, 2);
        $ytdAchievement = round($ytdAchievement, 2);
        $monthlyTargetAmount = round($monthlyTargetAmount, 2);
        $monthlySales = round($monthlySales, 2);
        $monthlyAchievement = round($monthlyAchievement, 2);
        $totalContribution = round($totalContribution, 2);
        $monthlyContribution = round($monthlyContribution, 2);
        $growth = round($growth, 2);

        $hospitals = implode('&', $hospitals);

        $lines .= "$saleName,$totalSales,$totalTargetAmount,$totalTargetQuantity,$ytdAchievement,$monthlyTargetAmount,$monthlySales,$monthlyAchievement,$totalContribution,$monthlyContribution,$growth,$mgrName,$hospitals\n";
    }

    return $lines;
}

function isZero($float, $epsilon = 0.00001)
{
    return abs($float) < $epsilon;
}

function getLastName($hospital, $year, $month, $productType, $key)
{

    $name = '';
    for ($i = 0; $i < 12 * 3; $i++) {
        if (empty($name) == false)
            break;

        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productType) {
                $name = $row[$key];
                break;
            }
        }

        $month--;
        if ($month == 0) {
            $month = 12;
            $year--;
        }
    }
    if (empty($name))
        $name = '';
    return $name;
}