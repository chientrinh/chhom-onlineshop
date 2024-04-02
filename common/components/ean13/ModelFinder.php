<?php

namespace common\components\ean13;
use Yii;

/**
 * ModelFinder: translate EAN13 to any models under /common/models
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/components/ean13/ModelFinder.php $
 * $Id: ModelFinder.php 2932 2016-10-07 07:39:02Z mori $
 */

class ModelFinder extends \yii\base\Model
{
    public $barcode;

    public function rules()
    {
        return [
            ['barcode','trim'],
            ['barcode','required'],
            ['barcode','string',
             'min' =>   1,
             'max' => 255,
             'when'=> function($model){return !is_numeric($model->barcode);}
            ],
            ['barcode',
             'filter',
             'filter'=> function($value){ return \common\models\Membercode::getPrefix() . $value . '0'; },
             'when'  => function($model){ return is_numeric($model->barcode) && (10 == strlen($model->barcode)); },
            ],
            ['barcode','integer',
             'min' => 1,
             'max' => pow(10,13) - 1,
             'when'=> function($model){ return is_numeric($model->barcode); },
            ],
        ];
    }

    /* @brief  get any model of known by this module
     * @return model | null
     */
    public function getOne($barcode=null)
    {
        if($barcode)
            $this->barcode = $barcode;

        if(! $this->validate())
            return null;

        if($customer = \backend\models\Customer::findByBarcode($this->barcode))
            return $customer;

        if($stock    = \common\models\RemedyStock::findByBarcode($this->barcode))
            return $stock;

        if($product  = \common\models\Product::findByBarcode($this->barcode))
            return $product;
        
        if($product = \common\models\Vegetable::findByBarcode($this->barcode))
            return $product;

        if($book     = \common\models\Book::findByBarcode($this->barcode))
            return $book->product;

        return null;
    }

}

