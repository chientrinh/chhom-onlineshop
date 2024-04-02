<?php

namespace common\components\ean13;
use Yii;

/**
 * CheckDigit for EAN13
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/ean13/CheckDigit.php $
 * $Id: CheckDigit.php 2314 2016-03-27 06:14:02Z mori $
 */

class CheckDigit extends \yii\base\Object
{
    const BODY_LENGTH  = 12;
    const DIGIT_LENGTH = 1;

    /* @brief  generate check digit for the code
     * @param  $code string, must be consists of 12 char
     * @return bool|int
     */
    public static function generate($code)
    {
        $code = (string) $code;

        if(self::BODY_LENGTH !== strlen($code))
            return false;

        if(! is_numeric($code))
            return false;

        return self::compute($code);
    }

    /* @brief  check if the last digit is accurate
     * @param  $code string, must be consists of 12 char
     * @return bool
     */
    public static function verify($code)
    {
        $code = (string) $code;

        if((self::BODY_LENGTH + self::DIGIT_LENGTH) !== strlen($code))
        return false;

        if(! is_numeric($code))
            return false;

        $target = (int) substr($code, self::BODY_LENGTH);
        $digit  = self::compute(substr($code, 0, self::BODY_LENGTH));

        return ($target == $digit);
    }

    /**
     * @link  many thanks to http://edmondscommerce.github.io/php/barcode/ean13-barcode-check-digit-with-php.html
     * @param  $digits string, must be consists of 12 char
     * @return int
     */
    private static function compute($digits)
    {
        //first change digits to a string so that we can access individual numbers
        $digits =(string)$digits;

        if(12 !== strlen($digits))
            throw new \yii\base\Exception('code must be of 12 characters');

        // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
        $next_ten = (ceil($total_sum/10))*10;
        $check_digit = $next_ten - $total_sum;

        return $check_digit;
    }
}

