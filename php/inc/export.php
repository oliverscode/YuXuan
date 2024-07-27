<?php

const CACHET = 60 * 60 * 24 * 365;

class Export
{

    public function hospital($year, $month, $productName): string
    {
        global $config, $cache;

        if (!$config['CACHE']) {
            return $this->hospitalSource($year, $month, $productName);
        }

        $key = "hospital_${year}_${month}_${productName}";
        return $cache->getOrSet($key, fn() => $this->hospitalSource($year, $month, $productName), CACHET);

    }

    /** YTD (YEAR OF TODAY) 是指今年开始至今, 和全年有少许差别 */
    private function hospitalSource($year, $month, $productName): string
    {
        $lines = "医院名称,全年销售额,全年指标金额,全年占比贡献,YTD达成百分比,当月销售金额,当月指标金额,当月达成百分比,当月占比贡献,同期增长,地区经理,销售代表\n";
        $all = Sale::all()->where(fn($x) => $x['ProductName'] == $productName);


        // 全部医院全年销售金额
        $allAmountOfYear = $all->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['Amount']);

        // 全部医院当月销售金额
        $allAmountOfMonth = $all->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);

        $hospitals = $all->groupBy(fn($x) => $x['HospitalName']);
        foreach ($hospitals as $hospital) {
            $linqHospital = new Linq($hospital);
            $hospitalName = $hospital[0]['HospitalName'];
            // 全年销售额
            $amountOfYear = $linqHospital->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['Amount']);

            // 全年指标金额
            $planAmountOfYear = $linqHospital->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['PlanAmount']);

            // 全年占比贡献
            $contributionOfYear = self::div($amountOfYear, $allAmountOfYear) * 100;

            // YTD达成百分比 至今销售金额 / 全年指标金额
            $ytd = self::div($amountOfYear, $planAmountOfYear) * 100;

            // 当月销售金额
            $amountOfMonth = $linqHospital->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);

            // 当月指标金额
            $planAmountOfMonth = $linqHospital->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['PlanAmount']);

            // 当月达成百分比
            $finishOfMonth = self::div($amountOfMonth, $planAmountOfMonth) * 100;

            // 当月占比贡献
            $contributionOfMonth = self::div($amountOfMonth, $allAmountOfMonth) * 100;

            // 同期增长
            $amountOfLastYearMonth = $linqHospital->where(fn($x) => $x['Year'] == $year - 1 && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);
            $growth = (self::div($planAmountOfMonth, $amountOfLastYearMonth) - 1) * 100;

            // 地区经理
            $mgrNames = self::getNames($hospital, 'MgrName');
            // 销售代表
            $saleNames = self::getNames($hospital, 'SaleName');

            // 所有数值类保留2位小数
            $amountOfYear = round($amountOfYear, 2);
            $planAmountOfYear = round($planAmountOfYear, 2);
            $contributionOfYear = round($contributionOfYear, 2);
            $ytd = round($ytd, 2);
            $amountOfMonth = round($amountOfMonth, 2);
            $planAmountOfMonth = round($planAmountOfMonth, 2);
            $finishOfMonth = round($finishOfMonth, 2);
            $contributionOfMonth = round($contributionOfMonth, 2);
            $growth = round($growth, 2);
            $lines .= "$hospitalName,$amountOfYear,$planAmountOfYear,$contributionOfYear%,$ytd%,$amountOfMonth,$planAmountOfMonth,$finishOfMonth%,$contributionOfMonth%,$growth%,$mgrNames,$saleNames\n";

        }

        return $lines;
    }


    public function mgr($year, $month, $productName): string
    {
        global $config, $cache;
        if (!$config['CACHE']) {
            return $this->mgrSource($year, $month, $productName);
        }

        $key = "mgr_${year}_${month}_${productName}";
        return $cache->getOrSet($key, fn() => $this->mgrSource($year, $month, $productName), CACHET);
    }

    private function mgrSource($year, $month, $productName): string
    {
        $lines = "地区经理,全年销售额,全年指标金额,全年占比贡献,YTD达成百分比,当月销售金额,当月指标金额,当月达成百分比,当月占比贡献,同期增长\n";
        $all = Sale::all()->where(fn($x) => $x['ProductName'] == $productName);

        // 全部医院全年销售金额
        $allAmountOfYear = $all->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['Amount']);

        // 全部医院当月销售金额
        $allAmountOfMonth = $all->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);

        $mgrs = $all->groupBy(fn($x) => $x['MgrName']);
        foreach ($mgrs as $mgr) {
            $linqMgr = new Linq($mgr);
            $mgrName = $mgr[0]['MgrName'];

            // 全年销售额
            $amountOfYear = $linqMgr->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['Amount']);

            // 全年指标金额
            $planAmountOfYear = $linqMgr->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['PlanAmount']);

            // 全年占比贡献
            $contributionOfYear = self::div($amountOfYear, $allAmountOfYear) * 100;

            // YTD达成百分比 至今销售金额 / 全年指标金额
            $ytd = self::div($amountOfYear, $planAmountOfYear) * 100;

            // 当月销售金额
            $amountOfMonth = $linqMgr->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);

            // 当月指标金额
            $planAmountOfMonth = $linqMgr->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['PlanAmount']);

            // 当月达成百分比
            $finishOfMonth = self::div($amountOfMonth, $planAmountOfMonth) * 100;

            // 当月占比贡献
            $contributionOfMonth = self::div($amountOfMonth, $allAmountOfMonth) * 100;

            // 同期增长
            $amountOfLastYearMonth = $linqMgr->where(fn($x) => $x['Year'] == $year - 1 && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);
            $growth = (self::div($planAmountOfMonth, $amountOfLastYearMonth) - 1) * 100;


            // 所有数值类保留2位小数
            $amountOfYear = round($amountOfYear, 2);
            $planAmountOfYear = round($planAmountOfYear, 2);
            $contributionOfYear = round($contributionOfYear, 2);
            $ytd = round($ytd, 2);
            $amountOfMonth = round($amountOfMonth, 2);
            $planAmountOfMonth = round($planAmountOfMonth, 2);
            $finishOfMonth = round($finishOfMonth, 2);
            $contributionOfMonth = round($contributionOfMonth, 2);
            $growth = round($growth, 2);
            $lines .= "$mgrName,$amountOfYear,$planAmountOfYear,$contributionOfYear%,$ytd%,$amountOfMonth,$planAmountOfMonth,$finishOfMonth%,$contributionOfMonth%,$growth%\n";

        }

        return $lines;
    }


    public function sale($year, $month, $productName): string
    {
        global $config, $cache;
        if (!$config['CACHE']) {
            return $this->mgrSale($year, $month, $productName);
        }

        $key = "sale_${year}_${month}_${productName}";
        return $cache->getOrSet($key, fn() => $this->mgrSale($year, $month, $productName), CACHET);
    }

    private function mgrSale($year, $month, $productName): string
    {
        $lines = "销售代表,全年销售额,全年指标金额,全年占比贡献,YTD达成百分比,当月销售金额,当月指标金额,当月达成百分比,当月占比贡献,同期增长,地区经理\n";
        $all = Sale::all()->where(fn($x) => $x['ProductName'] == $productName);

        // 全部医院全年销售金额
        $allAmountOfYear = $all->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['Amount']);

        // 全部医院当月销售金额
        $allAmountOfMonth = $all->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);

        $sales = $all->groupBy(fn($x) => $x['SaleName']);
        foreach ($sales as $sale) {
            $linqSale = new Linq($sale);
            $saleName = $sale[0]['SaleName'];
            $mgrName = $sale[0]['MgrName'];

            // 全年销售额
            $amountOfYear = $linqSale->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['Amount']);

            // 全年指标金额
            $planAmountOfYear = $linqSale->where(fn($x) => $x['Year'] == $year)->sum(fn($x) => $x['PlanAmount']);

            // 全年占比贡献
            $contributionOfYear = self::div($amountOfYear, $allAmountOfYear) * 100;

            // YTD达成百分比 至今销售金额 / 全年指标金额
            $ytd = self::div($amountOfYear, $planAmountOfYear) * 100;

            // 当月销售金额
            $amountOfMonth = $linqSale->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);

            // 当月指标金额
            $planAmountOfMonth = $linqSale->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)->sum(fn($x) => $x['PlanAmount']);

            // 当月达成百分比
            $finishOfMonth = self::div($amountOfMonth, $planAmountOfMonth) * 100;

            // 当月占比贡献
            $contributionOfMonth = self::div($amountOfMonth, $allAmountOfMonth) * 100;

            // 同期增长
            $amountOfLastYearMonth = $linqSale->where(fn($x) => $x['Year'] == $year - 1 && $x['Month'] == $month)->sum(fn($x) => $x['Amount']);
            $growth = (self::div($planAmountOfMonth, $amountOfLastYearMonth) - 1) * 100;


            // 所有数值类保留2位小数
            $amountOfYear = round($amountOfYear, 2);
            $planAmountOfYear = round($planAmountOfYear, 2);
            $contributionOfYear = round($contributionOfYear, 2);
            $ytd = round($ytd, 2);
            $amountOfMonth = round($amountOfMonth, 2);
            $planAmountOfMonth = round($planAmountOfMonth, 2);
            $finishOfMonth = round($finishOfMonth, 2);
            $contributionOfMonth = round($contributionOfMonth, 2);
            $growth = round($growth, 2);
            $lines .= "$saleName,$amountOfYear,$planAmountOfYear,$contributionOfYear%,$ytd%,$amountOfMonth,$planAmountOfMonth,$finishOfMonth%,$contributionOfMonth%,$growth%,$mgrName\n";

        }

        return $lines;
    }


    public function payMgr($year): string
    {
        global $config, $cache;
        if (!$config['CACHE']) {
            return $this->payMgrSource($year);
        }

        $key = "mgr_${year}";
        return $cache->getOrSet($key, fn() => $this->payMgrSource($year), CACHET);
    }

    private function payMgrSource($year): string
    {
        $lines = "地区经理,申请费用,销售额,投产比\n";
        $allMgrs = Sale::all()->where(fn($x) => $x['Year'] == $year);

        $allCost = Cost::all()->where(fn($x) => $x['Year'] == $year);

        $mgrs = $allMgrs->groupBy(fn($x) => $x['MgrName']);
        foreach ($mgrs as $mgr) {
            $linqMgr = new Linq($mgr);
            $mgrName = $mgr[0]['MgrName'];

            // 申请费用
            $sales = $allMgrs->select(fn($x) => $x['SaleName'])->where(fn($x) => strlen($x) > 0)->push($mgrName)->distinct();
            $cost = $allCost->where(fn($x) => $sales->has($x['Name']))->sum(fn($x) => $x['Amount']);


            // 全年销售额
            $amountOfYear = $linqMgr->sum(fn($x) => $x['Amount']);

            // 投产比
            $production = self::div($cost, $amountOfYear) * 100;

            $amountOfYear = round($amountOfYear, 2);
            $cost = round($cost, 2);
            $production = round($production, 2);

            $lines .= "$mgrName,$cost,$amountOfYear,$production%\n";
        }

        return $lines;
    }


    public function paySale($year): string
    {
        global $config, $cache;
        if (!$config['CACHE']) {
            return $this->paySaleSource($year);
        }

        $key = "sale_${year}";
        return $cache->getOrSet($key, fn() => $this->paySaleSource($year), CACHET);
    }

    private function paySaleSource($year): string
    {
        $lines = "销售代表,申请费用,销售额,投产比\n";
        $allSales = Sale::all()->where(fn($x) => $x['Year'] == $year)->groupBy(fn($x) => $x['SaleName']);

        $allCost = Cost::all()->where(fn($x) => $x['Year'] == $year);

        foreach ($allSales as $sale) {
            $linqSale = new Linq($sale);
            $saleName = $sale[0]['SaleName'];

            // 申请费用
            $cost = $allCost->where(fn($x) => $x['Name'] == $saleName)->first()['Amount'];


            // 全年销售额
            $amountOfYear = $linqSale->sum(fn($x) => $x['Amount']);

            // 投产比
            $production = self::div($cost, $amountOfYear) * 100;

            $amountOfYear = round($amountOfYear, 2);
            $cost = round($cost, 2);
            $production = round($production, 2);

            $lines .= "$saleName,$cost,$amountOfYear,$production%\n";
        }

        return $lines;
    }


    private static function getNames($list, $key): string
    {
        $names = [];
        foreach ($list as $row) {
            if (strlen($row[$key]) > 0)
                $names[] = $row[$key];
        }
        // 排序
        sort($names);

        // 去重并用&连接
        return implode('&', array_unique($names));
    }

    private static function div($a, $b): float
    {
        if (self::isZero($b))
            return 0;
        return $a / $b;
    }

    private static function isZero($float, $epsilon = 0.00001): float
    {
        return abs($float) < $epsilon;
    }
}

