<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "dtb_customer_info".
 *
 * @link    $URL: https://tarax.toyouke.com/svn/MALL/backend/models/CustomerInfo.php $
 * @version $Id: CustomerInfo.php 3106 2016-11-25 01:54:47Z mori $
 *
 * @property integer $customer_id
 * @property string $content
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $create_date
 * @property string $update_date
 * @property integer $weight_id
 *
 * @property MtbStaff $updatedBy
 * @property MtbStaff $createdBy
 * @property DtbCustomer $customer
 */
class CustomerInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_customer_info';
    }

    /* @inheritdoc */
    public function behaviors()
    {
        return [
            'staff_id' => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'date' => [
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date','update_date'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'update_date',
                ],
                'value' => function ($event) {
                    return new \yii\db\Expression('NOW()');
                },
            ],
            'log' => [
                'class'  => \common\models\ChangeLogger::className(),
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
            [['customer_id', 'content'], 'required'],
            [['customer_id', 'created_by', 'updated_by'], 'integer'],
            [['content'], 'string', 'length'=>[1, 1024]],
            [['weight_id'], 'default', 'value'=> CustomerInfoWeight::find()->min('weight_id') ],
            [['weight_id'], 'exist', 'targetClass'=> CustomerInfoWeight::className() ],
            [['create_date', 'update_date'], 'safe'],
            ['customer_id', 'exist', 'targetClass' => '\common\models\Customer', 'targetAttribute' => 'customer_id'],
            [['created_by','updated_by'], 'exist', 'targetClass' => '\backend\models\Staff', 'targetAttribute' => 'staff_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer'    => "顧客",
            'content'     => 'Content',
            'creator'     => "作成者",
            'updator'     => "更新者",
            'create_date' => "作成日",
            'update_date' => "更新日",
            'weight_id'   => "重み",
        ];
    }

    public function attributeHints()
    {
        return [
            'weight_id'   => "「警報」は、このお客様の注文があった場合つねに発送所スタッフが参照する仕訳伝票に記載されます",
        ];
    }

    public function beforeSave($insert)
    {
        if(defined('YII_ENV') && YII_ENV == 'test')
            return;

        if(! Yii::$app instanceof \yii\web\Application ||
             Yii::$app->user->isGuest ||
           ! Yii::$app->user->identity instanceof \backend\models\Staff)
        {
            $this->detachBehavior('staff_id');
            $this->created_by = 0; // system@toyouke.com
            $this->updated_by = 0; // system@toyouke.com
            $this->content   .= sprintf(' (%s)', Yii::$app->id);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdator()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Staff::className(), ['staff_id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeight()
    {
        return $this->hasOne(CustomerInfoWeight::className(), ['weight_id' => 'weight_id']);
    }
}
