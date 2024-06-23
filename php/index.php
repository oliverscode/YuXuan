<?php
require_once 'inc/app.php';
$action = Req::get('action');
if ($action == 'hospital') {
    $year = Req::int('year');
    $month = Req::int('month');
    $productType = Req::string('productType');
    $result = exportHospital($year, $month, $productType);

    die($result);
} else if ($action == 'people') {
    $year = Req::int('year');
    $month = Req::int('month');
    $productType = Req::string('productType');
    $peopleType = Req::string('peopleType');


    $result = exportPeople($year, $month, $productType, $peopleType);

    die($result);

} else if ($action == 'analysis') {
    analysisData();
    die('整合成功');
}


?>
<!doctype html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="Favicon.png" type="image/png">

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
            overflow: auto;
            white-space: nowrap;
        }

    </style>
</head>
<body>

<div class="ui top attached tabular menu">
    <a class="item active" data-tab="first">🏥医院数据</a>
    <a class="item " data-tab="second">👥人员数据</a>
    <a class="item " data-tab="third">🐒小杨操作区</a>
    <a class="item " data-tab="fourth">🦁朱总操作区</a>
</div>
<div class="ui bottom attached tab segment active" data-tab="first">

    <div class="ui" style="width: fit-content;">
        <form class="ui form" id="formHospital">
            <div class="inline fields" style="justify-content: center; text-align: center;">
                <div class="field">
                    <label>📅年份</label>
                    <label>
                        <select name="h_year">
                            <option value="2023">2023</option>
                            <option value="2024" selected>2024</option>
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
                        <select name="h_productType">
                            <option value="EDR" selected>EDR</option>
                            <option value="SIG">SIG</option>
                        </select>
                    </label>
                </div>
                <div class="field">
                    <button class="ui button" type="submit">🔍 查询数据</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="ui bottom attached tab segment " data-tab="second">

    <div class="ui" style="width: fit-content;">
        <form class="ui form" id="fromPeople">
            <div class="inline fields" style="justify-content: center; text-align: center;">

                <div class="field">
                    <label>📅年份</label>
                    <label>
                        <select name="p_year">
                            <option value="2023">2023</option>
                            <option value="2024" selected>2024</option>
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
                        <select name="p_productType">
                            <option value="EDR" selected>EDR</option>
                            <option value="SIG">SIG</option>
                        </select>
                    </label>
                </div>

                <div class="field">
                    <label>🥇级别</label>
                    <label>
                        <select name="p_peopleType">
                            <option value="mgr">经理</option>
                            <option value="sale" selected>组员</option>
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
<div class="ui bottom attached tab segment " data-tab="third">

    <div class="ui" style="width: fit-content;">
        <form class="ui form" id="formYang">
            <div class="inline fields" style="justify-content: center; text-align: center;">
                <div class="field">
                    <button class="ui button" type="submit">整合数据</button>
                </div>
            </div>
        </form>
    </div>

</div>
<div class="ui bottom attached tab segment " data-tab="fourth">

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

<table class="ui sortable celled table" id="list">


</table>

<script src="src/index.js"></script>

<script>

    $('.menu .item').tab();
    // 如果tab切换，就把当前tab的名称保存到localStorage, 以便刷新页面时恢复
    $('.menu .item').click(function () {
        let tab = $(this).attr('data-tab');
        localStorage.setItem('tab', tab);
    });
    // 页面加载时，恢复tab的选择项
    let tab = localStorage.getItem('tab');
    if (tab && tab.length > 0) {
        $('.menu .item').tab('change tab', tab);
    }


    // 如果页面上的select元素发生变化，就把选择项保存到localStorage, 以便刷新页面时恢复
    $('select').change(function () {
        let name = $(this).attr('name');
        let value = $(this).val();
        localStorage.setItem(name, value);
    });
    // 页面加载时，恢复select元素的选择项
    $('select').each(function () {
        let name = $(this).attr('name');
        let value = localStorage.getItem(name);
        if (value && value.length > 0) {
            $(this).val(value);
        }
    });


</script>


</body>
</html>