function exportHospital($year, $month, $productName)
{
    global $db;
    $cacheData = getCacheData(['exportHospital', $year, $month, $productName]);
    if ($cacheData != '') {
        return $cacheData;
    }

    $list = $db->query('SELECT * FROM TbHospital');
    $linqList = new Linq($list);

    $hospitals = [];
    foreach ($list as $row) {
        $hospitals[$row['HospitalName']][] = $row;
    }
    $lines = "医院名称,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月投入金额,当月销售金额,投产比,当月达成百分比,全年占比贡献,当月占比贡献,同期增长,地区经理,销售代表\n";

    foreach ($hospitals as $hospital) {
        $linqHospital = new Linq($hospital);

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
//        foreach ($list as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $allSales += $row['MonthlySales'];
//            }
//        }
        $allSales = $linqList->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);


        // 所有医院当前月份销售额
        $allMonthlySales = 0;
//        foreach ($list as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $allMonthlySales += $row['MonthlySales'];
//            }
//        }
        $allMonthlySales = $linqList->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);


        // 医院名称
        $hospitalName = $hospital[0]['HospitalName'];

        // 全年销售额, 只计算当年且当前产品类型的数据
//        foreach ($hospital as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalSales += $row['MonthlySales'];
//            }
//        }
        $totalSales = $linqHospital->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);


        // 全年指标金额, 只计算当年且当前产品类型的数据
