<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dtb_storage_item".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/StorageItem.php $
 * $Id: StorageItem.php 793 2015-03-14 00:32:51Z mori $
 *
 * @property integer $storage_id
 * @property integer $product_id
 *
 * @property DtbProduct $product
 * @property DtbStorage $storage
 */
class StorageItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_storage_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storage_id', 'product_id'], 'required'],
            [['storage_id', 'product_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'storage_id' => Yii::t('app', 'Storage ID'),
            'product_id' => Yii::t('app', 'Product ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(DtbProduct::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorage()
    {
        return $this->hasOne(DtbStorage::className(), ['storage_id' => 'storage_id']);
    }
}