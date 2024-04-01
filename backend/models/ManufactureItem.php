<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_manufacture_item".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/ManufactureItem.php $
 * $Id: ManufactureItem.php 804 2015-03-19 07:31:58Z mori $
 *
 * @property integer $manufact_id
 * @property integer $product_id
 * @property integer $quantity
 * @property integer $batch_no
 *
 * @property DtbManufacture $manufact
 * @property DtbProduct $product
 */
class ManufactureItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_manufacture_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manufact_id', 'product_id', 'quantity', 'batch_no'], 'required'],
            [['manufact_id', 'product_id', 'quantity', 'batch_no'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'manufact_id' => 'Manufact ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'batch_no' => 'Batch No',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManufact()
    {
        return $this->hasOne(DtbManufacture::className(), ['manufact_id' => 'manufact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(DtbProduct::className(), ['product_id' => 'product_id']);
    }
}