//        foreach ($hospital as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalTargetAmount += $row['MonthlySalesPlan'];
//            }
//        }
        $totalTargetAmount = $linqHospital->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySalesPlan']);

        // 全年指标数量, 只计算当年且当前产品类型的数据
//        foreach ($hospital as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalTargetQuantity += $row['MonthlyVolumePlan'];
//            }
//        }
        $totalTargetQuantity = $linqHospital->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlyVolumePlan']);

        // YTD达成百分比, 只计算当年且当前产品类型的数据, 保留两位小数
        if (!isZero($totalTargetAmount)) {
            $ytdAchievement = $totalSales / $totalTargetAmount * 100;
        }


        // 当月指标金额
//        foreach ($hospital as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $monthlyTargetAmount += $row['MonthlySalesPlan'];
//            }
//        }
        $monthlyTargetAmount = $linqHospital->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySalesPlan']);

        // 投入金额
        $inputAmount = $linqHospital->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['Cost']);

        // 当月销售金额
//        foreach ($hospital as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $monthlySales += $row['MonthlySales'];
//            }
//        }
        $monthlySales = $linqHospital->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);


        // 投产比 投入金额/当月销售额
        $productionRatio = 0;
        if (!isZero($monthlySales)) {
            $productionRatio = $inputAmount / $monthlySales * 100;
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
//        foreach ($list as $row) {
//            if ($row['Year'] == $lastYear && $row['Month'] == $lastMonth && $row['ProductType'] == $productName && $row['HospitalName'] == $hospitalName) {
//                $lastYearSales += $row['MonthlySales'];
//            }
//        }
        $lastYearSales = $linqList->where(fn($x) => $x['Year'] == $lastYear && $x['Month'] == $lastMonth && $x['HospitalName'] == $hospitalName)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        if (!isZero($lastYearSales)) {
            $growth = 100.0 * ($monthlySales / $lastYearSales - 1);
        }

        // 销售代表, 先按照年份倒序,再按照月份倒序,再取非空第一个
        $saleName = getLastName($hospital, $year, $month, $productName, 'SaleName');

        // 地区经理, 先按照年份倒序,再按照月份倒序,再取非空第一个
        $mgrName = getLastName($hospital, $year, $month, $productName, 'MgrName');


        // 全部保留2位小数
        $totalSales = round($totalSales, 2);
        $totalTargetAmount = round($totalTargetAmount, 2);
        $totalTargetQuantity = round($totalTargetQuantity, 2);
        $ytdAchievement = round($ytdAchievement, 2);
        $monthlyTargetAmount = round($monthlyTargetAmount, 2);
        $monthlySales = round($monthlySales, 2);
        $inputAmount = round($inputAmount, 2);
        $productionRatio = round($productionRatio, 2);
        $monthlyAchievement = round($monthlyAchievement, 2);
        $totalContribution = round($totalContribution, 2);
        $monthlyContribution = round($monthlyContribution, 2);
        $growth = round($growth, 2);


        $lines .= "$hospitalName,$totalSales,$totalTargetAmount,$totalTargetQuantity,$ytdAchievement%,$monthlyTargetAmount,$inputAmount, $monthlySales,$productionRatio%,$monthlyAchievement%,$totalContribution%,$monthlyContribution%,$growth%,$mgrName,$saleName\n";
    }

    setCacheData(['exportHospital', $year, $month, $productName], $lines);
    return $lines;

}

