<?php

class Product
{
    public static function getName($code)
    {
        if (!(mb_stripos($code, 'EDR') === false))
            return 'EDR';

        if (!(mb_stripos($code, 'SIG') === false))
            return 'SIG';

        if (!(mb_strpos($code, 'GRA') === false))
            return 'GRA';

        throw new Exception("Invalid product code: $code");
    }
}