using FreeSql;
using Pingfan.Kit;

namespace Model;

public class Orm
{
    /// <summary>
    /// 数据库访问对象
    /// </summary>
    public static readonly IFreeSql Db = null!;

    /// <summary>
    /// 全局数据库名称
    /// </summary>
    public const string DbName = "YuXuan";

    /// <summary>
    /// 数据库连接字符串
    /// </summary>
    public static readonly string ConnectionString = Config.Get("YuXuanDBConnectionString");

    static Orm()
    {
        var dbString = ConnectionString;
        if (dbString.IsNullOrWhiteSpace())
            dbString = Environment.GetEnvironmentVariable("YuXuanDBConnectionString", EnvironmentVariableTarget.Machine);

        if (dbString.IsNullOrWhiteSpace())
        {
            Log.Fatal("数据库连接字符串为空");
            Environment.Exit(1);
            return;
        }

        // 快速检测一下数据库是否能连接
        var ip = dbString!.Match(@"(?<=Data Source.*?=)(.*?)(?=;)").FirstOrDefault()!;
        var port = dbString!.Match(@"(?<=,)(.*?)(?=;)").FirstOrDefault()!.ToInt(1433);

        if (Telnet.Test(ip, port) == false)
        {
            Log.Fatal("数据库连接失败");
            throw new Exception($"数据库连接失败 {ip}:{port}");
        }


        Db = new FreeSqlBuilder()
                .UseConnectionString(DataType.SqlServer, dbString)
                .Build()
                .UseEmptyString()
                .UseSnowFlask()
            ;
    }
}