function exportPeople($year, $month, $productName, $peopleType)
{
    if ($peopleType == 'mgr') {
        return exportMgr($year, $month, $productName);
    } else if ($peopleType == 'sale') {
        return exportSale($year, $month, $productName);
    } else {
        throw new Exception('未知的人员类型');
    }
}

function exportMgr($year, $month, $productName)
{
    global $db;
    $cacheData = getCacheData(['exportMgr', $year, $month, $productName]);
    if ($cacheData != '') {
        return $cacheData;
    }
    $list = $db->query('SELECT * FROM TbHospital');
    $linqList = new Linq($list);

    $mgrs = [];
    foreach ($list as $row) {
        $mgrName = $row['MgrName'];
        if (empty($mgrName))
            continue;
        $mgrs[$mgrName][] = $row;
    }
    $lines = "地区经理,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月投入金额,当月销售金额,投产比,当月达成百分比,${productName}全年占比贡献,${productName}当月占比贡献,同期增长,医院列表,销售代表列表\n";

    foreach ($mgrs as $mgr) {
        $linqMgr = new Linq($mgr);
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
//        foreach ($list as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $allSales += $row['MonthlySales'];
//            }
//        }
        $allSales = $linqList->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);


        // 所有经理当前月份销售额
        $allMonthlySales = 0;
//        foreach ($list as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $allMonthlySales += $row['MonthlySales'];
//            }
//        }
        $allMonthlySales = $linqList->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        // 全年销售额, 只计算当年且当前产品类型的数据
//        foreach ($mgr as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalSales += $row['MonthlySales'];
//            }
//        }
        $totalSales = $linqMgr->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        // 全年指标金额, 只计算当年且当前产品类型的数据
//        foreach ($mgr as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalTargetAmount += $row['MonthlySalesPlan'];
//            }
//        }
        $totalTargetAmount = $linqMgr->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySalesPlan']);

        // 全年指标数量, 只计算当年且当前产品类型的数据
//        foreach ($mgr as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalTargetQuantity += $row['MonthlyVolumePlan'];
//            }
//        }
        $totalTargetQuantity = $linqMgr->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlyVolumePlan']);

        // YTD达成百分比, 只计算当年且当前产品类型的数据, 保留两位小数
        if (!isZero($totalTargetAmount)) {
            $ytdAchievement = $totalSales / $totalTargetAmount * 100;
        }

        // 当月指标金额
//        foreach ($mgr as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $monthlyTargetAmount += $row['MonthlySalesPlan'];
//            }
//        }
        $monthlyTargetAmount = $linqMgr->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySalesPlan']);

        // 投入金额
        $inputAmount = $linqMgr->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['Cost']);

        // 当月销售金额
