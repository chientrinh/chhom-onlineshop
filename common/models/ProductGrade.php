<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dtb_product_grade".
 *
 * $URL: https://tarax.toyouke.com/svn/MALL/common/models/ProductGrade.php $
 * $Id: ProductGrade.php 2795 2020-04-20 11:55:11Z kawai $
 *
 * @property int  $product_grade_id
 * @property int  $product_id
 * @property int  $grade_id
 * @property int  $price
 * @property int  $tax
 * @property int  $tax_rate
 * @property string  $create_date
 * @property string  $expire_date
 * @property string  $update_date
 *
 * @property ProductGrades[] $productGrades
 */

class ProductGrade extends \yii\db\ActiveRecord
{

    const DATETIME_MAX = '3000-12-31 00:00:00';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dtb_product_grade';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'date'=>[
                'class' => \yii\behaviors\AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date','update_date'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['update_date'],
                ],
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                },
            ],
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
            [['product_id', 'price', 'tax', 'tax_rate'], 'required'],
            [['product_grade_id', 'product_id', 'grade_id', 'price', 'tax', 'tax_rate'], 'integer', 'min' => 0],
            [['expire_date'], 'safe'],
            ['create_date','default', 'value'=> date('Y-m-d 00:00:00') ],
            ['expire_date','default','value'=> self::DATETIME_MAX ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_grade_id'  => "ID",
            'product_id'        => "商品ID",
            'grade_id'          => "会員ランク",
            'price'             => "税別価格",
            'tax'               => "消費税",
            'tax_rate'          => "消費税率",
            'create_date'       => "作成日時",
            'expire_date'       => "終了日時",
            'update_date'       => "更新日時",
        ];
    }


    public static function getGrade($product_id, \common\models\Customer $customer = null){
        if($customer && $customer->isAgencyOf(Company::PKEY_HE))
            return ProductGrade::find()->andWhere(['product_id' => $product_id, 'grade_id' => 5])->one();

        return ProductGrade::find()->andWhere(['product_id' => $product_id, 'grade_id' => $customer ? $customer->grade_id : 0])->one();
    }
/*
    public static function find()
    {
        return new SalesCategoryQuery(get_called_class());
    }
*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductGrades($product_id)
    {
        return $this->andWhere(['product_id' => $product_id])->all();
    }

    public function getStreaming($product_id)
    {
        return Streaming::find()->andWhere(['product_id' => $product_id])->all();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerGrade()
    {
        return $this->hasOne(CustomerGrade::className(), ['grade_id' => 'grade_id']);
    }


    public function getProduct()
    {
        return $this->hasOne(ProductMaster::className(), ['product_id' => 'product_id']);
    }


}

/*
class SalesCategoryQuery extends \yii\db\ActiveQuery
{
    public function forCampaign()
    {
        return $this->andWhere(['or', 
                                    ['company_id' => 3], 
                                    ['branch_id' => [Branch::PKEY_FRONT, Branch::PKEY_ATAMI, Branch::PKEY_ROPPONMATSU, Branch::PKEY_HJ_TOKYO, Branch::PKEY_EVENT]]
                    ]);
    }

    public function wareHouse()
    {
        return $this->andWhere(['branch_id' => [Branch::PKEY_ATAMI, Branch::PKEY_ROPPONMATSU]]);
    }
}
*/
