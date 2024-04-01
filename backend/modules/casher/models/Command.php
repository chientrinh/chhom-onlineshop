<?php

namespace backend\modules\casher\models;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/modules/casher/models/Command.php $
 * $Id: Command.php 1815 2015-11-15 12:01:05Z mori $
 */

class Command extends \yii\base\Model
{
    const ACTION_ERROR    =  0;
    const ACTION_REDUCE   =  1;
    const ACTION_DISCOUNT =  2;
    const ACTION_FINISH   =  5;
    const ACTION_RESET    =  9;

    public $code;
    public $action;
    public $seq;
    public $volume;

    public function rules()
    {
        return [
            ['code','required'],
            ['code','filter', 'filter'=> function($value) { return self::nomalizeCode($value); }, 'skipOnEmpty'=>true ],
            ['code','parse'],
            ['action','in','range'=> self::actions() ],
            ['seq',   'default', 'value' => false ],
            ['volume','default', 'value' =>     0 ],
            //['volume','integer','min'=>0,'max'=>1,'when'=>function($model){ return is_float($model->volume); },'tooBig'=>"割引率が100%を超えています"],
        ];
    }

    public static function actions()
    {
        return [
            self::ACTION_REDUCE,
            self::ACTION_DISCOUNT,
            self::ACTION_FINISH,
            self::ACTION_RESET,
        ];
    }

    /* @return string */
    public static function nomalizeCode($value)
    {
        $value = mb_convert_kana($value, 'as'); // 全角 to 半角
        $value = trim($value);
        $value = strtolower($value);

        return $value;
    }

    /* @return bool */
    public function parse()
    {
        $this->action = self::ACTION_ERROR;
        $code = $this->code;
        
        // reduce (item|all) N (per|yen)
        if(preg_match('/^r.*(i|a)[a-z]*\s*([0-9]+)\s*(p|y)/u',$code,$match))
        {
            $this->action = self::ACTION_REDUCE;
            $this->seq    = ('i' == $match[1]) ? true : false;
            $this->volume = ('p' == $match[3]) ? (float) ($match[2]/100) : (int) $match[2];
            return true;
        }

        // discount N yen : 末尾YENは 商品番号 D500 などと区別するために必須とする
        if(preg_match('/^d[a-z]*\s*([0-9]+)\s*y[e]?[n]?$/u',$code,$match))
        {
            $this->action = self::ACTION_DISCOUNT;
            $this->volume = (int) $match[1];
            return true;
        }

        if(('0' == $code) || preg_match('/^reset$/',$code))
        {
            $this->action = self::ACTION_RESET;
            return true;
        }

        if(preg_match('/^(finish|commit)$/',$code))
        {
            $this->action = self::ACTION_FINISH;
            return true;
        }

        return false;
    }
    

}