//        foreach ($mgr as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $monthlySales += $row['MonthlySales'];
//            }
//        }
        $monthlySales = $linqMgr->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        if (!isZero($monthlySales)) {
            // 投产比, 保留两位小数
            $productionRatio = $inputAmount / $monthlySales * 100;
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
//        foreach ($list as $row) {
//            if ($row['Year'] == $lastYear && $row['Month'] == $lastMonth && $row['ProductType'] == $productName && $row['MgrName'] == $mgrName) {
//                $lastYearSales += $row['MonthlySales'];
//            }
//        }
        $lastYearSales = $linqList->where(fn($x) => $x['Year'] == $lastYear && $x['Month'] == $lastMonth && $x['MgrName'] == $mgrName)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        if (!isZero($lastYearSales)) {
            $growth = 100.0 * ($monthlySales / $lastYearSales - 1);
        }

        // 医院列表
        foreach ($mgr as $row) {
            // 判断是否重复
            if (in_array($row['HospitalName'], $hospitals) == false)
                $hospitals[] = $row['HospitalName'];
        }

        // 销售代表列表
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
        $inputAmount = round($inputAmount, 2);
        $monthlySales = round($monthlySales, 2);
        $productionRatio = round($productionRatio, 2);
        $monthlyAchievement = round($monthlyAchievement, 2);
        $totalContribution = round($totalContribution, 2);
        $monthlyContribution = round($monthlyContribution, 2);
        $growth = round($growth, 2);

        $hospitals = implode('&', $hospitals);
        $sales = implode('&', $sales);

        $lines .= "$mgrName,$totalSales,$totalTargetAmount,$totalTargetQuantity,$ytdAchievement%,$monthlyTargetAmount,$inputAmount,$monthlySales,$productionRatio%,$monthlyAchievement%,$totalContribution%,$monthlyContribution%,$growth%,$hospitals,$sales\n";
    }
    setCacheData(['exportMgr', $year, $month, $productName], $lines);
    return $lines;
}

