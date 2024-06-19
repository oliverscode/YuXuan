using System.Reflection;
using FreeSql;
using Model;
using Pingfan.Kit;

namespace 数据库建立;

class Program
{
    static void Main(string[] args)
    {
        // 配置数据库连接
        var host = "10.0.0.15";
        var user = "sa";
        var pwd = "QWEqwe123";
        var dbName = Orm.DbName;

        if (ProcessEx.IsAdmin)
            Environment.SetEnvironmentVariable("YuXuanDBConnectionString",
                $"Data Source ={host}; Initial Catalog={dbName}; User ID={user}; Password={pwd};Max Pool Size=4096;Encrypt=True; TrustServerCertificate=True",
                EnvironmentVariableTarget.Machine);


        try
        {
            var tables = Orm.Db.DbFirst.GetTablesByDatabase(dbName);
            foreach (var dbTableInfo in tables)
            {
                Orm.Db.Execute($"drop table {dbTableInfo.Name}");
            }
        }
        catch (Exception e)
        {
            Console.Error.WriteLine("数据库打开失败");
            Console.Error.WriteLine(e.ToString());
            Console.ReadLine();
            return;
        }


        // 生成新的表
        var tableAssembies = new List<Type>();
        foreach (Type type in Assembly.GetAssembly(typeof(TbHospital))!.GetExportedTypes())
        {
            if (type.FullName.StartsWith("Model.Tb") && type.IsClass)
            {
                tableAssembies.Add(type);
            }
        }

        Orm.Db.CodeFirst.SyncStructure(tableAssembies.ToArray());
        Console.WriteLine("数据库表生成成功");
    }
}