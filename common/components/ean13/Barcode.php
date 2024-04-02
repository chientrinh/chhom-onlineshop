<?php

namespace common\components\ean13;
use Yii;

/**
 * Barcode: represents EAN13
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/ean13/Barcode.php $
 * $Id: Barcode.php 1687 2015-10-18 15:44:27Z mori $
 */

class Barcode extends \yii\base\Model
{
    const PREFIX_LENGTH =  2;
    const BODY_LENGTH   = 12;
    const DIGIT_LENGTH  =  1;

    public  $barcode;
    private $_checkdigit;

    public function rules()
    {
        return [
            ['barcode','trim'    ],
            ['barcode','required'],
            ['barcode','integer',
             'min' => 1,
             'max' => pow(10,13) - 1,
            ],
            ['barcode','string',
             'min' =>  self::BODY_LENGTH,
             'max' => (self::BODY_LENGTH + self::DIGIT_LENGTH)
            ],
        ];
    }

    public function getCheckdigit()
    {
        if($this->hasErrors())
            return false;

        if(isset($this->_checkdigit))
            return $this->_checkdigit;

        $body = substr($this->barcode, 0, self::BODY_LENGTH);
        $this->_checkdigit = CheckDigit::generate($body);

        return $this->_checkdigit;
    }

    public function getPrefix()
    {
        return substr($this->barcode, 0, self::PREFIX_LENGTH);
    }

}
