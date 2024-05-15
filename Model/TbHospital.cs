using FreeSql;
using FreeSql.DataAnnotations;

namespace Model;

[Index("Hospital", "HospitalName,Year,Month,Type", true)]
public class TbHospital
{
    [SnowFlake]
    [Column(IsPrimary = true)]
    public long Id { get; set; }

    /// <summary>
    /// 医院名称
    /// </summary>
    public string HospitalName { get; set; } = null!;

    /// <summary>
    /// 年份
    /// </summary>
    public int Year { get; set; }

    /// <summary>
    /// 月份
    /// </summary>
    public int Month { get; set; }


    /// <summary>
    /// 当月销售金额
    /// </summary>
    public decimal SaleOfMonth { get; set; }

    /// <summary>
    /// 当月指标金额
    /// </summary>
    public decimal PlanSaleOfMonth { get; set; }

    
    // /// <summary>
    // /// 当年指标金额
    // /// </summary>
    // public decimal PlanSaleOfYear { get; set; }
    
    /// <summary>
    /// 当月指标数量
    /// </summary>
    public decimal PlanCountOfMonth { get; set; }

    // /// <summary>
    // /// 全年销售金额
    // /// </summary>
    // public decimal SaleOfYear { get; set; }
    //
    // /// <summary>
    // /// 全年指标金额
    // /// </summary>
    // public decimal PlanSaleOfYear { get; set; }

    // /// <summary>
    // /// 全年指标盒数
    // /// </summary>
    // public int PlanCountOfYear { get; set; }


    /// <summary>
    /// 人名
    /// </summary>
    public string SaleRealName { get; set; } = null!;

    /// <summary>
    /// 经理人名
    /// </summary>
    public string MgrRealName { get; set; } = null!;

    /// <summary>
    /// 产品类型  EDR  SIG
    /// </summary>
    public string Type { get; set; } = null!;

    /// <summary>
    /// 备注
    /// </summary>
    public string? Comment { get; set; }
}