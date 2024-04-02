<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mtb_status".
 *
 * @property integer $status_id
 * @property string $name
 *
 * @property Purchase[] $purchases
 */
class PurchaseStatus extends \yii\db\ActiveRecord
{
    const PKEY_INIT    = 0;
    const PKEY_PAYING  = 1;
    const PKEY_DONE    = 7;
    const PKEY_CANCEL  = 8;
    const PKEY_VOID    = 9;
    const PKEY_RETURN  = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mtb_purchase_status';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'log' => [
                'class'  => ChangeLogger::className(),
                'owner'  => $this,
                'user'   => Yii::$app->has('user') ? Yii::$app->user : null,
            ],
        ];
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
            'status_id' => 'Status ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDtbPurchases()
    {
        return $this->hasMany(DtbPurchase::className(), ['status' => 'status_id']);
    }
}
