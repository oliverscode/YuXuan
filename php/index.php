<?php

require_once 'inc/app.php';
global $req;

$action = $req->post('action');
if ($action == 'export') {
    $year = $req->int('year');
    $month = $req->int('month');
    $type = $req->string('type');
    $productName = $req->string('productName');
    $peopleType = $req->string('peopleType');

    $export = new Export();

    if ($type == 'hospital')
        $result = $export->hospital($year, $month, $productName);
    else if ($type == 'people') {
        if ($peopleType == 'mgr')
            $result = $export->mgr($year, $month, $productName);
        else if ($peopleType == 'sale')
            $result = $export->sale($year, $month, $productName);
    } else if ($type == 'pay') {
        if ($peopleType == 'mgr')
            $result = $export->payMgr($year);
        else if ($peopleType == 'sale')
            $result = $export->paySale($year);
    }


    die($result);
} else if ($action == 'analysis') {
    // analysisData();
    die('整合成功');
} else if ($action == 'speed') {

//    $cache = new FileCache();
//    $cache->clear();
//
//    // 遍历从23年到现在的每一个月份
//    for ($year = 2023; $year <= date('Y'); $year++) {
//        for ($month = 1; $month <= 12; $month++) {
//            // 遍历每一个产品类型
//            foreach (['EDR', 'SIG'] as $productName) {
//                exportHospital($year, $month, $productName);
//            }
//
//            // 遍历每一个人员类型
//            foreach (['mgr', 'sale'] as $peopleType) {
//                exportPeople($year, $month, 'EDR', $peopleType);
//                exportPeople($year, $month, 'SIG', $peopleType);
//            }
//
//        }
//    }
//    die('加速成功');

}


?>
<!doctype html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="favicon.png" type="image/png">

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/semantic-ui/2.5.0/semantic.min.js"></script>
    <link href="https://cdn.bootcdn.net/ajax/libs/semantic-ui/2.5.0/semantic.min.css" rel="stylesheet">
    <!--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">-->
    <script src="src/layer/layer.js"></script>

    <script type="text/javascript"
            src="src/table/index.js"></script>

    <title>Yuxuan</title>
    <style>
        /*        表格的td不能超过200px */
        table td {
            max-width: 200px;
            min-height: 30px;

            overflow-x: auto;
            overflow-y: hidden;

            /*超出就添加滚动条*/
            /*overflow: auto;*/
            white-space: nowrap;
        }
    </style>
</head>
<body>

<div class="ui top attached tabular menu">
    <a class="item active" data-tab="tab_hospital">🏥医院数据</a>
    <a class="item " data-tab="tab_people">👥人员数据</a>
    <a class="item " data-tab="tab_pay">💴 投产比</a>
    <a class="item " data-tab="tab_yang">🐒小杨操作区</a>
    <a class="item " data-tab="tab_zhu">🦁朱总操作区</a>
</div>
<div class="ui bottom attached tab segment active" data-tab="tab_hospital">

    <div class="ui" style="width: fit-content;">
        <form class="ui form" id="formHospital" onsubmit="return false;">
            <div class="inline fields" style="justify-content: center; text-align: center;">
                <div class="field">
                    <label>📅年份</label>
                    <label>
                        <select name="h_year">
                            <option value="2024" selected>2024</option>
                            <option value="2023">2023</option>
                            <!-- 添加更多年份选项 -->
                        </select>
                    </label>
                </div>
                <div class="field">
                    <label>🗓️月份</label>
                    <label>
                        <select name="h_month">
                            <?php
                            $month = date('m');
                            for ($i = 1; $i <= 12; $i++) {
                                $selected = $i == $month ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            }
                            ?>
                        </select>
                    </label>
                </div>
                <div class="field">
                    <label>💊类型</label>
                    <label>
                        <select name="h_productName">
                            <option value="EDR" selected>EDR</option>
                            <option value="SIG">SIG</option>
                            <option value="GRA">GRA</option>
                        </select>
                    </label>
                </div>
                <div class="field">
                    <button class="ui button " type="submit">🔍 查询数据</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="ui bottom attached tab segment " data-tab="tab_people">

    <div class="ui" style="width: fit-content;">
        <form class="ui form" id="fromPeople" onsubmit="return false;">
            <div class="inline fields" style="justify-content: center; text-align: center;">

                <div class="field">
                    <label>📅年份</label>
                    <label>
                        <select name="p_year">
                            <option value="2024" selected>2024</option>
                            <option value="2023">2023</option>
                            <!-- 添加更多年份选项 -->
                        </select>
                    </label>
                </div>
                <div class="field">
                    <label>🗓️月份</label>
                    <label>
                        <select name="p_month">
                            <?php
                            $month = date('m');
                            for ($i = 1; $i <= 12; $i++) {
                                $selected = $i == $month ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            }
                            ?>
                        </select>
                    </label>
                </div>

                <div class="field">
                    <label>💊类型</label>
                    <label>
                        <select name="p_productName">
                            <option value="EDR" selected>EDR</option>
                            <option value="SIG">SIG</option>
                            <option value="GRA">GRA</option>
                        </select>
                    </label>
                </div>

                <div class="field">
                    <label>🥇级别</label>
                    <label>
                        <select name="p_peopleType">
                            <option value="mgr">地区经理</option>
                            <option value="sale" selected>销售代表</option>
                        </select>
                    </label>
                </div>

                <div class="field">
                    <button class="ui button " type="submit">🔍 查询数据</button>
                </div>
            </div>
        </form>
    </div>

