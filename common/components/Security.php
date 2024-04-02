<?php
namespace common\components;

/**
 * @link $URL: https://tarax.toyouke.com/svn/MALL/common/components/Security.php $
 * @copyright Copyright (c) 2015 Homoeopathic Educations Co. Ltd
 * @version $Id: Security.php 3668 2017-10-13 06:55:08Z kawai $
 * @author Reiko Mori <mori@homoeopathy.co.jp>
 */

class Security extends \yii\base\Security
{
    function generateRandomString($length = 32)
    {
        $seed = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789"; // exclude I,l,1,O,0
        $seedLength = strlen($seed);

        $str = [];
        for ($i = 0; $i < $length; $i++) {
            $str[] = $seed[mt_rand(0, $seedLength - 1)];
        }

        return implode($str);
    }
    
        function generateRandomNumber($length = 10)
    {
        $seed = "0123456789";
        $seedLength = strlen($seed);

        $str = [];
        for ($i = 0; $i < $length; $i++) {
            $str[] = $seed[mt_rand(0, $seedLength - 1)];
        }

        return implode($str);
    }

}