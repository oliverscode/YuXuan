<?php

class CsvReader
{
    private $fileName;

    public function __construct($fileName)
    {


        $this->fileName = $fileName;
    }

    public function read($orderBy = '')
    {
        global $cache;
        // 获取文件修改时间
        $mtime = filemtime($this->fileName);

        $key = "csv_${mtime}_${orderBy}_$this->fileName";
        $context = $cache->get($key);
        if ($context) {
            return $context;
        }

        $source = $this->readSourceFile($orderBy);
        $cache->set($key , $source);
        return $source;

    }

    private function readSourceFile($orderBy = '')
    {

        $content = file_get_contents($this->fileName);

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

                // 如果value中ascii字符占到一半以上, 就需要转成半角, 同时去掉,
                if ($this->getAsciiCount($value) > 0.5) {
                    $value = $this->fullWidthToHalfWidth($value);
                    $value = preg_replace('/,/', '', $value);
                }

                $row[$key] = $value;
            }
            $data[] = $row;
        }
        // 排序
        if (strlen($orderBy) > 0) {
            $linq = new Linq($data);
            return $linq->orderByAes(fn($x) => $x[$orderBy])->toArray();
        }

        return $data;
    }


    private function fullWidthToHalfWidth($str): string
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

    // 计算一个字符串中的ascii字符的比例
    private function getAsciiCount($str)
    {
        // 正则判断是否是ascii字符
        $pattern = '/[[:ascii:]]/';
        // 计算字符串长度
        $len = mb_strlen($str);

        // 计算ascii字符的数量
        $ascii = preg_match_all($pattern, $str);

        if ($len == 0) {
            return 0;
        }
        // 计算ascii字符的比例
        return $ascii / $len;

    }
}