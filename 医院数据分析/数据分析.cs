using FreeSql;
using Model;
using Pingfan.Kit;

namespace 医院数据分析;

public class 数据分析
{
    public static void Start()
    {
        Orm.Db.Delete<TbHospital>().Where("1=1").ExecuteAffrows();

        #region 2023年EDR销量

        {
            var path = @"整理数据\2023年EDR销量.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);

            foreach (var row in rows)
            {
                var line = row.Split("\t");

                var year = line[0].Substring(0, 4).ToInt();
                var month = line[0].Substring(4, 2).ToInt();

                var mgrName = line[1].Between("(", ")").Trim();
                var realName = line[2].Between("(", ")").Trim();
                var hospitalName = line[3].Trim();
                var saleOfMonth = line[4].ToDecimal();

                var hospital = new TbHospital()
                {
                    HospitalName = hospitalName,
                    Year = year,
                    Month = month,
                    SaleOfMonth = saleOfMonth,

                    MgrRealName = mgrName,
                    SaleRealName = realName,
                    Type = "EDR"
                };
                // 判断是否存在同样的医院
                var result = Orm.Db.Select<TbHospital>()
                    .Where(p => p.HospitalName == hospitalName)
                    .Where(p => p.Year == year)
                    .Where(p => p.Month == month)
                    .Where(p => p.Type == "EDR")
                    .First();
                if (result != null)
                {
                    hospital.Id = result.Id;
                    if (hospital.SaleRealName != result.SaleRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                    }

                    if (hospital.MgrRealName != result.MgrRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");
                    }


                    hospital.SaleOfMonth += result.SaleOfMonth;

                    Orm.Db.Update<TbHospital>()
                        .Set(p => p.SaleOfMonth, hospital.SaleOfMonth)
                        .Where(p => p.Id == result.Id)
                        .ExecuteAffrows(1);

                }
                else
                {
                    Orm.Db.Insert(hospital).ExecuteAffrows(1);
                }

                Orm.Db.Update<TbHospital>()
                    .Set(p => p.Comment + "2023年EDR销量,")
                    .Where(p => p.Id == hospital.Id)
                    .ExecuteAffrows(1);
            }

            Console.WriteLine("2023年EDR销量 处理完成");
        }

        #endregion

        #region 2023年SIG销量

        {
            var path = @"整理数据\2023年SIG销量.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);

            foreach (var row in rows)
            {
                var line = row.Split("\t");

                var year = line[0].Substring(0, 4).ToInt();
                var month = line[0].Substring(4, 2).ToInt();

                var mgrName = line[1].Between("(", ")").Trim();
                var realName = line[2].Between("(", ")").Trim();
                var hospitalName = line[3].Trim();
                var saleOfMonth = line[4].ToDecimal();

                var hospital = new TbHospital()
                {
                    HospitalName = hospitalName,
                    Year = year,
                    Month = month,
                    SaleOfMonth = saleOfMonth,

                    MgrRealName = mgrName,
                    SaleRealName = realName,
                    Type = "SIG"
                };
                // 判断是否存在同样的医院
                var result = Orm.Db.Select<TbHospital>()
                    .Where(p => p.HospitalName == hospitalName)
                    .Where(p => p.Year == year)
                    .Where(p => p.Month == month)
                    .Where(p => p.Type == "SIG")
                    .First();
                if (result != null)
                {
                    hospital.Id = result.Id;
                    if (hospital.SaleRealName != result.SaleRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                    }

                    if (hospital.MgrRealName != result.MgrRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");
                    }


                    hospital.SaleOfMonth += result.SaleOfMonth;
                    Orm.Db.Update<TbHospital>()
                        .Set(p => p.SaleOfMonth, hospital.SaleOfMonth)
                        .Where(p => p.Id == result.Id)
                        .ExecuteAffrows(1);
                }
                else
                {
                    Orm.Db.Insert(hospital).ExecuteAffrows(1);
                }

                Orm.Db.Update<TbHospital>()
                    .Where(p => p.Id == hospital.Id)
                    .Set(p => p.Comment + "2023年SIG销量,")
                    .ExecuteAffrows(1);
            }

            Console.WriteLine("2023年SIG销量 处理完成");
        }

        #endregion

        #region 2024年1-3月EDR销量

        {
            var path = @"整理数据\2024年EDR1-3月销量.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);


            foreach (var row in rows)
            {
                var line = row.Split("\t");

                var year = line[0].Substring(0, 4).ToInt();
                var month = line[0].Substring(4, 2).ToInt();

                var mgrName = line[1].Between("(", ")").Trim();
                var realName = line[2].Between("(", ")").Trim();
                var hospitalName = line[3].Trim();
                var saleOfMonth = line[4].ToDecimal();

                var hospital = new TbHospital()
                {
                    HospitalName = hospitalName,
                    Year = year,
                    Month = month,
                    SaleOfMonth = saleOfMonth,

                    MgrRealName = mgrName,
                    SaleRealName = realName,
                    Type = "EDR"
                };
                // 判断是否存在同样的医院
                var result = Orm.Db.Select<TbHospital>()
                    .Where(p => p.HospitalName == hospitalName)
                    .Where(p => p.Year == year)
                    .Where(p => p.Month == month)
                    .Where(p => p.Type == "EDR")
                    .First();
                if (result != null)
                {
                    hospital.Id = result.Id;
                    if (hospital.SaleRealName != result.SaleRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                    }

                    if (hospital.MgrRealName != result.MgrRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");
                    }


                    hospital.SaleOfMonth += result.SaleOfMonth;
                    Orm.Db.Update<TbHospital>()
                        .Set(p => p.SaleOfMonth, hospital.SaleOfMonth)
                        .Where(p => p.Id == result.Id)
                        .ExecuteAffrows(1);
                }
                else
                {
                    Orm.Db.Insert(hospital).ExecuteAffrows(1);
                }

                Orm.Db.Update<TbHospital>()
                    .Where(p => p.Id == hospital.Id)
                    .Set(p => p.Comment + "2024年EDR1-3月销量,")
                    .ExecuteAffrows(1);
            }

            Console.WriteLine("2024年1-3月EDR销量 处理完成");
        }

        #endregion

        #region 2024年1-3月SIG销量

        {
            var path = @"整理数据\2024年SIG1-3月销量.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);

            foreach (var row in rows)
            {
                var line = row.Split("\t");

                var year = line[0].Substring(0, 4).ToInt();
                var month = line[0].Substring(4, 2).ToInt();

                var mgrName = line[1].Between("(", ")").Trim();
                var realName = line[2].Between("(", ")").Trim();
                var hospitalName = line[3].Trim();
                var saleOfMonth = line[4].ToDecimal();

                var hospital = new TbHospital()
                {
                    HospitalName = hospitalName,
                    Year = year,
                    Month = month,
                    SaleOfMonth = saleOfMonth,

                    MgrRealName = mgrName,
                    SaleRealName = realName,
                    Type = "SIG"
                };
                // 判断是否存在同样的医院
                var result = Orm.Db.Select<TbHospital>()
                    .Where(p => p.HospitalName == hospitalName)
                    .Where(p => p.Year == year)
                    .Where(p => p.Month == month)
                    .Where(p => p.Type == "SIG")
                    .First();
                if (result != null)
                {
                    hospital.Id = result.Id;
                    if (hospital.SaleRealName != result.SaleRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                    }

                    if (hospital.MgrRealName != result.MgrRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");
                    }


                    hospital.SaleOfMonth += result.SaleOfMonth;
                    Orm.Db.Update<TbHospital>()
                        .Set(p => p.SaleOfMonth, hospital.SaleOfMonth)
                        .Where(p => p.Id == result.Id)
                        .ExecuteAffrows(1);
                }
                else
                {
                    Orm.Db.Insert(hospital).ExecuteAffrows(1);
                }

                Orm.Db.Update<TbHospital>()
                    .Where(p => p.Id == hospital.Id)
                    .Set(p => p.Comment + "2024年SIG1-3月销量,")
                    .ExecuteAffrows(1);
            }

            Console.WriteLine("2024年1-3月SIG销量 处理完成");
        }

        #endregion

        #region 两产品2024年4月销售数据

        {
            var path = @"整理数据\两产品四月销量.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);


            foreach (var row in rows)
            {
                var line = row.Split("\t");

                var year = line[0].Substring(0, 4).ToInt();
                var month = line[0].Substring(4, 2).ToInt();

                var mgrName = "";
                var realName = line[1].Between("(", ")").Trim();
                if (line[1].Contains("一组"))
                    mgrName = "朱戍馨";
                else if (line[1].Contains("二组"))
                    mgrName = "洪平良";
                else if (line[1].Contains("三组"))
                    mgrName = "张娜";
                else if (line[1].Contains("四组"))
                    mgrName = "狄志伟";
                else
                    throw new Exception("没有其他组了");

                var hospitalName = line[2].Trim();
                var type = line[3].Trim();
                var count = line[4].ToInt();

                var saleOfMonth = 0m;
                var goodsType = "";
                if (type == "EDR")
                    saleOfMonth = count * 40.81m;
                else if (type == "SIG 100片")
                    saleOfMonth = count * 155.84m;
                else if (type == "SIG  30片")
                    saleOfMonth = count * 47.63m;
                else
                    throw new Exception("没有这个商品");

                if (type.Contains("EDR"))
                    goodsType = "EDR";
                else if (type.Contains("SIG"))
                    goodsType = "SIG";


                var hospital = new TbHospital()
                {
                    HospitalName = hospitalName,
                    Year = year,
                    Month = month,
                    SaleOfMonth = saleOfMonth,

                    MgrRealName = mgrName,
                    SaleRealName = realName,
                    Type = goodsType,
                };
                // 判断是否存在同样的医院
                var result = Orm.Db.Select<TbHospital>()
                    .Where(p => p.HospitalName == hospitalName)
                    .Where(p => p.Year == year)
                    .Where(p => p.Month == month)
                    .Where(p => p.Type == goodsType)
                    .First();
                if (result != null)
                {
                    hospital.Id = result.Id;
                    if (hospital.SaleRealName != result.SaleRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                    }

                    if (hospital.MgrRealName != result.MgrRealName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");
                    }

                
                    hospital.SaleOfMonth += result.SaleOfMonth;
                    Orm.Db.Update<TbHospital>()
                        .Set(p => p.SaleOfMonth, hospital.SaleOfMonth)
                        .Where(p => p.Id == result.Id)
                        .ExecuteAffrows(1);
                }
                else
                {
                    Orm.Db.Insert(hospital).ExecuteAffrows(1);
                }

                Orm.Db.Update<TbHospital>()
                    .Where(p => p.Id == hospital.Id)
                    .Set(p => p.Comment + "两产品四月销量,")
                    .ExecuteAffrows(1);
            }

            Console.WriteLine("两产品2024年4月销售数据 处理完成");
        }

        #endregion

        #region EDR指标调整华南区

        {
            var path = @"整理数据\EDR指标调整华南区.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);


            foreach (var row in rows)
            {
                var line = row.Split("\t");
                var hospitalName = line[2].Trim();
                var mgrName = line[0].Between("(", ")").Trim();
                var realName = line[1].Between("(", ")").Trim();


                var year = 2024;
                for (var month = 1; month <= 12; month++)
                {
                    // 判断是否存在同样的医院
                    var result = Orm.Db.Select<TbHospital>()
                        .Where(p => p.HospitalName == hospitalName)
                        .Where(p => p.Year == year)
                        .Where(p => p.Month == month)
                        .Where(p => p.Type == "EDR")
                        .First();

                    if (result == null)
                    {
                        // throw new Exception($"没有这家医院的信息 [{hospitalName}]");
                        result = new TbHospital()
                        {
                            HospitalName = hospitalName,
                            Year = year,
                            Month = month,
                            Type = "EDR",
                            MgrRealName = mgrName,
                            SaleRealName = realName,
                        };
                        Orm.Db.Insert<TbHospital>(result).ExecuteAffrows(1);
                    }


                    if (result.MgrRealName != mgrName)
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");
                    if (result.SaleRealName != realName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                        result.SaleRealName = realName;
                    }


                    result.PlanCountOfMonth += line[2 + month].ToDecimal();
                    result.PlanSaleOfMonth += result.PlanCountOfMonth * 40.81m;
                    Orm.Db.Update<TbHospital>()
                        .Set(p => p.PlanCountOfMonth, result.PlanCountOfMonth)
                        .Set(p => p.PlanSaleOfMonth, result.PlanSaleOfMonth)
                        .Set(p => p.SaleRealName, result.SaleRealName)
                        .Set(p => p.Comment + "EDR指标调整华南区,")
                        .Where(p => p.HospitalName == hospitalName)
                        .Where(p => p.Year == year)
                        .Where(p => p.Month == month)
                        .Where(p => p.Type == "EDR")
                        .ExecuteAffrows(1);
                }
            }

            Console.WriteLine("EDR指标调整华南区 处理完成");
        }

        #endregion

        #region 2024年SIG指标

        {
            var path = @"整理数据\2024年SIG指标原版-SIG.txt";
            var text = File.ReadAllText(path);
            var rows = text.Split("\r", "\n").Skip(1);

            foreach (var row in rows)
            {
                var line = row.Split("\t");

                var mgrName = "";
                var realName = line[0].Between("(", ")").Trim();
                if (line[0].Contains("一组"))
                    mgrName = "朱戍馨";
                else if (line[0].Contains("二组"))
                    mgrName = "洪平良";
                else if (line[0].Contains("三组"))
                    mgrName = "张娜";
                else if (line[0].Contains("四组"))
                    mgrName = "狄志伟";
                else
                    throw new Exception("没有其他组了");

                var hospitalName = line[1].Trim();
                var type = line[2].Trim();

                var amountOfMonth = 0m;

                var year = 2024;
                for (var month = 1; month <= 12; month++)
                {
                    // 判断是否存在同样的医院
                    var result = Orm.Db.Select<TbHospital>()
                        .Where(p => p.HospitalName == hospitalName)
                        .Where(p => p.Year == year)
                        .Where(p => p.Month == month)
                        .Where(p => p.Type == "SIG")
                        .First();

                    var countOfMonth = line[2 + month].ToDecimal();
                    if (type == "SIG0101") // 30片
                        amountOfMonth = countOfMonth * 47.63m;
                    else if (type == "SIG0102") // 100片
                        amountOfMonth = countOfMonth * 155.84m;
                    else
                        throw new Exception("不存在的类型");

                    if (result == null)
                    {
                        // throw new Exception($"没有这家医院的信息 [{hospitalName}]");
                        result = new TbHospital()
                        {
                            HospitalName = hospitalName,
                            Year = year,
                            Month = month,
                            Type = "SIG",
                            MgrRealName = mgrName,
                            SaleRealName = realName
                        };
                        Orm.Db.Insert<TbHospital>(result).ExecuteAffrows(1);
                    }
                    else
                    {
                        result.PlanSaleOfMonth += amountOfMonth;
                        result.PlanCountOfMonth += countOfMonth;
                    }

                    if (result.SaleRealName != realName)
                    {
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 销售员名字不一样!");
                        result.SaleRealName = realName;
                    }

                    if (result.MgrRealName != mgrName)
                        Console.WriteLine($"[{hospitalName}]在{year}年{month}月, 区域经理名字不一样!");


                    Orm.Db.Update<TbHospital>()
                        // .Set(p => p.PlanSaleOfYear, result.PlanSaleOfYear)
                        .Set(p => p.PlanSaleOfMonth, amountOfMonth)
                        .Set(p => p.PlanCountOfMonth, line[2 + month].ToDecimal())
                        .Set(p => p.Comment + "2024年SIG指标,")
                        .Where(p => p.HospitalName == hospitalName)
                        .Where(p => p.Year == year)
                        .Where(p => p.Month == month)
                        .Where(p => p.Type == "SIG")
                        .ExecuteAffrows(1);
                }
            }

            Console.WriteLine("2024年SIG指标 处理完成");
        }

        #endregion
    }
}