function exportSale($year, $month, $productName)
{
    global $db;
    $cacheData = getCacheData(['exportSale', $year, $month, $productName]);
    if ($cacheData != '') {
        return $cacheData;
    }

    $list = $db->query('SELECT * FROM TbHospital');
    $linqList = new Linq($list);

    $sales = [];
    foreach ($list as $row) {
        $saleName = $row['SaleName'];
        if (empty($saleName))
            continue;
        $sales[$saleName][] = $row;
    }
    $lines = "销售代表,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月投入金额,当月销售金额,投产比,当月达成百分比,${productName}全年占比贡献,${productName}当月占比贡献,同期增长,地区经理,医院列表\n";

    foreach ($sales as $sale) {
        $linqSale = new Linq($sale);

        $saleName = $sale[0]['SaleName'];
        $mgrName = $sale[0]['MgrName'];
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


        // 所有销售代表, 当前产品全年销售额
        $allSales = 0;
//        foreach ($list as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $allSales += $row['MonthlySales'];
//            }
//        }
        $allSales = $linqList->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        // 所有销售代表当前月份销售额
        $allMonthlySales = 0;
//        foreach ($list as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $allMonthlySales += $row['MonthlySales'];
//            }
//        }
        $allMonthlySales = $linqList->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        // 全年销售额, 只计算当年且当前产品类型的数据
//        foreach ($sale as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalSales += $row['MonthlySales'];
//            }
//        }
        $totalSales = $linqSale->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        // 全年指标金额, 只计算当年且当前产品类型的数据
//        foreach ($sale as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalTargetAmount += $row['MonthlySalesPlan'];
//            }
//        }
        $totalTargetAmount = $linqSale->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySalesPlan']);

        // 全年指标数量, 只计算当年且当前产品类型的数据
//        foreach ($sale as $row) {
//            if ($row['Year'] == $year && $row['ProductType'] == $productName) {
//                $totalTargetQuantity += $row['MonthlyVolumePlan'];
//            }
//        }
        $totalTargetQuantity = $linqSale->where(fn($x) => $x['Year'] == $year)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlyVolumePlan']);

        // YTD达成百分比, 只计算当年且当前产品类型的数据, 保留两位小数
        if (!isZero($totalTargetAmount)) {
            $ytdAchievement = $totalSales / $totalTargetAmount * 100;
        }

        // 当月指标金额
//        foreach ($sale as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $monthlyTargetAmount += $row['MonthlySalesPlan'];
//            }
//        }
        $monthlyTargetAmount = $linqSale->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySalesPlan']);

        // 当月投入金额
        $inputAmount = $linqSale->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlyInput']);


        // 当月销售金额
//        foreach ($sale as $row) {
//            if ($row['Year'] == $year && $row['Month'] == $month && $row['ProductType'] == $productName) {
//                $monthlySales += $row['MonthlySales'];
//            }
//        }
        $monthlySales = $linqSale->where(fn($x) => $x['Year'] == $year && $x['Month'] == $month)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);

        // 投产比
        $productionRatio = 0;
        if (!isZero($monthlySales)) {
            $productionRatio = $inputAmount / $monthlySales * 100;
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
//        foreach ($list as $row) {
//            if ($row['Year'] == $lastYear && $row['Month'] == $lastMonth && $row['ProductType'] == $productName && $row['SaleName'] == $saleName) {
//                $lastYearSales += $row['MonthlySales'];
//            }
//        }
        $lastYearSales = $linqList->where(fn($x) => $x['Year'] == $lastYear && $x['Month'] == $lastMonth && $x['SaleName'] == $saleName)
            ->ifWhere($productName != 'ALL', fn($x) => $x['ProductType'] == $productName)
            ->sum(fn($x) => $x['MonthlySales']);


        if (!isZero($lastYearSales)) {
            $growth = 100.0 * ($monthlySales / $lastYearSales - 1);
        }

        // 医院列表
        foreach ($sale as $row) {
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
        $inputAmount = round($inputAmount, 2);
        $monthlySales = round($monthlySales, 2);
        $productionRatio = round($productionRatio, 2);
        $monthlyAchievement = round($monthlyAchievement, 2);
        $totalContribution = round($totalContribution, 2);
        $monthlyContribution = round($monthlyContribution, 2);
        $growth = round($growth, 2);

        $hospitals = implode('&', $hospitals);

        $lines .= "$saleName,$totalSales,$totalTargetAmount,$totalTargetQuantity,$ytdAchievement%,$monthlyTargetAmount,$inputAmount,$monthlySales,$productionRatio,$monthlyAchievement%,$totalContribution%,$monthlyContribution%,$growth%,$mgrName,$hospitals\n";
    }
    setCacheData(['exportSale', $year, $month, $productName], $lines);
    return $lines;
}

function getCacheData($keys)
{
    return '';

    // 把keys数组转换成字符串
    global $cache;
    $key = implode(',', $keys);
    return $cache->get($key, '');
}

function setCacheData($keys, $data)
{
    // 把keys数组转换成字符串
    global $cache;
    $key = implode(',', $keys);
    $cache->set($key, $data, 7 * 24 * 3600);
}


function isZero($float, $epsilon = 0.00001)
{
    return abs($float) < $epsilon;
}

function getLastName($hospital, $year, $month, $productName, $key)
{

    $names = [];

    $name = '';
    for ($i = 0; $i < 12 * 3; $i++) {


        foreach ($hospital as $row) {
            if ($row['Year'] == $year && $row['Month'] == $month) {
                if ($productName == 'ALL') {
                    $names[] = $row[$key];
                } else {
                    if ($row['ProductType'] == $productName) {
                        $name = $row[$key];
                        break;
                    }
                }

            }
        }
        if (count($names) > 0) {
            // $names去重
            $names = array_unique($names);
            // 用&连接
            $name = implode('&', $names);
            break;
        }
        if (!empty($name)) {
            break;
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