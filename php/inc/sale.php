<?php

class Sale
{
    private $row;
    private $hospitalName, $year, $month, $productName;

    public function __construct($hospitalName, $year, $month, $productName)
    {
        global $db;
        $this->hospitalName = $hospitalName;
        $this->year = $year;
        $this->month = $month;
        $this->productName = $productName;

        $this->row = $this->get();
        if ($this->row == null) {
            $this->add();
            $this->row = $this->get();
        }

        if ($this->row == null) {
            throw new Exception("添加失败");
        }

    }

    public function addAmount($saleName, $mgrName, $amount)
    {
        global $db;

        $this->checkName($saleName, $mgrName);


        $db->execute("UPDATE TbSale SET 
                  Amount = Amount + :amount,
                  MgrName = :mgrName,
                  SaleName = :saleName
              WHERE ID = :id;
            ", [
            'id' => $this->row['Id'],
            'mgrName' => $mgrName,
            'saleName' => $saleName,
            'amount' => $amount,
        ], 1);

    }

    public function AddPlanAmount($saleName, $mgrName, $amount)
    {
        global $db;

        $this->checkName($saleName, $mgrName);


        $db->execute("UPDATE TbSale SET 
                  PlanAmount = PlanAmount + :amount,
                  MgrName = :mgrName,
                  SaleName = :saleName
              WHERE ID = :id;
            ", [
            'id' => $this->row['Id'],
            'mgrName' => $mgrName,
            'saleName' => $saleName,
            'amount' => $amount,
        ], 1);

    }

    public static function all(): Linq
    {
        global $db;
        $list = $db->query('SELECT * FROM TbSale');
        return new Linq($list);
    }

    private function checkName($saleName, $mgrName)
    {
        global $db;
        $rowSaleName = $this->row['SaleName'];
        $rowMgrName = $this->row['MgrName'];
        if (mb_strlen($rowSaleName) > 0 && $rowSaleName != $saleName) {
//            $db->execute("UPDATE TbSale SET IsError = 1 WHERE ID = :id", [
//                'id' => $this->row['Id']
//            ], 1);
            // throw new Exception("销售员名称不一致, 原来是:$rowSaleName 新的是:$saleName, 医院:$this->hospitalName 产品:$this->productName 日期:$this->year $this->month");
        } else if (mb_strlen($rowMgrName) > 0 && $rowMgrName != $mgrName) {
            throw new Exception("经理名称不一致, 原来是:$rowMgrName 新的是:$mgrName");
        }

    }

    private function get()
    {
        global $db;
        $rows = $db->query("SELECT * FROM TbSale WHERE HospitalName = ? and Year = ? and Month = ? and ProductName = ? LIMIT 1",
            [
                $this->hospitalName,
                $this->year,
                $this->month,
                $this->productName
            ]);
        return $rows[0];
    }

    private function add()
    {
        global $db;
        $db->execute("INSERT INTO TbSale (HospitalName, Year, Month, ProductName) VALUES (:hospitalName, :year, :month, :productName);", [
            'hospitalName' => $this->hospitalName,
            'year' => $this->year,
            'month' => $this->month,
            'productName' => $this->productName
        ], 1);
    }

    public static function getName(string $name): string
    {
        $str = new Str($name);

        $n = $str->between('(', ')');

        // 特殊处理, 也代表空岗位
        if (!(mb_strpos($name, 'TBA') === false))
            return '';


        if (mb_strlen($n) > 0)
            return $n;

        return $name;
    }

}