</div>
<div class="ui bottom attached tab segment " data-tab="tab_pay">

    <div class="ui" style="width: fit-content;">
        <form class="ui form" id="fromPay" onsubmit="return false;">
            <div class="inline fields" style="justify-content: center; text-align: center;">

                <div class="field">
                    <label>📅年份</label>
                    <label>
                        <select name="tp_year">
                            <option value="2024" selected>2024</option>
                        </select>
                    </label>
                </div>


                <div class="field">
                    <label>🥇级别</label>
                    <label>
                        <select name="tp_peopleType">
                            <option value="mgr">地区经理</option>
                            <option value="sale" selected>销售代表</option>
                        </select>
                    </label>
                </div>

                <div class="field">
                    <button class="ui button " type="submit">🔍 查询数据</button>
                </div>
            </div>
        </form>
    </div>

</div>
<div class="ui bottom attached tab segment " data-tab="tab_yang">

    <div class="ui" style="width: fit-content;">
        <form class="ui form">
            <div class="inline fields" style="justify-content: center; text-align: center;">
                <div class="field">
                    <button class="ui button" type="button" id="btnAnalysis">整合数据</button>
                    <button class="ui button" type="button" id="btnSpeed">加速查询</button>
                </div>
            </div>
        </form>
    </div>

</div>
<div class="ui bottom attached tab segment " data-tab="tab_zhu">

    <div class="ui" style="width: fit-content;">
        <form class="ui form">
            <div class="inline fields" style="justify-content: center; text-align: center;">
                <div class="field">
                    <button class="ui button" id="btnExport">📤导出当前表格</button>
                    <button class="ui button red" id="btnExit">🚪退出登录</button>
                </div>
            </div>
        </form>
    </div>
</div>

<table class="ui sortable celled table selectable striped" id="list">


</table>

<script src="src/index.js"></script>

<script>

    $('.menu .item').tab();
    // 如果tab切换，就把当前tab的名称保存到localStorage, 以便刷新页面时恢复
    $('.menu .item').click(function () {
        let tab = $(this).attr('data-tab');
        localStorage.setItem('tab', tab);
    });

    // 页面加载时 恢复 tab
    $(() => {
        // 先恢复选择项
        $('select').each(function () {
            let name = $(this).attr('name');
            let value = localStorage.getItem(name);
            if (value && value.length > 0) {
                $(this).val(value);
            }
        });


        let tab = localStorage.getItem('tab');
        if (tab && tab.length > 0) {
            $('.menu .item').tab('change tab', tab);
            // 同时触发一次查询
            if (tab === 'tab_hospital') {
                hospitalQuery();
            } else if (tab === 'tab_people') {
                peopleQuery();
            } else if (tab === 'tab_pay') {
                payQuery();
            }
        }
    });


    // tab切换时，触发一次查询
    $('.menu .item').click(function () {
        let tab = $(this).attr('data-tab');
        if (tab === 'tab_hospital') {
            hospitalQuery();
        } else if (tab === 'tab_people') {
            peopleQuery();
        } else if (tab === 'tab_pay') {
            payQuery();
        }
    });


    // 如果页面上的select元素发生变化，就把选择项保存到localStorage, 以便刷新页面时恢复
    $('select').change(function () {
        let name = $(this).attr('name');
        let value = $(this).val();
        localStorage.setItem(name, value);

        // 如果name以h_开头，就提交医院表单
        if (name.startsWith('h_')) {
            hospitalQuery();
        }
        // 如果name以p_开头，就提交人员表单
        else if (name.startsWith('p_')) {
            peopleQuery();
        }
    });


</script>


</body>
</html>
