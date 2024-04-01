<?php

namespace backend\models;

use Yii;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/CustomerInfoWeight.php $
 * $Id: CustomerInfoWeight.php 2737 2016-07-17 08:06:54Z mori $
 *
 * This is the model class for table "mtb_customer_info_weight".
 *
 * @property integer $weight_id
 * @property string $name
 */
class CustomerInfoWeight extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_customer_info_weight';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'weight_id' => 'Weight ID',
            'name' => 'Name',
        ];
    }
}
