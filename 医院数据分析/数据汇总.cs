using System.Text;
using Model;
using Pingfan.Kit;

namespace 医院数据分析;

public class 数据汇总
{
    public static void Start()
    {
        Export(2024, new List<int>() { 1, 2, 3, 4 }, 4, "EDR");
        Export(2024, new List<int>() { 1, 2, 3, 4 }, 4, "SIG");
    }

    private static void Export(int currentYear, IList<int> months, int currentMonth, string currentType)
    {
        var list = Orm.Db.Select<TbHospital>().ToList();

        var hospitals = list.GroupBy(x => x.HospitalName).ToList();

        var sb = new StringBuilder();
        
        sb.AppendLine($"医院名称,全年销售额,全年指标金额,全年指标数量,YTD达成百分比,当月指标金额,当月销售金额,当月达成百分比,{currentType}全年占比贡献,{currentType}当月占比贡献,同期增长,人名,经理名,数据来自");
        foreach (var hospital in hospitals)
        {
            var name = hospital.Key;

            // 全年销售额
            var saleYearAmount = hospital
                .Where(p => p.Year == currentYear && p.Type == currentType)
                .Sum(x => x.SaleOfMonth);

            // 全年指标金额
            var planYearAmount = hospital
                .Where(p => p.Year == currentYear && p.Type == currentType)
                .Sum(x => x.PlanSaleOfMonth);

            // 全年指标数量
            var playYearCount = hospital
                .Where(p => p.Year == currentYear && p.Type == currentType)
                .Sum(x => x.PlanCountOfMonth);

            // YTD达成百分比 1-4月的数据 累计的 销售金额/全年指标金额
            var ytd = planYearAmount == 0
                ? 0
                : hospital
                    .Where(p => p.Year == currentYear && months.Contains(p.Month) && p.Type == currentType)
                    .Sum(x => x.SaleOfMonth) / planYearAmount * 100;

            // 当月指标金额
            var planMonthAmount = hospital
                .Where(p => p.Year == currentYear && p.Month == currentMonth && p.Type == currentType)
                .Sum(x => x.PlanSaleOfMonth);

            // 当月销售金额
            var saleMonthAmount = hospital
                .Where(p => p.Year == currentYear && p.Month == currentMonth && p.Type == currentType)
                .Sum(x => x.SaleOfMonth);

            // 当月达成百分比
            var monthPercent = planMonthAmount == 0 ? 0 : saleMonthAmount / planMonthAmount * 100;

            // 全年占比贡献 本家医院除以本年所有医院的销售额
            var contributionOfYear =
                hospital.Where(p => p.Year == currentYear && p.Type == currentType)
                    .Sum(x => x.SaleOfMonth) /
                list.Where(p => p.Year == currentYear && p.Type == currentType)
                    .Sum(x => x.SaleOfMonth) * 100;
            
            
            // 当月占比贡献 本家医院除以本月所有医院的销售额
            var contributionOfMonth =
                hospital.Where(p => p.Year == currentYear && p.Month == currentMonth && p.Type == currentType)
                    .Sum(x => x.SaleOfMonth) /
                list.Where(p => p.Year == currentYear && p.Month == currentMonth && p.Type == currentType)
                    .Sum(x => x.SaleOfMonth) * 100;
            
            

            // 同期增长, 去年这个时候
            var lastYearMonth = hospital
                .Where(p => p.Year == currentYear - 1 && p.Month == currentMonth && p.Type == currentType)
                .Sum(x => x.SaleOfMonth);
            lastYearMonth = lastYearMonth == 0
                ? 0
                : ((hospital.Where(p => p.Year == currentYear && p.Month == currentMonth && p.Type == currentType)
                    .Sum(x => x.SaleOfMonth) / lastYearMonth) - 1) * 100;


            // 要hospital中的年份和月份都是要排序后的, 最后一个就是最新的
            var lastRow = hospital.OrderByDescending(p => p.Year).ThenByDescending(p => p.Month).First();
            // 人名
            var realname = lastRow.SaleRealName;
            // 经理名
            var manager =lastRow.MgrRealName;
            
            // 数据来自
            var remark = string.Join('&',
                hospital
                    .Where(p => p.Type == currentType)
                    .SelectMany(p => p.Comment?.Split(',', StringSplitOptions.RemoveEmptyEntries)).Distinct()
                    .Select(p => p.Replace(',', '&').TrimEnd('&', '\0')));


            sb.AppendLine(
                $"{name},{saleYearAmount},{planYearAmount},{playYearCount},{Math.Round(ytd, 2)}%,{planMonthAmount},{saleMonthAmount},{Math.Round(monthPercent, 2)}%,{Math.Round(contributionOfYear, 2)}%,{Math.Round(contributionOfMonth, 2)}%,{Math.Round(lastYearMonth, 2)}%,{realname},{manager},{remark}");
        }

        var path = $"{currentYear}年{months.First()}-{months.Last()}月-{currentType}汇总.csv";
        var result = sb.ToString();
        File.WriteAllText(path, result, Encoding.UTF8);
    }
}