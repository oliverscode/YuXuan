<?php

class Cost
{
    private $row;

    private $name, $year, $month;

    public function __construct($name, $year, $month)
    {
        $this->name = $name;
        $this->year = $year;
        $this->month = $month;

        $this->row = $this->get();
        if ($this->row == null) {
            $this->add();
            $this->row = $this->get();
        }

        if ($this->row == null) {
            throw new Exception("添加失败");
        }

    }

    public function addPay($money)
    {
        global $db;
        $db->execute("UPDATE TbCost SET
                                Amount = Amount + :money
                            WHERE Id = :id
            ", [
            'id' => $this->row['Id'],
            'money' => $money,
        ], 1);
    }

    public static function all()
    {
        global $db;
        $list = $db->query('SELECT * FROM TbCost');
        return new Linq($list);
    }

    private function get()
    {
        global $db;
        $rows = $db->query("SELECT * FROM TbCost WHERE Name = ? and Year = ? and Month = ? LIMIT 1",
            [
                $this->name,
                $this->year,
                $this->month,
            ]);
        return $rows[0];
    }

    private function add()
    {
        global $db;
        $db->execute("INSERT INTO TbCost (Name, Year, Month) VALUES (:name, :year, :month);", [
            'name' => $this->name,
            'year' => $this->year,
            'month' => $this->month,
        ], 1);
    }
}