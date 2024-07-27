<?php

class analysis
{
    public function __construct()
    {
        //      $this->clearDb();
        //      $this->clearCache();
    }

    public function sale()
    {
        $path = Path::combineFromServerRoot('/data/2023-1 2024-6.txt');

        $csv = new CsvReader($path);
        $list = $csv->read('业务月');
        foreach ($list as $row) {
            $year = intval(mb_substr($row['业务月'], 0, 4));
            $month = intval(mb_substr($row['业务月'], 4, 2));

            $hospitalName = $row['医院名称'];

            $mgrName = Mgr::getName($row['所属地区']);
            $saleName = Sale::getName($row['所属辖区']);

            $productName = Product::getName($row['品规编码']);
            $amount = floatval($row['金额']);

            $sale = new Sale($hospitalName, $year, $month, $productName);
            $sale->addAmount($saleName, $mgrName, $amount);

        }
    }

    public function plan()
    {
        $path = Path::combineFromServerRoot('/data/2024plan.txt');
        $csv = new CsvReader($path);
        $list = $csv->read('');

        foreach ($list as $row) {

            $year = 2024;
            $hospitalName = $row['机构名称'];
            $area = $row['所属辖区'];
            $saleName = Sale::getName($area);
            $mgrName = Mgr::getName($area);
            $productName = Product::getName($row['品规编码']);


            for ($month = 1; $month <= 12; $month++) {

                $planAmount = $row[$month . '月指标金额'];
                $planAmount = floatval($planAmount);

                $sale = new Sale($hospitalName, $year, $month, $productName);
                $sale->AddPlanAmount($saleName, $mgrName, $planAmount);

            }
        }
    }

    public function pay()
    {
        $path = Path::combineFromServerRoot('/data/pay.txt');
        $csv = new CsvReader($path);
        $list = $csv->read('预算月份');

        foreach ($list as $row) {

            $date = mb_split('/', $row['预算月份']);
            if (count($date) != 2)
                throw new Exception("日期格式错误:$row[预算月份]");
            $year = intval($date[0]);
            $month = intval($date[1]);

            // 判断月份是否是1-12
            if ($month < 1 || $month > 12)
                throw new Exception("月份错误:$month");


            $name = $row['申请人'];
            // 判断名字是否是空
            if (mb_strlen($name) == 0)
                throw new Exception("名字错误:$name");

            // 冻结金额
            $m1 = floatval($row['冻结金额']);
            $m2 = floatval($row['使用金额']);

            $pay = $m1 + $m2;

            $cost = new Cost($name, $year, $month);
            $cost->addPay($pay);
        }

    }

    public function clearCache()
    {
        $cache = new FileCache();
        $cache->clear();
    }

    public function clearDb()
    {
        global $db;
        $db->execute('DELETE FROM TbSale');
        $db->execute('DELETE FROM TbCost');
    }

}
