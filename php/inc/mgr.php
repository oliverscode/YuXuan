<?php

class Mgr
{
    public static array $names = ['朱戍馨', '洪平良', '张娜', '狄志伟'];

    public static function getName(string $area): string
    {

        if (!mb_stripos($area, '一') === false) {
            return '朱戍馨';
        }
        if (!mb_stripos($area, '二') === false) {
            return '洪平良';
        }
        if (!mb_stripos($area, '三') === false) {
            return '张娜';
        }
        if (!mb_stripos($area, '四') === false) {
            return '狄志伟';
        }
        throw new Exception('没有找到对应的经理');
    }
}