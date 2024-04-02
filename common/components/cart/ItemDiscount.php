<?php

namespace common\components\cart;
use Yii;

/**
 * container of Discount for CartItem
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/cart/ItemDiscount.php $
 * $Id: ItemDiscount.php 1744 2015-11-01 19:07:12Z mori $
 */

class ItemDiscount extends \yii\base\Model
{
    public $rate   = 0;
    public $amount = 0;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if(100 < $this->rate){ $this->rate = 0; }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rate', 'filter',  'filter' => function($value){ return (100 < $value) ? 0 : $value; } ],
            [['amount','rate'], 'default', 'value' => 0],
            ['amount', 'integer', 'min' => 0 ],
            ['rate'  , 'integer', 'min' => 0, 'max' => 100 ],
            ['amount', 'in', 'range' => [0], 'when' => function ($model) { return (0 < $model->rate  ); } ],
            ['rate'  , 'in', 'range' => [0], 'when' => function ($model) { return (0 < $model->amount); } ],
        ];
    }

    public function getLabel()
    {
        if($this->rate)
            return sprintf("&minus;%d %%", $this->rate);

        if($this->amount)
            return sprintf("&minus;&yen; %s", number_format($this->amount));

        return null;
    }

    public function beforeValidate()
    {
        return parent::beforeValidate();
    }
}

