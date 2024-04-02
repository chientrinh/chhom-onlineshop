<?php

namespace common\components;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/KanaHelper.php $
 * $Id: KanaHelper.php 1074 2015-06-10 10:43:19Z mori $
 */

class KanaHelper extends \yii\base\Component
{
    function __construct()
    {
    }

    public static function split($keywords, $mode='as')
    {
        mb_language("Ja");
        mb_internal_encoding("utf-8");

        $words = mb_convert_kana($keywords, $mode);
        $words = explode(' ', $words);

        return $words;
    }

    public static function toKatakana($str)
    {
        mb_language("Ja");
        mb_internal_encoding("utf-8");

        return mb_convert_kana($str, 'KhCs');
    }

    public static function toHiragana($str)
    {
        mb_language("Ja");
        mb_internal_encoding("utf-8");

        return mb_convert_kana($str, 'Hcs');
    }

}
?>
