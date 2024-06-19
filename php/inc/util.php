<?php

function readData($path)
{

    $content = file_get_contents($path);


    /*
    这个$content的格式如下:
    DSM	所属辖区	机构名称	1月指标数量	2月指标数量	3月指标数量	4月指标数量	5月指标数量	6月指标数量	7月指标数量	8月指标数量	9月指标数量	10月指标数量	11月指标数量	12月指标数量
    广州一组DSM(朱戍馨)	广州一组MR(李保国)	东莞东华医院	30	30	30	100	100	122	300	300	300	300	300	300
    广州一组DSM(朱戍馨)	广州一组MR(李保国)	东莞市中医院	0	0	0	0	0	0	0	0	0	50	50	50
    广州一组DSM(朱戍馨)	广州一组MR(李保国)	东莞市万江医院	0	0	0	0	0	0	0	0	0	50	50	50

    需要按行读取，然后按列读取，然后返回一个二维数组, 第一行为标题，后面的行为数据, 我希望关联数组的key是标题，value是数据
    */

    $lines = explode("\n", $content);
    $titles = explode("\t", $lines[0]);
    $data = array();
    for ($i = 1; $i < count($lines); $i++) {
        if (empty($lines[$i]))
            continue;

        $values = explode("\t", $lines[$i]);
        $row = array();
        for ($j = 0; $j < count($titles); $j++) {

            // key去掉空字符, 例如空格, 换行符, 制表符等
            $key = $titles[$j];
            $key = preg_replace('/\s/', '', $key);
            $key = preg_replace('/\n/', '', $key);
            $key = preg_replace('/\t/', '', $key);
            $key = preg_replace('/\r/', '', $key);
            $key = preg_replace('/\v/', '', $key);
            $key = preg_replace('/\f/', '', $key);

            $value = $values[$j];
            $value = fullWidthToHalfWidth($value);
            // 如果value以数字开头，就可能是金额, 就可能有逗号, 就需要去掉逗号
            if (preg_match('/\d/', $value)) {
                $value = preg_replace('/,/', '', $value);
            }


            $row[$key] = $value;
        }
        $data[] = $row;
    }
    return $data;
}

function fullWidthToHalfWidth($str)
{
    // 结果字符串
    $res = '';

    for ($i = 0; $i < mb_strlen($str, 'UTF-8'); $i++) {
        // 获取字符
        $char = mb_substr($str, $i, 1, 'UTF-8');
        // 转换为Unicode编码
        $code = mb_ord($char, 'UTF-8');

        // 全角空格特殊处理
        if ($code == 0x3000) {
            $res .= chr(0x20);
        } // 全角字符（除空格）转换为半角
        elseif ($code >= 0xFF01 && $code <= 0xFF5E) {
            $res .= chr($code - 0xFEE0);
        } // 其他字符保持不变
        else {
            $res .= $char;
        }
    }

    return $res;
}