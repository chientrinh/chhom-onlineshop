<?php

namespace common\components;

/**
 * source: http://php.net/manual/ja/function.recode-string.php
 * contributed by Joel Yliluoma
 * copied & edited by Reiko Mori <mori@homoeopathy.co.jp> on 22 Feb 2014
 * updated         by Reiko Mori <mori@homoeopathy.co.jp> on 24 Jul 2015
 *
 * usage:
 *  $rk = new Romaji2Kana();
 *  print_r( $rk->convert('konnichiha') );
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/Romaji2Kana.php $
 * $Id: Romaji2Kana.php 2404 2016-04-09 06:42:48Z mori $
 */

// eucjp: 2421; unicode: 3041
define('HIRATABLE', 'a A i I u U e E o O KAGAKIGIKUGUKEGEKOGOSAZASIZISUZUSEZESOZO'.
       'TADATIDItuTUDUTEDETODONANINUNENOHABAPAHIBIPIHUBUPUHEBEPEHOBOPO'.
       'MAMIMUMEMOyaYAyuYUyoYORARIRUREROwaWAWIWEWOn ');
// eucjp: 2521; unicode: 30A1
define('KATATABLE', 'a A i I u U e E o O KAGAKIGIKUGUKEGEKOGOSAZASIZISUZUSEZESOZO'.
       'TADATIDItuTUDUTEDETODONANINUNENOHABAPAHIBIPIHUBUPUHEBEPEHOBOPO'.
       'MAMIMUMEMOyaYAyuYUyoYORARIRUREROwaWAWIWEWOn VUkake');
// The strings in the defines should be constant, not appendage expressions. (Line length limitation)

class Romaji2Kana extends \yii\base\Object
{

    private static function HiraTrans($s)
    {
        $pos = strpos(HIRATABLE, $s);
        if($pos===false) return 0xA1BC; // ^
        return 0xA4A1 + $pos/2;
    }

    private static function KataTrans($s)
    {
        $pos = strpos(KATATABLE, $s);
        if($pos===false) return 0xA1BC; // ^
        return 0xA5A1 + $pos/2;
    }

    /**
     * @var $text string
     * convert only lowercase alphabets which separated with whitespace, other strings will remain unchanged.
     * e.g., ' あi う e o Ka kI KU' will become 'あi う え お Ka kI KU'
     */
    public static function translate($text, $mode='hiragana')
    {
        $text = mb_convert_kana($text, 'Hcas'); // 英数字スペースは「半角」、「半／全カナ」は「全角かな」に
        $text = trim($text);
        $text = preg_replace('/ +/', ' ', $text); // truncate spaces

        $buf = [];
        foreach(explode(' ', $text) as $v)
        {
            if(! preg_match('/^[-a-z]+$/', $v))
            {
                $buf[] = $v; continue;
            }
            $kana  = self::convert($v, $mode);
            $buf[] = (0 == strlen($kana)) ? $v : $kana;
        }
        $text = implode(' ', $buf);

        return $text;
    }

    /**
     * @var $s must be alphabet only, Japanese characters will be removed
     */
    public static function convert($s, $mode='hiragana')
    {
        $s = strtoupper(str_replace(
            ['shi', 'sh', 'fu', 'chi', 'ch', 'tsu', 'ji', 'jy', 'dz', 'l', '-', 'â',  'î',  'û',  'ê',  'ô',  'ā',  'ī',  'ū',  'ē',  'ō'],
            ['si',  'sy', 'hu', 'ti',  'ty', 'tu',  'zi', 'j',  'j',  'r', '^', 'a^', 'i^', 'u^', 'e^', 'o^', 'a^', 'i^', 'u^', 'e^', 'o^'],
            $s));

        // JA -> Jya (じゃ)
        $s = preg_replace('@JY?([AUO])@e', '"ZIy".strtolower("\1")', $s);
        // JE -> ZIe (じぇ)
        $s = preg_replace('@JE@e', '"ZIe"', $s);
        // DYE -> DIe (ぢぇ)
        $s = preg_replace('@DYE@e', '"DIe"', $s);
        // DYO -> DIyo (ぢょ)
        $s = preg_replace('@DYO@e', '"DIyo"', $s);
        // FO -> FUxo (ふぉ)
        $s = preg_replace('@F([AIOE])@e', '"HU".strtolower("\1")', $s);
        // VO -> VUxo (う゛ぉ)
        $s = preg_replace('@V([AIUEO])@e', '"VU".strtolower("\1")', $s);
        // KYA -> KYya (きゃ)
        $s = preg_replace('@([KSTNHMRGJBPD])Y([AUO])@e',   '"\1Iy".strtolower("\2")', $s);
        // THA -> TEya (てゃ)
        $s = preg_replace('@TH([AUO])@e', '"TEy".strtolower("\1")', $s);
        // THI -> THi (てぃ)
        $s = preg_replace('@TH([IE])@e', '"TE".strtolower("\1")', $s);
        // XTU -> tu (make them actually small)
        $s = preg_replace('@X(TU|Y[AUO]|[AIUEO]|KA|KE)@e', 'strtolower("\1")', $s);
        // KKO -> tuKO
        $s = preg_replace('@([KSTHMRYWGZBPDV]{2,})@e',
                          'str_pad("",2*strlen("\1")-2,"tu").substr("\1",0,1)', $s);
        // N -> n (but not NO -> nO)
        // At this point, N' will work correctly
        $s = preg_replace('@N(?![AIUEO])@', 'n', $s);
        // Unrecognized characters off
        $s = eregi_replace('[^^VAIUEOKSTNHMYRWGZBPD]', '', $s);

        $pat = '@([AIUEOnaiueo^]|..)@e';

        switch ($mode)
        {
        case 'katakana':
            $kana = preg_replace($pat, 'pack("n", self::KataTrans("\1"))', $s);
            break;

        case 'hiragana':
        default:
            $kana = preg_replace($pat, 'pack("n", self::HiraTrans("\1"))', $s);
            break;
        }
        $kana = iconv('EUC-JP', 'UTF-8//TRANSLIT', $kana);

        return $kana;
    }

}
?>
